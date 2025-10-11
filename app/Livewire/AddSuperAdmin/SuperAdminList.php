<?php

namespace App\Livewire\AddSuperAdmin;

use App\Exports\StaffExport;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Maatwebsite\Excel\Facades\Excel;

class SuperAdminList extends Component
{
    use WithPagination;

    public $search = '';
    public $showAddMember = false;

    #[On('hideAddMember')]
    public function hideAddMember()
    {
        $this->showAddMember = false;
    }

    public function exportStaffList()
    {
        if (!in_array('Export Report', restaurant_modules())) {
            $this->dispatch('showUpgradeLicense');
        } else {
            return Excel::download(new StaffExport, 'superadmin-users-' . now()->toDateTimeString() . '.xlsx');
        }
    }

    public function render()
    {
        return view('livewire.add-super-admin.super-admin-list');
    }
}
