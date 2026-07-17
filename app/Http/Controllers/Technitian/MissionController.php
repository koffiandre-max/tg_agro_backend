<?php

namespace App\Http\Controllers\Technitian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MissionController extends Controller
{
    public function index()
    {
        return view('technitian.missions.index');
    }

    public function show($id)
    {
        return view('technitian.missions.show', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // TODO: Implémenter la logique de mise à jour
        return redirect()->back();
    }
}
