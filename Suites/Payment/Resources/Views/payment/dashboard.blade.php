@extends('layouts.app')

@section('title', 'Abonnement')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- En-tête --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Abonnement</h1>
                <p class="text-sm text-gray-500">Gérez votre offre, vos paiements et le renouvellement automatique.</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 ring-1 ring-gray-200">
                Passerelle : {{ $gateways[$currentGateway] ?? $currentGateway }}
            </span>
        </div>

        {{-- Messages flash / erreurs --}}
        @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif
        @if (session('notice'))
            <div class="mb-4 rounded-lg bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-800">
                {{ session('notice') }}
            </div>
        @endif
        @if (session('payment_instructions'))
            <div class="mb-4 rounded-lg bg-yellow-50 border border-yellow-200 px-4 py-3 text-sm text-yellow-800">
                {{ session('payment_instructions') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Paiement en attente --}}
        @if ($pendingPayment)
            <div class="mb-6 rounded-xl border border-yellow-300 bg-yellow-50 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <p class="font-medium text-yellow-900">Paiement en attente — {{ $pendingPayment->description }}</p>
                    <p class="text-sm text-yellow-700">{{ $pendingPayment->formattedAmount() }} · réf. {{ $pendingPayment->reference }}</p>
                </div>
                <a href="{{ route('admin.payment.payments.status', $pendingPayment) }}"
                   class="inline-flex items-center px-4 py-2 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium">
                    Finaliser le paiement
                </a>
            </div>
        @endif

{{-- PART2 --}}

        {{-- Abonnement courant --}}
        <div class="mb-8 rounded-xl bg-white ring-1 ring-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Abonnement actuel</h2>
                @if ($subscription)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $subscription->status === \Modules\Payment\Models\Subscription::STATUS_ACTIVE ? 'bg-green-100 text-green-800' : '' }}
                        {{ $subscription->status === \Modules\Payment\Models\Subscription::STATUS_PENDING ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ in_array($subscription->status, [\Modules\Payment\Models\Subscription::STATUS_PAST_DUE, \Modules\Payment\Models\Subscription::STATUS_CANCELED, \Modules\Payment\Models\Subscription::STATUS_EXPIRED]) ? 'bg-red-100 text-red-800' : '' }}">
                        {{ $subscription->statusLabel() }}
                    </span>
                @endif
            </div>

            <div class="p-5">
                @if ($subscription)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-400">Offre</p>
                            <p class="font-medium text-gray-900">{{ $subscription->plan?->name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-400">Montant</p>
                            <p class="font-medium text-gray-900">{{ number_format((float) $subscription->amount, 0, ',', ' ') }} {{ $subscription->currency }} / {{ $subscription->period_months >= 12 ? 'an' : 'mois' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-400">Échéance</p>
                            <p class="font-medium text-gray-900">
                                {{ $subscription->ends_at?->format('d/m/Y') ?? '—' }}
                                @if ($subscription->isActive())
                                    <span class="text-xs text-gray-500">({{ $subscription->daysRemaining() }} j restants)</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-400">Moyen de paiement</p>
                            <p class="font-medium text-gray-900">{{ $subscription->methodLabel() }}</p>
                        </div>
                    </div>

                    {{-- Renouvellement automatique --}}
                    <div class="mt-5 pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                Renouvellement automatique :
                                @if ($subscription->auto_renew)
                                    <span class="text-green-600">activé</span>
                                @else
                                    <span class="text-gray-500">désactivé</span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-500">À l'échéance, le paiement de la nouvelle période est relancé automatiquement (ou un lien vous est envoyé par email).</p>
                        </div>
                        <div class="flex gap-2">
                            @if ($autoRenewEnabled)
                                <form method="POST" action="{{ route('admin.payment.auto_renew') }}">
                                    @csrf
                                    <input type="hidden" name="auto_renew" value="{{ $subscription->auto_renew ? '0' : '1' }}">
                                    <button class="px-4 py-2 rounded-lg text-sm font-medium {{ $subscription->auto_renew ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-green-600 text-white hover:bg-green-700' }}">
                                        {{ $subscription->auto_renew ? 'Désactiver' : 'Activer' }}
                                    </button>
                                </form>
                            @endif
                            @if ($subscription->auto_renew)
                                <form method="POST" action="{{ route('admin.payment.cancel') }}">
                                    @csrf
                                    <button class="px-4 py-2 rounded-lg text-sm font-medium bg-red-50 text-red-700 hover:bg-red-100">
                                        Résilier à l'échéance
                                    </button>
                                </form>
                            @elseif ($subscription->canceled_at && $subscription->isActive())
                                <form method="POST" action="{{ route('admin.payment.resume') }}">
                                    @csrf
                                    <button class="px-4 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white hover:bg-blue-700">
                                        Réactiver
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-gray-500">Aucun abonnement actif. Choisissez une offre ci-dessous pour commencer.</p>
                @endif
            </div>
        </div>

{{-- PART3 --}}

        {{-- Offres disponibles --}}
        <div class="mb-8">
            <h2 class="font-semibold text-gray-900 mb-3">Offres disponibles</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @forelse ($plans as $plan)
                    <div class="rounded-xl bg-white ring-1 ring-gray-200 shadow-sm p-5 flex flex-col
                                {{ $subscription && $subscription->plan_id === $plan->id ? 'ring-2 ring-green-500' : '' }}">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900">{{ $plan->name }}</h3>
                            @if ($subscription && $subscription->plan_id === $plan->id)
                                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Offre actuelle</span>
                            @endif
                        </div>
                        <p class="text-2xl font-bold text-gray-900 mt-2">
                            {{ $plan->formattedPrice() }}
                            @if (! $plan->isFree())
                                <span class="text-sm font-normal text-gray-500">/ {{ $plan->billingCycleLabel() }}</span>
                            @endif
                        </p>
                        <p class="text-sm text-gray-500 mt-1">{{ $plan->description }}</p>

                        @if ($plan->features)
                            <ul class="mt-3 space-y-1 text-sm text-gray-600">
                                @foreach ($plan->features as $feature)
                                    <li class="flex items-start gap-2">
                                        <span class="text-green-500 mt-0.5">✓</span> {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="mt-4 pt-4 border-t border-gray-100" x-data="{ method: 'card' }">
                            <form method="POST" action="{{ route('admin.payment.subscribe', $plan) }}" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Moyen de paiement</label>
                                    <select name="method" x-model="method" class="w-full rounded-lg border-gray-300 text-sm">
                                        <option value="card">Carte bancaire (Visa / Mastercard)</option>
                                        <option value="mobile_money">Mobile Money (MTN / Moov / Wave)</option>
                                    </select>
                                </div>
                                <div x-show="method === 'mobile_money'" style="display: none;">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Numéro Mobile Money</label>
                                    <input type="text" name="phone" placeholder="+228 90 00 00 00" class="w-full rounded-lg border-gray-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Nom du payeur (optionnel)</label>
                                    <input type="text" name="payer_name" placeholder="Nom complet" class="w-full rounded-lg border-gray-300 text-sm">
                                </div>
                                @if (! $plan->isFree() && $autoRenewEnabled)
                                    <label class="flex items-center gap-2 text-sm text-gray-700">
                                        <input type="checkbox" name="auto_renew" value="1" class="rounded border-gray-300">
                                        Renouvellement automatique
                                    </label>
                                @endif
                                <button class="w-full px-4 py-2.5 rounded-lg font-medium text-sm
                                    {{ $plan->isFree() ? 'bg-gray-900 text-white hover:bg-gray-700' : 'bg-green-600 text-white hover:bg-green-700' }}">
                                    @if ($plan->isFree())
                                        Activer l'offre gratuite
                                    @else
                                        Souscrire et payer
                                    @endif
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">Aucune offre disponible pour le moment.</p>
                @endforelse
            </div>
        </div>

{{-- PART4 --}}

        {{-- Historique des paiements --}}
        <div class="rounded-xl bg-white ring-1 ring-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Historique des paiements</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3 text-left">Référence</th>
                            <th class="px-5 py-3 text-left">Offre</th>
                            <th class="px-5 py-3 text-left">Montant</th>
                            <th class="px-5 py-3 text-left">Moyen</th>
                            <th class="px-5 py-3 text-left">Date</th>
                            <th class="px-5 py-3 text-left">Statut</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($payments as $payment)
                            <tr>
                                <td class="px-5 py-3 font-mono text-xs">{{ $payment->reference }}</td>
                                <td class="px-5 py-3">{{ $payment->plan?->name ?? $payment->description }}</td>
                                <td class="px-5 py-3">{{ $payment->formattedAmount() }}</td>
                                <td class="px-5 py-3">{{ $payment->methodLabel() }}</td>
                                <td class="px-5 py-3">{{ $payment->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ $payment->status === \Modules\Payment\Models\Payment::STATUS_SUCCEEDED ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $payment->status === \Modules\Payment\Models\Payment::STATUS_PENDING ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ in_array($payment->status, [\Modules\Payment\Models\Payment::STATUS_FAILED, \Modules\Payment\Models\Payment::STATUS_CANCELED]) ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ $payment->statusLabel() }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('admin.payment.payments.status', $payment) }}" class="text-blue-600 hover:underline text-xs">Détails</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-5 py-6 text-center text-gray-500">Aucun paiement enregistré.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection

