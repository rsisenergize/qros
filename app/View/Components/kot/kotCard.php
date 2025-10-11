<?php

namespace App\View\Components\kot;

use Closure;
use Illuminate\View\Component;
use App\Models\KotCancelReason;
use Illuminate\Contracts\View\View;

class kotCard extends Component
{
    public $kot;
    public $kotSettings;
    public $printer;
    public $cancelReasons;
    public $showAllKitchens;

    /**
     * Create a new component instance.
     */

    //
    public function __construct($kot, $kotSettings, $cancelReasons = null, $showAllKitchens = false)
    {
        $this->kot = $kot;
        $this->kotSettings = $kotSettings;
        $this->cancelReasons = $cancelReasons;
        $this->showAllKitchens = $showAllKitchens;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {

        return view('components.kot.kot-card');
    }
}
