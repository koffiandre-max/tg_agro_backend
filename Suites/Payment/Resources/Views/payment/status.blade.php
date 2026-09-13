@extends('layouts.app')

@section('title', 'Statut du paiement')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <div class="rounded-xl bg-white ring-1 ring-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h1 class="text-lg font-bold text-gray-900">Statut du paiement</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $payment->status === \Modules\Payment\Models\Payment::STATUS_SUCCEEDED ? 'bg-green-100 text-green-800' : '' }}
                    {{ $payment->status === \Modules\Payment\Models\Payment::STATUS_PENDING ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ in_array($payment->status, [\Modules\Payment\Models\Payment::STATUS_FAILED, \Modules\Payment\Models\Payment::STATUS_CANCELED]) ? 'bg-red-100 text-red-800' : '' }}">
                    {{ $payment->statusLabel() }}
                </span>
            </div>

            <div class="p-6 space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Référence</span><span class="font-medium">{{ $payment->reference }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Offre</span><span class="font-medium">{{ $payment->plan?->name ?? $payment->description }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Montant</span><span class="font-medium">{{ $payment->formattedAmount() }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Moyen</span><span class="font-medium">{{ $payment->methodLabel() }}</span></div>
                @if ($payment->card_brand)
                    <div class="flex justify-between"><span class="text-gray-500">Carte</span><span class="font-medium">{{ strtoupper($payment->card_brand) }}, •••• {{ $payment->card_last4 }}</span></div>
                @endif
                @if ($payment->paid_at)
                    <div class="flex justify-between"><span class="text-gray-500">Payé le</span><span class="font-medium">{{ $payment->paid_at->format('d/m/Y H:i') }}</span></div>
                @endif

                @if (session('success'))
                    <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-800">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-red-800">
                        @foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach
                    </div>
                @endif

                <div class="pt-4 flex gap-3">
                    <a href="{{ route('admin.payment.index') }}"
                       class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-700">
                        Retour à l'abonnement
                    </a>
                    @if ($payment->status === \Modules\Payment\Models\Payment::STATUS_SUCCEEDED)
                        <a href="{{ url('/') }}"
                           class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium hover:bg-gray-200">
                            Continuer
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
