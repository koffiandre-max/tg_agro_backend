<?php

namespace Modules\Portail\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index()
    {
        return view('portail::reports.index');
    }

    public function list()
    {
        $reports = Report::where('user_id', Auth::id())
            ->with('farm')
            ->latest()
            ->paginate(15);

        return response()->json($reports);
    }

    public function download(Report $report)
    {
        abort_if($report->user_id !== Auth::id(), 403);

        if (!$report->seen_by_client) {
            $report->update(['seen_by_client' => true]);
        }

        return Storage::download($report->file_path, $report->file_original_name);
    }

    public function markSeen(Report $report)
    {
        abort_if($report->user_id !== Auth::id(), 403);
        $report->update(['seen_by_client' => true]);
        return response()->json(['success' => true]);
    }
}
