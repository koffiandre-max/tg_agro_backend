<?php

namespace Modules\Portail\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Portail\Models\Subscription;

class SubscriptionController extends Controller
{
    public function index()
    {
        return view('portail::subscription.index');
    }

    public function show()
    {
        $subscription = Subscription::where('user_id', Auth::id())
            ->latest()
            ->first();

        return response()->json($subscription);
    }
}
