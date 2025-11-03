<?php

namespace App\Livewire\Settings;

use App\Helper\Files;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\DeliveryPlatform;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class DeliveryApps extends Component
{
    use LivewireAlert, WithFileUploads;

    public $settings;
    public $deliveryPlatforms;

    // Form fields for new/edit platform
    public $name = '';
    public $logo;
    public $commissionType = 'percent';
    public $commissionValue = '';
    public $isActive = true;
    public $editingIndex = null;
    public $logoUrl = null;
    public $hasNewPhoto = false;

    // Modal states
    public $showAddForm = false;
    public $confirmDeleteModal = false;
    public $deletingPlatformId = null;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:delivery_platforms,name,' . ($this->editingIndex ?: 'NULL') . ',id,branch_id,' . (branch()->id ?? 'NULL'),
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'commissionType' => 'required|in:percent,fixed',
            'commissionValue' => 'required|numeric|min:0|max:' . ($this->commissionType === 'percent' ? '100' : '999999.99'),
            'isActive' => 'boolean',
        ];
    }

    protected $messages = [
        'name.required' => 'Platform name is required',
        'name.unique' => 'A platform with this name already exists for this branch',
        'commissionValue.required' => 'Commission value is required',
        'commissionValue.numeric' => 'Commission value must be a number',
        'commissionValue.min' => 'Commission value cannot be negative',
        'commissionValue.max' => 'Commission value cannot exceed 100% for percentage or 999999.99 for fixed amount',
        'logo.image' => 'Logo must be an image file',
        'logo.mimes' => 'Logo must be a file of type: jpeg, png, jpg, gif, webp',
        'logo.max' => 'Logo size cannot exceed 2MB',
    ];

    public function mount()
    {
        $this->fetchDeliveryPlatforms();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function fetchDeliveryPlatforms()
    {
        $this->deliveryPlatforms = DeliveryPlatform::orderBy('created_at', 'desc')->get();
    }

    public function openAddForm()
    {
        $this->resetForm();
        $this->showAddForm = true;
    }

    public function hideAddForm()
    {
        $this->showAddForm = false;
        $this->resetForm();
    }

    public function editPlatform($platformId)
    {
        // Skip edit for demo data
        if (str_starts_with($platformId, 'demo-')) {
            $this->alert('info', 'This is demo data. Create a branch to manage real delivery platforms.', [
                'position' => 'top-end',
                'toast' => true,
            ]);
            return;
        }

        $platform = DeliveryPlatform::find($platformId);

        if (!$platform) {
            $this->alert('error', 'Platform not found.', [
                'position' => 'top-end',
                'toast' => true,
            ]);
            return;
        }

        // Check if platform belongs to current branch
        if ($platform->branch_id !== branch()->id) {
            $this->alert('error', 'Unauthorized action.', [
                'position' => 'top-end',
                'toast' => true,
            ]);
            return;
        }

        $this->editingIndex = $platformId;
        $this->name = $platform->name;
        $this->commissionType = $platform->commission_type;
        $this->commissionValue = $platform->commission_value;
        $this->isActive = $platform->is_active;
        $this->logo = $platform->logo;
        $this->logoUrl =  $platform->logo ? $platform->logo_url : null ;
        $this->showAddForm = true;
    }

    public function savePlatform()
    {
        $this->validate();

        $branchId = branch()->id ?? null;

        if (!$branchId) {
            $this->alert('error', 'No branch found. Please create a branch first.', [
                'position' => 'top-end',
                'toast' => true,
            ]);
            return;
        }

        $logoPath = null;

        // Handle logo upload
        if ($this->logo) {
            try {
                $logoPath = Files::uploadLocalOrS3($this->logo, 'delivery-apps-logo', null, height: 350);
            } catch (\Exception $e) {
                $this->alert('error', $e, [
                    'position' => 'top-end',
                    'toast' => true,
                ]);
                return;
            }
        }

        $platformData = [
            'branch_id' => $branchId,
            'name' => trim($this->name),
            'commission_type' => $this->commissionType,
            'commission_value' => $this->commissionValue,
            'is_active' => $this->isActive,
        ];

        if ($logoPath) {
            $platformData['logo'] = $logoPath;
        }

        try {
            if ($this->editingIndex !== null && !str_starts_with($this->editingIndex, 'demo-')) {
                // Update existing platform
                $platform = DeliveryPlatform::find($this->editingIndex);

                if (!$platform) {
                    $this->alert('error', 'Platform not found.', [
                        'position' => 'top-end',
                        'toast' => true,
                    ]);
                    return;
                }

                // Delete old logo if new one is uploaded
                if ($logoPath && $platform->logo) {
                    Files::deleteFile($platform->logo, 'delivery-apps-logo');
                }

                $platform->update($platformData);
            } else {
                // Create new platform
                DeliveryPlatform::create($platformData);
            }

            $this->fetchDeliveryPlatforms();
            $this->hideAddForm();

            $message = $this->editingIndex !== null
                ? __('messages.deliveryPlatformUpdated')
                : __('messages.deliveryPlatformAdded');

            $this->alert('success', $message, [
                'position' => 'top-end',
                'toast' => true,
            ]);
        } catch (\Exception $e) {
            // Clean up uploaded file if database operation fails
            if ($logoPath) {
                Files::deleteFile($logoPath, 'delivery-apps-logo');
            }

            $this->alert('error', 'Failed to save platform. Please try again.', [
                'position' => 'top-end',
                'toast' => true,
            ]);
        }
    }

    public function confirmDelete($platformId)
    {
        $this->deletingPlatformId = $platformId;
        $this->confirmDeleteModal = true;
    }

    public function deletePlatform()
    {
        if ($this->deletingPlatformId !== null) {
            // Skip deletion for demo data
            if (str_starts_with($this->deletingPlatformId, 'demo-')) {
                $this->alert('info', __('messages.demoDataCannotBeDeleted'), [
                    'position' => 'top-end',
                    'toast' => true,
                ]);
                $this->confirmDeleteModal = false;
                $this->deletingPlatformId = null;
                return;
            }

            $platform = DeliveryPlatform::find($this->deletingPlatformId);

            if (!$platform) {
                $this->alert('error', __('messages.platformNotFound'), [
                    'position' => 'top-end',
                    'toast' => true,
                ]);
                $this->confirmDeleteModal = false;
                $this->deletingPlatformId = null;
                return;
            }

            // Check if platform belongs to current branch
            if ($platform->branch_id !== branch()->id) {
                $this->alert('error', 'Unauthorized action.', [
                    'position' => 'top-end',
                    'toast' => true,
                ]);
                $this->confirmDeleteModal = false;
                $this->deletingPlatformId = null;
                return;
            }

            try {
                // Delete logo file if exists
                if ($platform->logo) {
                    Storage::disk('public')->delete($platform->logo);
                }

                $platform->delete();

                $this->fetchDeliveryPlatforms();
                $this->confirmDeleteModal = false;
                $this->deletingPlatformId = null;

                $this->alert('success', __('messages.deliveryPlatformDeleted'), [
                    'position' => 'top-end',
                    'toast' => true,
                ]);
            } catch (\Exception $e) {
                $this->alert('error', 'Failed to delete platform.', [
                    'position' => 'top-end',
                    'toast' => true,
                ]);

                $this->confirmDeleteModal = false;
                $this->deletingPlatformId = null;
            }
        }
    }

    public function togglePlatformStatus($platformId)
    {
        // Skip for demo data
        if (str_starts_with($platformId, 'demo-')) {
            // Find the platform in the collection and toggle its status
            $this->deliveryPlatforms = $this->deliveryPlatforms->map(function ($platform) use ($platformId) {
                if ($platform->id === $platformId) {
                    $platform->is_active = !$platform->is_active;
                }
                return $platform;
            });

            $platform = $this->deliveryPlatforms->firstWhere('id', $platformId);
            $status = $platform->is_active ? __('messages.activated') : __('messages.deactivated');
            $this->alert('success', __('messages.deliveryPlatformStatusUpdated', ['status' => $status]), [
                'position' => 'top-end',
                'toast' => true,
            ]);
            return;
        }

        $platform = DeliveryPlatform::find($platformId);

        if (!$platform) {
            $this->alert('error', __('messages.platformNotFound'), [
                'position' => 'top-end',
                'toast' => true,
            ]);
            return;
        }

        // Check if platform belongs to current branch
        if ($platform->branch_id !== branch()->id) {
            $this->alert('error', 'Unauthorized action.', [
                'position' => 'top-end',
                'toast' => true,
            ]);
            return;
        }

        try {
            $platform->update(['is_active' => !$platform->is_active]);

            // Update the local collection
            $this->deliveryPlatforms = $this->deliveryPlatforms->map(function ($p) use ($platform) {
                if ($p->id === $platform->id) {
                    $p->is_active = $platform->is_active;
                }
                return $p;
            });

            $status = $platform->is_active ? __('messages.activated') : __('messages.deactivated');
            $this->alert('success', __('messages.deliveryPlatformStatusUpdated', ['status' => $status]), [
                'position' => 'top-end',
                'toast' => true,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Failed to update platform status.', [
                'position' => 'top-end',
                'toast' => true,
            ]);
        }
    }

    public function removeLogo()
    {
        if ($this->editingIndex !== null && !str_starts_with($this->editingIndex, 'demo-')) {
            // If editing an existing platform, delete the logo from storage and database
            $platform = DeliveryPlatform::find($this->editingIndex);

            if ($platform && $platform->logo) {
                try {
                    // Delete logo file from storage
                    Storage::disk('public')->delete($platform->logo);

                    // Update platform to remove logo reference
                    $platform->update(['logo' => null]);

                    $this->alert('success', __('messages.logoRemoved'), [
                        'position' => 'top-end',
                        'toast' => true,
                    ]);

                    // Reset logo in form
                    $this->logo = null;
                    $this->logoUrl = null;

                    // Refresh the delivery platforms list
                    $this->fetchDeliveryPlatforms();
                } catch (\Exception $e) {
                    $this->alert('error', 'Failed to remove logo. Please try again.', [
                        'position' => 'top-end',
                        'toast' => true,
                    ]);
                }
            }
        } else {
            // If adding a new platform, just clear the temporary logo
            $this->logo = null;
            $this->logoUrl = null;
        }
    }

    public function getLogoUrl($logoPath)
    {
        if (!$logoPath) {
            return asset('img/file-icon.png'); // Fallback logo
        }

        return Storage::url($logoPath);
    }

    private function resetForm()
    {
        $this->reset(['name', 'logo', 'commissionType', 'commissionValue', 'isActive', 'editingIndex', 'logoUrl', 'hasNewPhoto']);
        $this->commissionType = 'percent';
        $this->isActive = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.settings.delivery-apps');
    }
}
