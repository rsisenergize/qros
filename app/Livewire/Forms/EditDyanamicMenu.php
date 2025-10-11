<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use App\Models\CustomMenu;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Str;

class EditDyanamicMenu extends Component
{
    use LivewireAlert; // Include the trait

    public $editMenuName;
    public $editMenuSlug;
    public $editMenuContent;
    public $trixId;
    public $menuId;
    public $customMenuEdit;
    public $showEditDynamicMenuModal = false;
    public $editMenuPosition;

    public function mount()
    {
        $this->customMenuEdit = CustomMenu::findOrFail($this->menuId);
        $this->editMenuName = $this->customMenuEdit->menu_name;
        $this->editMenuSlug = $this->customMenuEdit->menu_slug;
        $this->editMenuContent = $this->customMenuEdit->menu_content;
        $this->editMenuPosition = $this->customMenuEdit->position;
        $this->trixId = 'trix-' . uniqid();
    }
    
    public function showEditDynamicMenu($id)
    {
        $this->menuId = $id;
        $this->showEditDynamicMenuModal = true;
    }

    public function generateSlug()
    {
        $this->editMenuSlug = Str::slug($this->editMenuName);
    }

    #[On('hideEditMenu')]
    public function hideEditMenu()
    {
        $this->showEditDynamicMenuModal = false;
        $this->js('window.location.reload()');
    }
    
    public function editDynamicMenu()
    {
        $this->validate([
            'editMenuName' => 'required',
            'editMenuSlug' => 'required',
            'editMenuContent' => 'required',
        ]);

        $this->customMenuEdit->update([
            'menu_name' => $this->editMenuName,
            'menu_slug' => $this->editMenuSlug,
            'menu_content' => $this->editMenuContent,
            'position' => $this->editMenuPosition,
        ]);

        $this->dispatch('hideEditDyanamicMenu');

        $this->alert('success', __('messages.menuUpdate'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        return view('livewire.forms.edit-dyanamic-menu');
    }

}
