<?php

namespace App\Livewire\Forms;

use App\Models\Role;
use App\Models\User;
use App\Models\Country;
use Livewire\Component;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditSuperAdmin extends Component
{
    use LivewireAlert;

    public $roles;
    public $user;
    public $userName;
    public $userEmail;
    public $userRole;
    public $showEditModal = false;

    #[On('showEditUser')]
    public function showEditUser($userId)
    {
        $this->user = User::findOrFail($userId);
        $this->userName = $this->user->name;
        $this->userEmail = $this->user->email;
        $this->userRole = $this->user->roles->first()->name ?? '';
        $this->showEditModal = true;
    }

    public function mount()
    {
        // Get all roles including Super Admin for superadmin management
        $this->roles = Role::all();
    }

    public function updatedPhoneCodeIsOpen($value)
    {
        if (!$value) {
            $this->reset(['phoneCodeSearch']);
            $this->updatedPhoneCodeSearch();
        }
    }

    public function submitForm()
    {
        $this->validate([
            'userName' => 'required|string|max:255',
            'userEmail' => 'required|email|unique:users,email,' . $this->user->id,
            'userRole' => 'required|exists:roles,name'
        ]);

        // Update user
        $this->user->update([
            'name' => $this->userName,
            'email' => $this->userEmail,
        ]);

        // Update role if changed
        if ($this->user->roles->first()->name !== $this->userRole) {
            $this->user->syncRoles([$this->userRole]);
        }

        // Close modal
        $this->showEditModal = false;

        // Show success message
        $this->alert('success', __('messages.superadminUserUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);

        // Refresh the user table
        $this->dispatch('refreshUsers');

        // Reset form
        $this->reset([
            'userName', 'userEmail', 'userRole'
        ]);
    }

    public function closeModal()
    {
        $this->showEditModal = false;
        $this->reset([
            'userName', 'userEmail', 'userRole'
        ]);
    }

    public function render()
    {
        return view('livewire.forms.edit-super-admin');
    }

}
