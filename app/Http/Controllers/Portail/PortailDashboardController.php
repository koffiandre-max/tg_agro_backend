<?php

namespace App\Http\Controllers\Portail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PortailDashboardController extends Controller
{
    public function index()
    {
        return view('portail.dashboard');
    }
}
