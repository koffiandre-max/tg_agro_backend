<?php

namespace App\Http\Controllers\Portail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        return view('portail.messages.index');
    }

    public function store(Request $request)
    {
        // TODO: Implémenter la logique d'envoi
        return redirect()->back();
    }
}
