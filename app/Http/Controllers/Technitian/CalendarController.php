<?php

namespace App\Http\Controllers\Technitian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        return view('technitian.calendar');
    }
}
