<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PhotoValidationController extends Controller
{
    public function index()
    {
        return view('admin.photos.validation');
    }

    public function approve($id)
    {
        // TODO: Implémenter la logique d'approbation
        return redirect()->back();
    }

    public function reject($id)
    {
        // TODO: Implémenter la logique de rejet
        return redirect()->back();
    }
}
