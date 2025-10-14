<?php

namespace Modules\Kitchen\Livewire;

use Carbon\Carbon;
use App\Models\Kot;
use App\Models\KotItem;
use Livewire\Component;
use App\Models\KotSetting;
use App\Models\MultipleKot;
use Livewire\Attributes\On;
use Modules\Kitchen\Entities\Kitchen;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Kitchens extends Component
{

    protected $listeners = ['refreshKots' => '$refresh'];
    public $filterOrders;
    public $dateRangeType;
    public $startDate;
    public $endDate;
    public $kotPlace;
    public $pusherEnabled;
    public $pusherSettings;

    public function mount($kotPlace)
    {
        $this->kotPlace = $kotPlace;

    }

    public function render()
    {

        return view('kitchen::livewire.kitchens', [

            'kotPlace' => $this->kotPlace,

        ]);
    }

}
