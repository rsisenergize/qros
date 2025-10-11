<?php

namespace App\Livewire\Shop;

use App\Models\Branch;
use Livewire\Component;
use Livewire\Attributes\Rule;
use App\Models\CustomerAddress;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Addresses extends Component
{
    use LivewireAlert;

    const MAX_ADDRESSES = 3;

    public $addresses = [];
    public $showAddressForm = false;
    public $editMode = false;
    public $selectedAddressId = null;

    #[Rule('required|string|max:50')]
    public $label = '';

    #[Rule('required|string')]
    public $address = '';

    #[Rule('required|numeric')]
    public $lat = null;

    #[Rule('required|numeric')]
    public $lng = null;

    public $confirmDeleteAddressModal = false;
    public $confirmDeleteAddressId = null;
    public $shopBranch;
    public $mapApiKey;

    public function mount()
    {
        if (!customer()) {
            abort(404);
        }

        $branch = Branch::where('id', $this->shopBranch->id)
            ->with(['deliverySetting', 'deliveryFeeTiers'])
            ->first();

        if (!$branch) {
            abort(404);
        }
        $this->mapApiKey = global_setting()->google_map_api_key ?? null;
        $this->refreshAddresses();
    }

    public function refreshAddresses()
    {
        if (customer()) {
            $this->addresses = CustomerAddress::where('customer_id', customer()->id)->get();
        }
    }

    public function createNewAddress()
    {
        $this->reset(['label', 'address', 'lat', 'lng', 'selectedAddressId']);
        $this->editMode = false;
        $this->showAddressForm = true;
        $this->dispatch('initAddressMap');
    }

    public function saveAddress()
    {
        if (!customer()) {
            $this->alert('error', __('messages.loginRequired'), [
                'toast' => true,
                'position' => 'top-end'
            ]);
            return;
        }

        $this->validate();

        if ($this->editMode) {
            $address = CustomerAddress::find($this->selectedAddressId);
            if ($address && $address->customer_id == customer()->id) {
                $address->update([
                    'label' => $this->label,
                    'address' => $this->address,
                    'lat' => $this->lat,
                    'lng' => $this->lng,
                ]);
                $this->alert('success', __('messages.addressUpdated'), [
                    'toast' => true,
                    'position' => 'top-end'
                ]);
            }
        } else {
            if ($this->hasReachedAddressLimit()) {
                $this->alert('error', __('messages.addressLimitReached'), [
                    'toast' => true,
                    'position' => 'top-end'
                ]);
                return;
            }

            CustomerAddress::create([
                'customer_id' => customer()->id,
                'label' => $this->label,
                'address' => $this->address,
                'lat' => $this->lat,
                'lng' => $this->lng,
            ]);
            $this->alert('success', __('messages.addressAdded'), [
                'toast' => true,
                'position' => 'top-end'
            ]);
        }

        $this->showAddressForm = false;
        $this->refreshAddresses();
    }

    private function hasReachedAddressLimit()
    {
        return CustomerAddress::where('customer_id', customer()->id)->count() >= self::MAX_ADDRESSES;
    }

    public function editAddress($addressId)
    {
        $address = CustomerAddress::where('id', $addressId)
            ->where('customer_id', customer()->id)
            ->first();

        if ($address) {
            $this->selectedAddressId = $address->id;
            $this->label = $address->label;
            $this->address = $address->address;
            $this->lat = $address->lat;
            $this->lng = $address->lng;
            $this->editMode = true;
            $this->showAddressForm = true;
            $this->dispatch('initAddressMap', [
                'lat' => $address->lat,
                'lng' => $address->lng
            ]);
        }
    }

    public function confirmDeleteAddress($addressId)
    {
        $this->confirmDeleteAddressId = $addressId;
        $this->confirmDeleteAddressModal = true;
    }

    public function deleteAddress()
    {
        $address = CustomerAddress::where('id', $this->confirmDeleteAddressId)
            ->where('customer_id', customer()->id)
            ->first();

        if ($address) {
            $address->delete();

            $this->confirmDeleteAddressModal = false;
            $this->confirmDeleteAddressId = null;
            $this->alert('success', __('messages.addressDeleted'), [
                'toast' => true,
                'position' => 'top-end'
            ]);
            $this->refreshAddresses();
        }
    }

    public function cancelForm()
    {
        $this->showAddressForm = false;
    }

    public function render()
    {
        return view('livewire.shop.addresses');
    }
}
