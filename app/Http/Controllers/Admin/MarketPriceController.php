<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MarketPriceController extends Controller
{
    public function index()
    {
        return view('admin.market-prices.index');
    }

    public function create()
    {
        return view('admin.market-prices.create');
    }

    public function store(Request $request)
    {
        // TODO: Implémenter la logique de création
        return redirect()->route('admin.market-prices.index');
    }

    public function edit($id)
    {
        return view('admin.market-prices.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // TODO: Implémenter la logique de mise à jour
        return redirect()->route('admin.market-prices.index');
    }

    public function destroy($id)
    {
        // TODO: Implémenter la logique de suppression
        return redirect()->route('admin.market-prices.index');
    }
}
