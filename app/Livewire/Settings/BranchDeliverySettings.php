<?php

namespace App\Livewire\Settings;

use App\Models\Branch;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Enums\DeliveryFeeType;
use App\Models\DeliveryFeeTier;
use App\Models\BranchDeliverySetting;
use Illuminate\Validation\Rules\RequiredIf;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class BranchDeliverySettings extends Component
{
    use WithFileUploads , LivewireAlert;

    public Branch $branch;
    public BranchDeliverySetting $deliverySettings;
    public ?string $selectedFeeType = 'fixed';
    public bool $isEnabled = true;
    public float $maxRadius = 5.0;
    public ?string $unit = 'km';
    public ?float $fixedFee = null;
    public ?float $perDistanceRate = null;
    public ?float $freeDeliveryOverAmount = null;
    public ?float $freeDeliveryWithinRadius = null;
    public ?string $deliveryScheduleStart = null;
    public ?string $deliveryScheduleEnd = null;
    public int $avgDeliverySpeedKmh = 30;
    public ?int $additionalEtaBufferTime = 0;

    public array $tiers = [];

    public function mount()
    {

        $this->branch = Branch::where('id', branch()->id)->with(['deliverySetting', 'deliveryFeeTiers'])->first();

        if ($this->branch->deliverySetting) {

            $this->deliverySettings = $this->branch->deliverySetting;
            $this->isEnabled = $this->deliverySettings->is_enabled;
            $this->maxRadius = $this->deliverySettings->max_radius;
            $this->unit = $this->deliverySettings->unit;
            $this->selectedFeeType = $this->deliverySettings->fee_type->value;
            $this->fixedFee = $this->deliverySettings->fixed_fee;
            $this->perDistanceRate = $this->deliverySettings->per_distance_rate;
            $this->freeDeliveryOverAmount = $this->deliverySettings->free_delivery_over_amount;
            $this->freeDeliveryWithinRadius = $this->deliverySettings->free_delivery_within_radius;
            $this->deliveryScheduleStart = $this->deliverySettings->delivery_schedule_start;
            $this->deliveryScheduleEnd = $this->deliverySettings->delivery_schedule_end;
            $this->avgDeliverySpeedKmh = $this->deliverySettings->avg_delivery_speed_kmh;
            $this->additionalEtaBufferTime = $this->deliverySettings->additional_eta_buffer_time ?? 0;
        }

        if ($this->branch->deliveryFeeTiers->isNotEmpty()) {
            foreach ($this->branch->deliveryFeeTiers as $tier) {
                $this->tiers[] = [
                    'id' => $tier->id,
                    'min_distance' => $tier->min_distance,
                    'max_distance' => $tier->max_distance,
                    'fee' => $tier->fee
                ];
            }
        } else {
            $this->addTier();
        }
    }

    public function addTier()
    {
        $lastTier = end($this->tiers);
        $newMinDistance = $lastTier ? round($lastTier['max_distance'] + 0.1, 2) : 0;

        $this->tiers[] = [
            'id' => null,
            'min_distance' => $newMinDistance,
            'max_distance' => round($newMinDistance + 5, 2),
            'fee' => 5.00
        ];
    }

    public function removeTier($index)
    {
        if (isset($this->tiers[$index])) {
            $tier = $this->tiers[$index];

            if (isset($tier['id']) && $tier['id']) {
                DeliveryFeeTier::destroy($tier['id']);
            }

            unset($this->tiers[$index]);
            $this->tiers = array_values($this->tiers);
        }
    }

    public function save()
    {

        if ($this->branch->lat === null || $this->branch->lng === null) {
            $this->alert('error', __('messages.noBranchCoordinates'), [
                'toast' => true,
                'position' => 'top-end',
                'showConfirmButton' => false,
                'timer' => 5000
            ]);
            return;
        }

        $this->deliveryScheduleStart = $this->deliveryScheduleStart === '00:00' ? null : $this->deliveryScheduleStart;
        $this->deliveryScheduleEnd = $this->deliveryScheduleEnd === '00:00' ? null : $this->deliveryScheduleEnd;

        $this->validate([
            'maxRadius' => 'required|numeric|min:0.1',
            'unit' => 'required|in:km,miles',
            'selectedFeeType' => 'required|in:fixed,tiered,per_distance',
            'fixedFee' => 'nullable|numeric|required_if:selectedFeeType,fixed',
            'perDistanceRate' => 'nullable|numeric|required_if:selectedFeeType,per_distance',
            'freeDeliveryOverAmount' => 'nullable|numeric',
            'freeDeliveryWithinRadius' => 'nullable|numeric|lte:maxRadius',
            'avgDeliverySpeedKmh' => 'required|integer|min:1',
            'tiers.*.min_distance' => 'required_if:selectedFeeType,tiered|numeric|min:0',
            'tiers.*.max_distance' => 'required_if:selectedFeeType,tiered|numeric|gt:tiers.*.min_distance',
            'deliveryScheduleStart' => [
                'nullable',
                new RequiredIf(function () {
                    return $this->deliveryScheduleEnd !== null && $this->deliveryScheduleEnd !== '00:00';
                }),
            ],
            'deliveryScheduleEnd' => [
                'nullable',
                new RequiredIf(function () {
                    return $this->deliveryScheduleStart !== null && $this->deliveryScheduleStart !== '00:00';
                }),
            ],
            'tiers.*.fee' => 'required_if:selectedFeeType,tiered|numeric|min:0',
            'additionalEtaBufferTime' => 'nullable|integer|min:0',
        ]);
        // Custom validation for tiered distances
        if ($this->selectedFeeType === 'tiered') {
            if (empty($this->tiers)) {
            $this->addError('tiers', __('validation.tiers.required'));
            } else {
            foreach ($this->tiers as $index => $tier) {
                if ($index > 0 && $tier['min_distance'] <= $this->tiers[$index - 1]['max_distance']) {
                $this->addError("tiers.$index.min_distance", __('validation.greaterThanPrevious'));
                }
            }
            // Update maxRadius for tiered fee type
            $this->maxRadius = max(array_column($this->tiers, 'max_distance'));
            }
        }

        if ($this->getErrorBag()->isNotEmpty()) {
            return; // Stop execution if there are validation errors
        }


        BranchDeliverySetting::updateOrCreate(
            ['branch_id' => $this->branch->id],
            [
                'is_enabled' => $this->isEnabled,
                'max_radius' => $this->maxRadius,
                'unit' => $this->unit,
                'fee_type' => $this->selectedFeeType,
                'fixed_fee' => $this->fixedFee,
                'per_distance_rate' => $this->perDistanceRate,
                'free_delivery_over_amount' => $this->freeDeliveryOverAmount,
                'free_delivery_within_radius' => $this->freeDeliveryWithinRadius,
                'delivery_schedule_start' => $this->deliveryScheduleStart,
                'delivery_schedule_end' => $this->deliveryScheduleEnd,
                'avg_delivery_speed_kmh' => $this->avgDeliverySpeedKmh,
                'additional_eta_buffer_time' => $this->additionalEtaBufferTime,
            ]
        );

        // Save fee tiers if using tiered pricing
        if ($this->selectedFeeType === 'tiered') {
            foreach ($this->tiers as $tier) {
                if (isset($tier['id']) && $tier['id']) {
                    // Update existing tier
                    DeliveryFeeTier::where('id', $tier['id'])->update([
                        'min_distance' => $tier['min_distance'],
                        'max_distance' => $tier['max_distance'],
                        'fee' => $tier['fee']
                    ]);
                } else {
                    // Create new tier
                    DeliveryFeeTier::create([
                        'branch_id' => $this->branch->id,
                        'min_distance' => $tier['min_distance'],
                        'max_distance' => $tier['max_distance'],
                        'fee' => $tier['fee']
                    ]);
                }
            }
        }

        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        $feeTypes = DeliveryFeeType::labels();
        return view('livewire.settings.branch-delivery-settings', [
            'feeTypes' => $feeTypes,
        ]);
    }
}
