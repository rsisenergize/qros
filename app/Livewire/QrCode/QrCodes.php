<?php

namespace App\Livewire\QrCode;

use App\Helper\Files;
use App\Models\Area;
use Livewire\Component;
use App\Models\Table;
use App\Models\FileStorage;

class QrCodes extends Component
{

    public $areaID = null;

    public function downloadQrCode($tableCode, $branchId)
    {
        $filename = 'qrcode-' . $branchId . '-' . str()->slug($tableCode, '-', (auth()->user() ? auth()->user()->locale : 'en')) . '.png';

        $file = FileStorage::where('filename', $filename)->first();

        return download_local_s3($file, 'qrcodes/' . $filename);
    }

    public function downloadBranchQrCode()
    {
        $branch = branch();

        $filename = 'qrcode-branch-' . $branch->id . '-' . $branch->restaurant->id . '.png';

        $file = FileStorage::where('filename', $filename)->first();

        return download_local_s3($file, 'qrcodes/' . $filename);
    }

    public function generateQrCode($tableId = null)
    {
        if ($tableId) {
            $table = Table::find($tableId);
        } else {
            $table = branch();
        }

        $table->generateQrCode();

        $this->redirect(route('qrcodes.index'));
    }

    public function render()
    {
        $query = Area::with('tables');

        if (!is_null($this->areaID)) {
            $query = $query->where('id', $this->areaID);
        }

        $query = $query->get();

        return view('livewire.qr-code.qr-codes', [
            'tables' => $query,
            'areas' => Area::get()
        ]);
    }
}
