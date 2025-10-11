<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CustomMenu;

class NewCustomPages extends Component
{
    public $slug;

    public function render()
    {
        $customMenu = CustomMenu::where('menu_slug', $this->slug)->where('is_active', 1)->firstOrFail();


        return view('livewire.new-custom-pages', [
            'customMenu' => $customMenu,
        ]);
    }

}
