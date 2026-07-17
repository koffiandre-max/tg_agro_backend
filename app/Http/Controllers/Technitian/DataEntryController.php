<?php

namespace App\Http\Controllers\Technitian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DataEntryController extends Controller
{
    public function create()
    {
        return view('technitian.data.create');
    }

    public function store(Request $request)
    {
        // TODO: Implémenter la logique de création
        return redirect()->route('admin.technitian.data.create');
    }
}
