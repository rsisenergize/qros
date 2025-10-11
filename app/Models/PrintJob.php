<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Restaurant;
use App\Models\Branch;
use App\Models\Printer;
use App\Traits\HasBranch;
use RuntimeException;
use App\Helper\Files;

class PrintJob extends Model
{
    use HasBranch;

    protected $guarded = ['id'];

    protected $casts   = [
        'payload' => 'json',
        'printed_at' => 'datetime'
    ];


    protected $appends = ['image_path'];

    public function getImagePathAttribute()
    {
        return asset(Files::UPLOAD_FOLDER . '/print/' . $this->image_filename);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function printer()
    {
        return $this->belongsTo(Printer::class);
    }

    /**
     * Convert an ESC/POS JSON payload to printable 80 mm HTML.
     *
     * @param  string $json  The original payload:
     *                       {"text":"\u001b@ ...","cutPaper":true}
     * @return string        Full <!DOCTYPE html> … markup
     * @throws RuntimeException on malformed input
     */
    public function getHtml($paperWidthMm = 80)
    {
        $json = $this->payload['text'] ?? '';
        $printerSetting = $this->printer;
        $paperWidthMm = $printerSetting->print_format == 'thermal80mm' ? 80 : $paperWidthMm;
        $paperWidthMm = $printerSetting->print_format == 'thermal56mm' ? 56 : $paperWidthMm;
        $paperWidthMm = $printerSetting->print_format == 'thermal112mm' ? 112 : $paperWidthMm;


        /* -------- 0. Sanitise width argument -------------------------------- */
        $paperWidthMm = (int) $paperWidthMm;
        if ($paperWidthMm <= 0) $paperWidthMm = 80;          // sensible default

        /* -------- 1. Normalise ESC chars and un-escape hex ------------------ */
        $escpos = str_replace('\e', '\x1B', $json);        // "\e" → ESC
        $raw    = stripcslashes($escpos);                    // "\x1B" → 0x1B

        /* -------- 2. Parse ESC/POS into HTML lines -------------------------- */
        $lines = [];
        $align = 'left';
        $bold  = false;
        $buf   = '';

        $flush = static function (&$buf, &$lines, $align, $bold): void {
            if ($buf === '') return;
            $cls = "line $align" . ($bold ? ' bold' : '');
            $lines[] = '<div class="' . $cls . '">' .
                htmlspecialchars($buf) . '</div>';
            $buf = '';
        };

        for ($i = 0, $len = strlen($raw); $i < $len; $i++) {
            $c = $raw[$i];
            if ($c === "\x1B") {                       // ESC
                $cmd = $raw[++$i] ?? '';
                switch ($cmd) {
                    case 'a':                          // ESC a n  – alignment
                        $flush($buf, $lines, $align, $bold);
                        $n = ord($raw[++$i] ?? "\0");
                        $align = ['left', 'center', 'right'][$n] ?? 'left';
                        break;
                    case 'E':                          // ESC E n  – bold
                        $flush($buf, $lines, $align, $bold);
                        $bold = (ord($raw[++$i] ?? "\0") === 1);
                        break;
                    case '@':                          // ESC @    – reset
                        $flush($buf, $lines, $align, $bold);
                        $align = 'left';
                        $bold  = false;
                        break;
                    default:                           // unhandled ESC/POS → skip
                        break;
                }
            } elseif ($c === "\n") {                   // newline
                $flush($buf, $lines, $align, $bold);
            } else {
                $buf .= $c;                            // printable char
            }
        }
        $flush($buf, $lines, $align, $bold);           // last line

        /* -------- 3. Build CSS with dynamic width --------------------------- */
        $css = <<<CSS
body{margin:0;padding:0;width:{$paperWidthMm}mm;font-family:'Courier New',monospace;font-size:12px}
.line{white-space:pre}
.center{text-align:center}.right{text-align:right}
.bold{font-weight:bold}
CSS;

        /* -------- 4. Assemble the HTML doc ---------------------------------- */
        return "<!DOCTYPE html>
<html lang=\"en\"><head>
<meta charset=\"utf-8\"><title>Thermal Ticket</title>
<style>{$css}</style></head><body>
" . implode("\n", $lines) . "
</body></html>";
    }
}
