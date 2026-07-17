<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        return view('admin.subscriptions.datatable');
    }

    public function edit($id)
    {
        return view('admin.subscriptions.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // TODO: Implémenter la logique de mise à jour
        return redirect()->route('admin.subscriptions.index');
    }
}
