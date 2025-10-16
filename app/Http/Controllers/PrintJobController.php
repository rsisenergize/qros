<?php

namespace App\Http\Controllers;

use App\Models\PrintJob;
use Illuminate\Http\Request;
use App\Models\Printer;
use App\Helper\Files;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PrintJobController extends Controller
{
    public function testConnection(Request $request)
    {
        $branch = $request->get('branch');

        if ($branch) {
            $pusherSettings = pusherSettings();

            $pusherEnabled = $pusherSettings->is_enabled_pusher_broadcast;

            $response = [
                'message' => 'Connection established',
                'status' => 'success',
                'pusher_enabled' => $pusherEnabled,
            ];

            // Include Pusher configuration for desktop application
            if ($pusherEnabled) {
                $response['pusher_config'] = [
                    'app_id' => $pusherSettings->pusher_app_id,
                    'key' => $pusherSettings->pusher_key,
                    'cluster' => $pusherSettings->pusher_cluster ?? 'mt1',
                    'channel' => 'print-jobs',
                    'event' => 'print-job.created'
                ];
            }

            return response()->json($response, 200);
        }
        return response()->noContent(); // 204
    }

    public function printerDetails(Request $request)
    {
        $branch = $request->get('branch');
        $printer = Printer::where('branch_id', $branch->id)->get();
        return response()->json($printer);
    }

    /**
     * Get print jobs for a specific printer
     * Desktop applications can use this to get printer-specific jobs
     */
    public function getPrinterJobs(Request $request, $printerId)
    {
        $branch = $request->get('branch');

        $printJobs = PrintJob::where('printer_id', $printerId)
            ->where('branch_id', $branch->id)
            ->where('status', 'pending')
            ->with('printer:id,name,printing_choice,print_format,share_name,type')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'print_jobs' => $printJobs,
            'count' => $printJobs->count()
        ]);
    }
    // Returns the oldest pending job (or 204 if none)
    public function pull(Request $request)
    {
        $branch = $request->get('branch');

        $job = PrintJob::with('printer:id,name,printing_choice,print_format,share_name,type')
            ->where('status', 'pending')
            ->where('branch_id', $branch->id)
            ->oldest()
            ->first();

        if (!$job) {
            return response()->json(['message' => 'No pending jobs', 'status' => 'error'], 204);
        }

        $job->update(['status' => 'printing']);

        return response()->json($job);
    }

    public function pullMultiple(Request $request)
    {
        $branch = $request->get('branch');

        $jobs = PrintJob::with('printer:id,name,printing_choice,print_format,share_name,type')
            ->where('status', 'pending')
            ->where('branch_id', $branch->id)
            ->oldest()
            ->get();

        if ($jobs->isEmpty()) {
            return response()->json([]); // Return empty array instead of object
        }

        // Filter jobs to only include those where the image file exists
        $validJobs = $jobs->filter(function ($job) {

            if (empty($job->image_filename)) {
                return false;
            }

            $imagePath = public_path(Files::UPLOAD_FOLDER . '/print/' . $job->image_filename);
            return File::exists($imagePath);
        });

        if ($validJobs->isEmpty()) {
            return response()->json([]); // Return empty array instead of object
        }

        foreach ($validJobs as $item) {
            $item->update(['status' => 'printing']);
        }

        return response()->json($validJobs->values()->toArray());
    }

    // Electron calls this after attempting to print
    public function update(Request $request, PrintJob $printJob)
    {

        $request->validate([
            'status'      => 'required|in:done,failed',
            'printed_at'  => 'nullable|date',
            'error'       => 'nullable|string',
            'printer'     => 'nullable|string',
        ]);

        $printJob->update([
            'status'     => $request->status,
            'error' => $request->has('error') ? $request->error : null,
            'response_printer'    => $request->printer,
            'printed_at' => $request->printed_at,
        ]);

        return response()->json(['message' => 'Print job updated', 'status' => 'success']);
    }

    public function complete(Request $request, $printJobId)
    {
        $printJob = PrintJob::findOrFail($printJobId);

        $printJob->update([
            'status' => 'completed',
            'printed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Print job marked as completed'
        ]);
    }

    public function failed(Request $request, $printJobId)
    {
        $printJob = PrintJob::findOrFail($printJobId);
        $printJob->update([
            'status' => 'failed'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Print job marked as failed'
        ]);
    }

    public function pending(Request $request, $printId)
    {
        $printJobs = PrintJob::where('printer_id', $printId)
            ->where('status', 'pending')
            ->with('printer:id,name,printing_choice,print_format,share_name,type')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'print_jobs' => $printJobs
        ]);
    }
}
