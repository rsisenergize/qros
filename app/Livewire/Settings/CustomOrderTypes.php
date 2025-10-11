<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\OrderType;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class CustomOrderTypes extends Component
{
    use LivewireAlert;

    public $settings;
    public $allowCustomOrderTypeOptions = false;
    public $orderTypeFields = [];
    public $confirmDeleteOrderTypeModal = false;
    public $fieldId = null;
    public $fieldIndex = null;
    public $orderTypeOptions = [];

    protected function rules()
    {
        return [
            'allowCustomOrderTypeOptions' => 'boolean',
            'orderTypeFields.*.orderTypeName' => 'required',
            'orderTypeFields.*.type' => 'required|in:dine_in,delivery,pickup',
        ];
    }

    public function mount()
    {
        $this->allowCustomOrderTypeOptions = (bool)($this->settings->show_order_type_options ?? false);
        
        // Initialize order type options with proper translations
        $this->orderTypeOptions = [
            'dine_in' => __('modules.settings.dineIn'),
            'delivery' => __('modules.settings.delivery'),
            'pickup' => __('modules.settings.pickup')
        ];

        $this->fetchData();

        if (empty($this->orderTypeFields)) {
            $this->addMoreOrderTypeFields();
        }
    }

    public function fetchData()
    {
        $orderTypes = OrderType::where('branch_id', branch()->id)->get();

        $this->orderTypeFields = $orderTypes->map(function ($orderType) {
            return [
                'id' => $orderType->id,
                'orderTypeName' => $orderType->order_type_name,
                'enabled' => (bool)$orderType->is_active,
                'type' => $orderType->type ?? '',
                'isDefault' => $orderType->is_default,
            ];
        })->toArray();
    }

    public function addMoreOrderTypeFields()
    {
        $this->orderTypeFields[] = [
            'id' => null,
            'orderTypeName' => '',
            'enabled' => true,
            'type' => '',
            'isDefault' => false,
        ];
    }
    

    public function showConfirmationOrderTypeField($index, $id = null)
    {
        // Don't delete default order types
        if (isset($this->orderTypeFields[$index]['isDefault']) && $this->orderTypeFields[$index]['isDefault']) {
            $this->alert('error', __('modules.settings.cannotDeleteDefaultOrderType'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
            ]);
            return;
        }
        
        if (is_null($id)) {
            $this->removeOrderTypeField($index);
        } else {
            $this->confirmDeleteOrderTypeModal = true;
            $this->fieldId = $id;
            $this->fieldIndex = $index;
        }
    }

    public function deleteAndRemove($id, $index)
    {
        if ($id) {
            OrderType::destroy($id);
        }
        
        $this->removeOrderTypeField($index);
        $this->reset(['fieldId', 'fieldIndex', 'confirmDeleteOrderTypeModal']);
        $this->alert('success', __('messages.orderTypeDeleted'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
        ]);
    }

    public function removeOrderTypeField($index)
    {
        if (isset($this->orderTypeFields[$index])) {
            unset($this->orderTypeFields[$index]);
            $this->orderTypeFields = array_values($this->orderTypeFields);
        }
    }

    public function saveOrderTypes()
    {
        // Filter out empty fields
        $this->orderTypeFields = array_values(array_filter($this->orderTypeFields, function ($field) {
            return !empty($field['orderTypeName']);
        }));

        $messages = [
            'orderTypeFields.*.orderTypeName.required' => __('validation.orderTypeNameRequired'),
            'orderTypeFields.*.type.required' => __('validation.orderTypeRequired'),
            'orderTypeFields.*.type.in' => __('validation.invalidOrderType'),
        ];

        $this->validate($this->rules(), $messages);

        $branchId = branch()->id;
        
        foreach ($this->orderTypeFields as $field) {
            OrderType::updateOrCreate(
                ['id' => $field['id']],
                [
                    'branch_id' => $branchId,
                    'order_type_name' => $field['orderTypeName'],
                    'is_active' => $field['enabled'],
                    'type' => $field['type'],
                    'slug' => Str::slug($field['orderTypeName'], '_'),
                ]
            );
        }

        $this->fetchData();
        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
        ]);
    }

    public function updatedAllowCustomOrderTypeOptions($value)
    {
        if (!$value) {
            $this->orderTypeFields = [];
        } elseif (empty($this->orderTypeFields)) {
            $this->fetchData();
        }

        // Update the settings
        $this->settings->show_order_type_options = $value;
        $this->settings->save();

        session()->forget('restaurant');
        
        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
        ]);
    }

    public function render()
    {
        return view('livewire.settings.custom-order-types');
    }
}
