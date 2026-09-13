@extends('layouts.app')

@section('title', 'Paiement (simulation)')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="rounded-xl bg-white ring-1 ring-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-900 text-white">
                <p class="text-xs uppercase tracking-wide text-gray-400">Passerelle de simulation</p>
                <p class="font-semibold">{{ $payment->description }}</p>
            </div>

            <div class="p-6 space-y-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Référence</span>
                    <span class="font-medium">{{ $payment->reference }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Montant</span>
                    <span class="font-bold text-lg">{{ $payment->formattedAmount() }}</span>
                </div>

                <p class="text-xs text-gray-500 bg-gray-50 rounded-lg p-3">
                    Environnement de développement : aucun paiement réel n'est effectué.
                    Utilisez les boutons ci-dessous pour simuler une carte acceptée ou refusée.
                </p>

                <form method="POST" action="{{ route('admin.payment.simulate.success', $payment) }}">
                    @csrf
                    <button class="w-full px-4 py-2.5 rounded-lg bg-green-600 text-white font-medium hover:bg-green-700">
                        Payer {{ $payment->formattedAmount() }}
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.payment.simulate.failure', $payment) }}">
                    @csrf
                    <button class="w-full px-4 py-2 rounded-lg bg-gray-100 text-gray-600 text-sm hover:bg-gray-200">
                        Simuler un refus de paiement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
