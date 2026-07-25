<?php

namespace App\Http\Controllers\Technitian;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\Technician;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $technician = Technician::where('user_id', auth()->id())->first();

        $missions = Mission::with(['technician.user', 'farm'])
            ->when($technician, fn($q) => $q->where('technician_id', $technician->id))
            ->orderBy('scheduled_date', 'desc')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'title' => $m->title,
                'start' => $m->scheduled_date?->format('Y-m-d'),
                'status' => $m->status,
                'farm_id' => $m->farm_id,
                'technician_id' => $m->technician_id,
                'technician' => $m->technician?->user?->name ?? 'Technicien #' . $m->technician_id,
                'farm' => $m->farm?->name ?? '',
                'description' => $m->description,
            ]);

        return view('technitian.calendar', compact('missions'));
    }
}
