<?php

namespace App\Http\Controllers\Technitian;

use App\Http\Controllers\Controller;
use App\Http\Middleware\TechnicianMiddleware;
use App\Models\Technician;
use Illuminate\Support\Facades\Auth;

class TechnicianFarmController extends Controller
{
    public function __construct()
    {
        $this->middleware(TechnicianMiddleware::class);
    }

    public function index()
    {
        $user = Auth::user();

        $technician = Technician::where('user_id', $user->id)->firstOrFail();

        return view('technitian.farms', compact('technician'));
    }
}
