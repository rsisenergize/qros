<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Kot;
use App\Http\Controllers\KotController;
use App\Models\Printer;
use App\Helper\Files;

class ViewPngController extends Controller
{
    // Render your Blade view (with data) inside a capture wrapper

    // Preview KOT for image capture
    public function previewKot($id, $kotPlaceid = null)
    {
        $kot = Kot::with('items', 'order.waiter', 'table')->findOrFail($id);
        $kotPlaceid = $kotPlaceid ?? 1;

        $printerSetting = $kot->kotPlace?->printerSetting;

        $content = (new KotController($id))->printKot($id, $kotPlaceid, Printer::getPrintWidth($printerSetting), true)->render();

        return view('capture-kot', compact('content', 'kot'));
    }

    // Save KOT image specifically
    public function storeKot(Request $request)
    {
        $data = $request->validate([
            'image_base64' => 'required|string',   // data:image/png;base64,...
            'kot_id'       => 'required|integer',  // KOT ID
            'width'        => 'nullable|integer',  // final width (px) e.g. 512 or 384
            'mono'         => 'nullable|boolean',  // pure B/W for thermal
        ]);

        return $this->storeImage($data, 'kot', $data['kot_id']);
    }

    // Save Order image specifically
    public function storeOrder(Request $request)
    {
        $data = $request->validate([
            'image_base64' => 'required|string',   // data:image/png;base64,...
            'order_id'     => 'required|integer',  // Order ID
            'width'        => 'nullable|integer',  // final width (px) e.g. 512 or 384
            'mono'         => 'nullable|boolean',  // pure B/W for thermal
        ]);

        return $this->storeImage($data, 'order', $data['order_id']);
    }

    // Save Cash Register report image (X/Z reports)
    public function storeReport(Request $request)
    {
        $data = $request->validate([
            'image_base64' => 'required|string',   // data:image/png;base64,
            'session_id'   => 'required|integer',  // Cash register session ID
            'report_type'  => 'required|string',   // x-report | z-report
            'width'        => 'nullable|integer',  // final width (px)
            'mono'         => 'nullable|boolean',  // pure B/W for thermal
        ]);

        $type = strtolower($data['report_type']) === 'z-report' ? 'z-report' : 'x-report';
        return $this->storeImage($data, $type, $data['session_id']);
    }

    /**
     * Common method to store images (KOT or Order)
     *
     * @param array $data
     * @param string $type
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    private function storeImage(array $data, string $type, int $id)
    {
        try {
            // Process base64 image data
            $binary = $this->processBase64Image($data['image_base64']);

            if ($binary === false) {
                return response()->json(['ok' => false, 'message' => 'Invalid base64'], 422);
            }

            // Create and process image
            $img = $this->createImage($binary, $data['mono'] ?? false);


            // Store image file
            $path = $this->storeImageFile($img, $type, $id);


            return response()->json([
                'ok'   => true,
                'url'  => $this->getImageUrl($path),
                'path' => $path,
                'w'    => $img->width(),
                'h'    => $img->height(),
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'ok' => false,
                'message' => 'Failed to process image'
            ], 500);
        }
    }

    /**
     * Process base64 image data
     *
     * @param string $imageBase64
     * @return string|false
     */
    private function processBase64Image(string $imageBase64)
    {
        // Strip prefix if it's a data URL
        $base64 = str_starts_with($imageBase64, 'data:image/')
            ? substr($imageBase64, strpos($imageBase64, ',') + 1)
            : $imageBase64;

        return base64_decode($base64, true);
    }

    /**
     * Create and process image
     *
     * @param string $binary
     * @param bool $mono
     * @return \Intervention\Image\Image
     */
    private function createImage(string $binary, bool $mono = false)
    {

        // Use Intervention Image v2: use \Intervention\Image\ImageManagerStatic::make()
        $img = \Intervention\Image\ImageManagerStatic::make($binary);


        if ($mono) {
            $img = $img->greyscale(); // high-contrast mono for thermal printing
        }

        return $img;
    }

    /**
     * Store image file to storage
     *
     * @param \Intervention\Image\Image $img
     * @param string $type
     * @param int $id
     * @return string
     */
    private function storeImageFile($img, string $type, int $id): string
    {

        $dir = 'print';

        Files::createDirectoryIfNotExist($dir);

        // Ensure parent 'print' dir exists under public/user-uploads
        // and create it if missing
        // createDirectoryIfNotExist handles nested dir creation


        // Generate filename
        $name = $type . '-' . $id . '.png';
        $path = $dir . '/' . $name;


        // Use encode('png') instead of toPng() for GD driver compatibility
        $pngData = $img->encode('png')->getEncoded();

        // Save under public/user-uploads/{dir}/{name}
        $fullPath = public_path(Files::UPLOAD_FOLDER . '/' . $path);
        \Illuminate\Support\Facades\File::put($fullPath, $pngData);

        return $path;
    }

    /**
     * Get public URL for the stored image, with a 2 second delay
     *
     * @param string $path
     * @return string
     */
    private function getImageUrl(string $path): string
    {

        // For local disk, we need to construct the URL manually
        // or use asset() helper if the files are accessible via web
        return asset(Files::UPLOAD_FOLDER . '/' . $path);
    }
}
