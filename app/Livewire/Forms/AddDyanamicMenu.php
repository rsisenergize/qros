<?php

namespace App\Livewire\Forms;

use App\Models\CustomMenu;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Str;

class AddDyanamicMenu extends Component
{
    use LivewireAlert;

    public $menuName;
    public $menuSlug;
    public $menuContent;
    public $position = 'header';

    public function generateSlug()
    {
        $this->menuSlug = Str::slug($this->menuName);
    }

    public function submitDynamicWebPageForm()
    {
        $this->validate([
        'menuName' => 'required|string|max:255|unique:custom_menus,menu_name',
        'menuSlug' => 'required|string|max:255|unique:custom_menus,menu_slug',
        'menuContent' => 'required|string',
        ]);

        CustomMenu::create([
            'menu_name' => $this->menuName,
            'menu_slug' => $this->menuSlug,
            'menu_content' => $this->menuContent,
            'position' => $this->position,
        ]);

        $this->reset();
        $this->dispatch('reset-trix-editor');
        $this->dispatch('hideAddDyanamicMenu');

        $this->alert('success', __('messages.settingsUpdated'), [
        'toast' => true,
        'position' => 'top-end',
        'showCancelButton' => false,
        'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        return view('livewire.forms.add-dyanamic-menu');
    }

}
