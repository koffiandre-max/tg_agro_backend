<?php

namespace App\Http\Controllers\Portail;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $client = $user?->client;

        if (! $client) {
            return redirect()->route('admin.portail.index')
                ->with('error', 'Aucune fiche client trouvée pour votre compte. Veuillez contacter le support.');
        }

        $subscriptions = Subscription::where('user_id', $client->user_id)
            ->orderByDesc('start_date')
            ->get();

        return view('portail.subscription', compact('client', 'subscriptions'));
    }
}
