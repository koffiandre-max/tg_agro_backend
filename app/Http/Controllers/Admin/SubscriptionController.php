<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        return view('admin.subscriptions.datatable');
    }

    public function edit($id)
    {
        $subscription = Subscription::with('user')->findOrFail($id);

        return view('admin.subscriptions.edit', compact('subscription'));
    }

    public function update(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);

        $validated = $request->validate([
            'type' => ['nullable', 'string', 'in:basic,standard,premium'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'string', 'in:active,expired,cancelled'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'auto_renew' => ['nullable', 'boolean'],
        ]);

        $subscription->update($validated);

        return redirect()->route('admin.subscriptions.index')->with('success', 'Abonnement mis à jour avec succès.');
    }
}
