<?php

namespace App\Livewire\AddSuperAdmin;

use App\Models\Role;
use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class SuperAdminUserTable extends Component
{
    use LivewireAlert;
    use WithPagination;

    public $search = '';
    public $user;
    public $roles;
    public $showEditUserModal = false;
    public $confirmDeleteUserModal = false;

    protected $listeners = ['refreshUsers' => '$refresh'];

    public function mount()
    {
        $this->roles = Role::where('name', '<>', 'Super Admin')->get();
    }

    public function showEditUser($id)
    {
        // Dispatch event to the edit component
        $this->dispatch('showEditUser', $id);
    }

    #[On('hideEditUser')]
    public function hideEditUser()
    {
        $this->showEditUserModal = false;
    }

    public function showDeleteUser($id)
    {
        $this->user = User::findOrFail($id);
        $this->confirmDeleteUserModal = true;
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Don't allow superadmin to delete themselves
        if ($user->id == user()->id) {
            $this->alert('error', __('messages.cannotDeleteOwnAccount'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
            return;
        }

        $user->delete();
        $this->confirmDeleteUserModal = false;
        $this->user = null;

        $this->alert('success', __('messages.userDeleted'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function setUserRole($role, $userID)
    {
        $user = User::findOrFail($userID);

        // Don't allow superadmin to change their own role
        if ($user->id == user()->id) {
            $this->alert('error', __('messages.cannotEditOwnRole'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
            return;
        }

        $user->syncRoles([$role]);

        $this->alert('success', __('messages.userRoleUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        $query = User::whereNull('restaurant_id')
            ->where(function ($q) {
                return $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        return view('livewire.add-super-admin.super-admin-user-table', [
            'users' => $query
        ]);
    }
}
