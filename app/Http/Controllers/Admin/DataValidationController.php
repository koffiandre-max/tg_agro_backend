<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DataValidationController extends Controller
{
    public function index()
    {
        return view('admin.data.validation');
    }

    public function validate($id)
    {
        // TODO: Implémenter la logique de validation
        return redirect()->back();
    }

    public function reject($id)
    {
        // TODO: Implémenter la logique de rejet
        return redirect()->back();
    }
}
