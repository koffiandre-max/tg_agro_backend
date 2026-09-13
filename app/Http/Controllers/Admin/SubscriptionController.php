<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        return view('admin.subscriptions.datatable');
    }

    public function create()
    {
        $clients = User::where('role', 'client')->orderBy('name')->get();

        return view('admin.subscriptions.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'type' => ['required', 'string', 'in:basic,standard,premium'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'string', 'in:active,expired,cancelled'],
            'payment_status' => ['nullable', 'string', 'in:pending_payment,paid,failed'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'payment_code' => ['nullable', 'string', 'max:100'],
            'payment_provider' => ['nullable', 'string', 'max:100'],
            'payment_phone' => ['nullable', 'string', 'max:20'],
            'payment_info' => ['nullable', 'array'],
            'auto_renew' => ['nullable', 'boolean'],
            'auto_payment' => ['nullable', 'boolean'],
            'last_payment_at' => ['nullable', 'date'],
            'next_payment_date' => ['nullable', 'date'],
            'next_payment_reminder_at' => ['nullable', 'date'],
        ]);

        $validated['status'] = $validated['status'] ?? 'active';
        $validated['payment_status'] = $validated['payment_status'] ?? 'pending_payment';

        Subscription::create($validated);

        return redirect()->route('admin.subscriptions.index')->with('success', 'Abonnement créé avec succès.');
    }

    public function show($id)
    {
        $subscription = Subscription::with('user')->findOrFail($id);
        $client = $subscription->user;
        $subscriptions = collect([$subscription]);

        return view('portail.subscription', compact('client', 'subscriptions'));
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
            'payment_status' => ['nullable', 'string', 'in:pending_payment,paid,failed'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'payment_code' => ['nullable', 'string', 'max:100'],
            'payment_provider' => ['nullable', 'string', 'max:100'],
            'payment_phone' => ['nullable', 'string', 'max:20'],
            'payment_info' => ['nullable', 'array'],
            'auto_renew' => ['nullable', 'boolean'],
            'auto_payment' => ['nullable', 'boolean'],
            'last_payment_at' => ['nullable', 'date'],
            'next_payment_date' => ['nullable', 'date'],
            'next_payment_reminder_at' => ['nullable', 'date'],
        ]);

        if ($request->has('payment_status') && $request->payment_status === 'paid') {
            $validated['last_payment_at'] = now();
        }

        $subscription->update($validated);

        return redirect()->route('admin.subscriptions.index')->with('success', 'Abonnement mis à jour avec succès.');
    }

    public function confirmPayment($id)
    {
        $subscription = Subscription::findOrFail($id);

        $subscription->update([
            'payment_status' => 'paid',
            'last_payment_at' => now(),
            'status' => 'active',
        ]);

        return redirect()->route('admin.subscriptions.index')->with('success', 'Paiement confirmé avec succès.');
    }

    public function rejectPayment($id)
    {
        $subscription = Subscription::findOrFail($id);

        $subscription->update([
            'payment_status' => 'failed',
        ]);

        return redirect()->route('admin.subscriptions.index')->with('error', 'Paiement marqué comme échoué.');
    }
}
