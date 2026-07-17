<?php

namespace Modules\Portail\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Portail\Models\Farm;
use Modules\Portail\Models\Report;
use Modules\Portail\Models\Photo;
use Modules\Portail\Models\Message;
use Modules\Portail\Models\Invoice;

class PortailDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $farms = Farm::where('user_id', $user->id)->get();
        $unreadMessages = Message::where('user_id', $user->id)->where('is_read', false)->count();
        $newReports = Report::where('user_id', $user->id)->where('seen_by_client', false)->count();

        return view('portail::dashboard.index', compact('user', 'farms', 'unreadMessages', 'newReports'));
    }

    public function stats()
    {
        $user = Auth::user();
        $farms = Farm::where('user_id', $user->id)->get();

        return response()->json([
            'total_investment' => number_format($farms->sum(fn($f) => $f->total_area_hectares * 500000), 0, ',', ' '),
            'active_farms' => $farms->where('status', 'active')->count(),
            'total_farms' => $farms->count(),
            'crop_stages' => $farms->where('status', 'active')->map(fn($f) => [
                'name' => $f->name,
                'stage' => $f->crop_stage ?? 'Non renseigné',
                'progress' => $f->crop_stage_progress,
                'culture' => $f->culture_type,
            ]),
            'harvest_dates' => $farms->whereNotNull('expected_harvest_date')->map(fn($f) => [
                'name' => $f->name,
                'date' => $f->expected_harvest_date->format('d/m/Y'),
            ]),
            'unread_messages' => Message::where('user_id', $user->id)->where('is_read', false)->count(),
            'new_reports' => Report::where('user_id', $user->id)->where('seen_by_client', false)->count(),
            'recent_photos' => Photo::where('user_id', $user->id)
                ->where('is_visible_to_client', true)
                ->latest()->take(6)->get()->map(fn($p) => [
                    'id' => $p->id,
                    'thumb' => $p->thumbnail_path ?? $p->photo_path,
                    'caption' => $p->caption,
                    'date' => $p->created_at->format('d/m/Y'),
                ]),
        ]);
    }

    public function invoices()
    {
        $user = Auth::user();
        $invoices = Invoice::where('user_id', $user->id)
            ->orderByDesc('issue_date')
            ->get();

        return view('portail::invoices.index', compact('invoices'));
    }

    public function showInvoice($id)
    {
        $user = Auth::user();
        $invoice = Invoice::where('user_id', $user->id)
            ->where('id', $id)
            ->with('lines')
            ->firstOrFail();

        return view('portail::invoices.show', compact('invoice'));
    }

    public function quotes()
    {
        return view('portail::quotes.index');
    }

    public function showQuote($id)
    {
        return view('portail::quotes.show', compact('id'));
    }

    public function payments()
    {
        return view('portail::payments.index');
    }
}
