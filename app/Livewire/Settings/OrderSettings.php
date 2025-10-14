<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\OrderNumberSetting;
use App\Models\Branch;
use App\Models\OrderType;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class OrderSettings extends Component
{
    use LivewireAlert;

    public $activeTab;
    public $branchId;
    public $enableFeature = false;
    public $prefix = 'Order';
    public $digits = 3;
    public $separator = '-';
    public $includeDate = false;
    public $showYear = false;
    public $showMonth = false;
    public $showDay = false;
    public $showTime = false;
    public $resetDaily = false;
    public $hideMenuItemImageOnPos = false;
    public $hideMenuItemImageOnCustomerSite = false;
    public $settings;
    public $tokenSettings = [];

    public function mount()
    {
        $this->branchId = branch()->id ?? null;

        if (!$this->branchId) {
            throw new \Exception('No branch found. Please ensure at least one branch exists.');
        }

        $this->loadSettings($this->branchId);
        $this->loadTokenSettings();
        $this->activeTab = 'prefix';

        $this->hideMenuItemImageOnPos = (bool) restaurant()->hide_menu_item_image_on_pos ?? false;
        $this->hideMenuItemImageOnCustomerSite = (bool) restaurant()->hide_menu_item_image_on_customer_site ?? false;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function saveOrderSettings()
    {
        // Automatically enable includeDate if any date part is enabled
        if ($this->showYear || $this->showMonth || $this->showDay || $this->showTime) {
            $this->includeDate = true;
        } else {
            $this->includeDate = false;
        }

        $this->validate([
            'branchId' => 'required|exists:branches,id',
            'prefix' => 'required|string|max:50',
            'digits' => 'required|integer|min:1|max:10',
            'separator' => ['required', 'string', 'size:1', 'regex:/[^a-zA-Z0-9]/'],
        ]);

        OrderNumberSetting::updateOrCreate(
            ['branch_id' => $this->branchId],
            [
                'enable_feature' => $this->enableFeature,
                'prefix' => $this->prefix,
                'digits' => $this->digits,
                'separator' => $this->separator,
                'include_date' => $this->includeDate,
                'show_year' => $this->showYear,
                'show_month' => $this->showMonth,
                'show_day' => $this->showDay,
                'show_time' => $this->showTime,
                'reset_daily' => $this->resetDaily,
            ]
        );

        $this->alert('success', __('messages.settingsUpdated'), [
            'position' => 'top-end',
            'toast' => true,
        ]);
    }

    public function saveMenuItemImageSettings()
    {
        $this->validate([
            'branchId' => 'required|exists:branches,id',
            'hideMenuItemImageOnPos' => 'boolean',
            'hideMenuItemImageOnCustomerSite' => 'boolean',
        ]);

        // Get the current restaurant for this branch
        $branch = Branch::find($this->branchId);
        if ($branch && $branch->restaurant) {
            $branch->restaurant->update([
                'hide_menu_item_image_on_pos' => $this->hideMenuItemImageOnPos,
                'hide_menu_item_image_on_customer_site' => $this->hideMenuItemImageOnCustomerSite,
            ]);
        }


        session()->forget('restaurant');

        $this->alert('success', __('messages.settingsUpdated'), [
            'position' => 'top-end',
            'toast' => true,
        ]);
    }

    public function getPreviewProperty()
    {
        // Automatically enable includeDate if any date part is enabled
        $includeDate = ($this->showYear || $this->showMonth || $this->showDay || $this->showTime);

        if (!$this->enableFeature) {
            return __('modules.order.customPrefixFeatureDisabled');
        }

        $prefix = $this->prefix ?: 'ORD';
        $seq = str_pad('1', (int) $this->digits, '0', STR_PAD_LEFT);
        $sep = $this->separator ?: '-';

        $dateParts = [];
        if ($includeDate) {
            if ($this->showYear) {
                $dateParts[] = now()->format('Y');
            }
            if ($this->showMonth) {
                $dateParts[] = now()->format('m');
            }
            if ($this->showDay) {
                $dateParts[] = now()->format('d');
            }
            if ($this->showTime) {
                $dateParts[] = now()->format('Hi');
            }
        }

        $parts = array_filter([
            $prefix,
            $dateParts ? implode('', $dateParts) : null,
            $seq
        ]);

        return implode($sep, $parts);
    }

    public function getBranchesProperty()
    {
        return Branch::select('id', 'name')->get();
    }

    protected function loadSettings()
    {
        $settings = OrderNumberSetting::where('branch_id', $this->branchId)->first();

        if ($settings) {
            $this->enableFeature = $settings->enable_feature;
            $this->prefix = $settings->prefix;
            $this->digits = $settings->digits;
            $this->separator = $settings->separator;
            $this->includeDate = $settings->include_date;
            $this->showYear = $settings->show_year;
            $this->showMonth = $settings->show_month;
            $this->showDay = $settings->show_day;
            $this->showTime = $settings->show_time;
            $this->resetDaily = $settings->reset_daily;
        } else {
            $this->enableFeature = false;
            $this->prefix = 'ORD';
            $this->digits = 3;
            $this->separator = '-';
            $this->includeDate = false;
            $this->showYear = false;
            $this->showMonth = false;
            $this->showDay = false;
            $this->showTime = false;
            $this->resetDaily = false;
        }

        // Load menu image settings from restaurant
        $branch = Branch::find($this->branchId);
        if ($branch && $branch->restaurant) {
            $this->hideMenuItemImageOnPos = $branch->restaurant->hide_menu_item_image_on_pos ?? false;
            $this->hideMenuItemImageOnCustomerSite = $branch->restaurant->hide_menu_item_image_on_customer_site ?? false;
        }
    }

    protected function loadTokenSettings(): void
    {
        $this->tokenSettings = [];
        if (!$this->branchId) {
            return;
        }

        $orderTypes = OrderType::where('branch_id', $this->branchId)->get(['id', 'enable_token_number', 'order_type_name']);
        foreach ($orderTypes as $type) {
            $this->tokenSettings[$type->id] = (bool) $type->enable_token_number;
        }
    }

    public function saveTokenSettings(): void
    {
        $this->validate([
            'branchId' => 'required|exists:branches,id',
            'tokenSettings' => 'array',
        ]);

        $orderTypes = OrderType::where('branch_id', $this->branchId)->pluck('id');

        foreach ($orderTypes as $typeId) {
            $enabled = (bool) ($this->tokenSettings[$typeId] ?? false);
            OrderType::where('id', $typeId)->update(['enable_token_number' => $enabled]);
        }

        $this->alert('success', __('messages.settingsUpdated'), [
            'position' => 'top-end',
            'toast' => true,
        ]);
    }

    public function getOrderTypesProperty()
    {
        return OrderType::where('branch_id', $this->branchId)
            ->select('id', 'order_type_name', 'enable_token_number')
            ->orderBy('id')
            ->get();
    }

    public function render()
    {
        return view('livewire.settings.order-settings');
    }
}
