<?php

namespace App\Livewire\Kot;


use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Traits\PrinterSetting;

class DirectPrint extends Component
{
    use LivewireAlert, PrinterSetting;

    public $kotId;
    protected $connector;
    protected $printer;
    protected $printerTypeLines = 48; // Default fallback
    protected $charPerLine;
    public $loading = false;
    public $kotPlaceId;

    public function mount()
    {
        $this->charPerLine = $this->printerTypeLines;
    }

    public function printKotThermal()
    {
        try {
            $this->handleKotPrint($this->kotId, $this->kotPlaceId);
            $this->alert('success', __('modules.kot.print_success'));
        }
        catch (\Exception $e) {
            $this->alert('error', __('modules.kot.print_failed', ['error' => $e->getMessage()]));
        }
    }

    public function render()
    {
        return view('livewire.kot.direct-print');
    }

}
