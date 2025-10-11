<?php

namespace App\Livewire\Forms;

use App\Models\Role;
use App\Models\User;
use App\Models\Country;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\Hash;

class EditStaff extends Component
{
    use LivewireAlert;

    public $member;
    public $roles;
    public $memberName;
    public $memberEmail;
    public $memberRole;
    public $restaurantPhoneNumber;
    public $restaurantPhoneCode;
    public $phoneCodeSearch = '';
    public $phoneCodeIsOpen = false;
    public $allPhoneCodes;
    public $filteredPhoneCodes;
    public $password;

    public function mount()
    {
        $this->roles = Role::where('display_name', '<>', 'Super Admin')->get();
        $this->memberName = $this->member->name;
        $this->memberEmail = $this->member->email;
        $this->restaurantPhoneNumber = $this->member->phone_number;
        $this->restaurantPhoneCode = $this->member->phone_code;
        $this->memberRole = $this->member->roles->pluck('name')[0] ?? null;
        $this->password = ''; // Start with empty password field

        // Initialize phone codes
        $this->allPhoneCodes = collect(Country::pluck('phonecode')->unique()->filter()->values());
        $this->filteredPhoneCodes = $this->allPhoneCodes;
    }

    public function updatedPhoneCodeIsOpen($value)
    {
        if (!$value) {
            $this->reset(['phoneCodeSearch']);
            $this->updatedPhoneCodeSearch();
        }
    }

    public function updatedPhoneCodeSearch()
    {
        $this->filteredPhoneCodes = $this->allPhoneCodes->filter(function ($phonecode) {
            return str_contains($phonecode, $this->phoneCodeSearch);
        })->values();
    }

    public function selectPhoneCode($phonecode)
    {
        $this->restaurantPhoneCode = $phonecode;
        $this->phoneCodeIsOpen = false;
        $this->phoneCodeSearch = '';
        $this->updatedPhoneCodeSearch();
    }

    public function submitForm()
    {
        $this->validate([
            'memberName' => 'required',
            'memberEmail' => 'required|unique:users,email,' . $this->member->id,
            'restaurantPhoneNumber' => [
                'required',
                'regex:/^[0-9\s]{8,20}$/',
            ],
            'restaurantPhoneCode' => 'required',
        ]);

        $user = User::withoutGlobalScopes()->where('restaurant_id', restaurant()->id)->find($this->member->id);
        $user->name = $this->memberName;
        $user->email = $this->memberEmail;
        $user->phone_number = $this->restaurantPhoneNumber;
        $user->phone_code = $this->restaurantPhoneCode;
        if (!empty($this->password)) {
            $user->password = bcrypt($this->password);
        }



        $user->save();

        $user->syncRoles([$this->memberRole]);

        // Reset the value
        $this->memberName = '';
        $this->memberEmail = '';
        $this->memberRole = '';
        $this->password = '';

        $this->dispatch('hideEditStaff');

        $this->alert('success', __('messages.memberUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        return view('livewire.forms.edit-staff', [
            'phonecodes' => $this->filteredPhoneCodes,
        ]);
    }

}
