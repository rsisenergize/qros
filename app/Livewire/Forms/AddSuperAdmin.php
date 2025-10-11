<?php

namespace App\Livewire\Forms;

use App\Models\Role;
use App\Models\User;
use App\Models\Country;
use Livewire\Component;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddSuperAdmin extends Component
{
    use LivewireAlert;

    public $roles;
    public $userName;
    public $userEmail;
    public $userRole;
    public $userPassword;

    public function mount()
    {
        // Get all roles including Super Admin for superadmin management
        $this->roles = Role::all();
        $this->userRole = $this->roles->first()->name;
    }

    public function submitForm()
    {
        $this->validate([
            'userName' => 'required|string|max:255',
            'userEmail' => 'required|email|unique:users,email',
            'userPassword' => 'required|min:8',
            'userRole' => 'required|exists:roles,name'
        ]);

        // Create new user
        $user = User::create([
            'name' => $this->userName,
            'email' => $this->userEmail,
            'password' => bcrypt($this->userPassword),
            'restaurant_id' => null, // Superadmin users don't belong to restaurants
            'branch_id' => null,     // Superadmin users don't belong to branches
        ]);

        // Assign the selected role
        $user->assignRole($this->userRole);

        // Reset form
        $this->reset([
            'userName', 'userEmail', 'userRole', 'userPassword'
        ]);

        // Close modal
        $this->dispatch('hideAddMember');

        // Show success message
        $this->alert('success', __('messages.superadminUserAdded'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);

        // Refresh the user table
        $this->dispatch('refreshUsers');
    }

    public function render()
    {
        return view('livewire.forms.add-super-admin');
    }

}
