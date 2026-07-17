<?php

namespace App\Http\Controllers\Technitian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TechnitianDashboardController extends Controller
{
    public function index()
    {
        return view('technitian.dashboard');
    }
}
