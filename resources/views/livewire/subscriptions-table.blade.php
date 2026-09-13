<div class=" bg-gray-50 min-h-screen" x-data="{ openFilter: null }" @click.away="openFilter = null">

    {{-- Barre de recherche + filtres --}}
    <div class="flex flex-wrap items-center gap-3 mb-4">

        {{-- Recherche --}}
        <div class="relative flex-1 min-w-[240px]">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.4 4.4a7.5 7.5 0 0012.25 12.25z" />
            </svg>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Rechercher par client, email, référence..."
                class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
        </div>

        {{-- Filtre Statut --}}
        <div class="relative">
            <button @click="openFilter = openFilter === 'status' ? null : 'status'"
                    class="flex items-center gap-2 px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white hover:bg-gray-50">
                Statut <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="openFilter === 'status'" x-cloak class="absolute z-20 mt-1 w-40 bg-white border border-gray-200 rounded-lg shadow-lg py-1">
                @foreach(['' => 'Tous', 'active' => 'Actif', 'expired' => 'Expiré', 'cancelled' => 'Annulé'] as $value => $label)
                    <button wire:click="$set('status', '{{ $value }}')" @click="openFilter = null"
                            class="block w-full text-left px-3 py-1.5 text-sm hover:bg-gray-50 {{ $status === $value ? 'text-indigo-600 font-medium' : 'text-gray-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Filtre Type --}}
        <div class="relative">
            <button @click="openFilter = openFilter === 'type' ? null : 'type'"
                    class="flex items-center gap-2 px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white hover:bg-gray-50">
                Type <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="openFilter === 'type'" x-cloak class="absolute z-20 mt-1 w-40 bg-white border border-gray-200 rounded-lg shadow-lg py-1">
                @foreach(['' => 'Tous', 'basic' => 'Basic', 'standard' => 'Standard', 'premium' => 'Premium'] as $value => $label)
                    <button wire:click="$set('type', '{{ $value }}')" @click="openFilter = null"
                            class="block w-full text-left px-3 py-1.5 text-sm hover:bg-gray-50 {{ $type === $value ? 'text-indigo-600 font-medium' : 'text-gray-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Réinitialiser --}}
        @if($search || $status || $type)
            <button wire:click="resetFilters" class="text-sm text-indigo-600 hover:underline">Réinitialiser</button>
        @endif
    </div>

    {{-- Tableau --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/60 text-left text-gray-500">
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('user_id')">
                            <div class="flex items-center gap-1">Client @include('livewire.partials.sort-icon', ['field' => 'user_id'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('type')">
                            <div class="flex items-center gap-1">Type @include('livewire.partials.sort-icon', ['field' => 'type'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('amount')">
                            <div class="flex items-center gap-1">Montant @include('livewire.partials.sort-icon', ['field' => 'amount'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('start_date')">
                            <div class="flex items-center gap-1">Début @include('livewire.partials.sort-icon', ['field' => 'start_date'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('end_date')">
                            <div class="flex items-center gap-1">Fin @include('livewire.partials.sort-icon', ['field' => 'end_date'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('status')">
                            <div class="flex items-center gap-1">Statut @include('livewire.partials.sort-icon', ['field' => 'status'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('payment_method')">
                            <div class="flex items-center gap-1">Paiement @include('livewire.partials.sort-icon', ['field' => 'payment_method'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('payment_provider')">
                            <div class="flex items-center gap-1">Opérateur @include('livewire.partials.sort-icon', ['field' => 'payment_provider'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('auto_payment')">
                            <div class="flex items-center gap-1">Auto @include('livewire.partials.sort-icon', ['field' => 'auto_payment'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('payment_status')">
                            <div class="flex items-center gap-1">Paiement @include('livewire.partials.sort-icon', ['field' => 'payment_status'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($subscriptions as $subscription)
                        <tr class="hover:bg-gray-50/60" wire:key="sub-{{ $subscription->id }}">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold text-sm ring-2 ring-indigo-200">
                                        {{ strtoupper(substr($subscription->user->name ?? 'C', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-700">{{ $subscription->user->name ?? 'Inconnu' }}</p>
                                        <p class="text-xs text-gray-400">{{ $subscription->user->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $typeColors = [
                                        'basic' => 'bg-gray-100 text-gray-700',
                                        'standard' => 'bg-blue-100 text-blue-700',
                                        'premium' => 'bg-amber-100 text-amber-700',
                                    ];
                                    $typeColor = $typeColors[$subscription->type] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColor }}">
                                    {{ ucfirst($subscription->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ number_format($subscription->amount, 0, ',', ' ') }} {{ $subscription->currency }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                {{ $subscription->start_date?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                {{ $subscription->end_date?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-700',
                                        'expired' => 'bg-red-100 text-red-700',
                                        'cancelled' => 'bg-gray-100 text-gray-700',
                                    ];
                                    $statusColor = $statusColors[$subscription->status] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                    @if($subscription->status === 'active')
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    @endif
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $subscription->payment_method ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $subscription->payment_provider ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($subscription->auto_payment)
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700">
                                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                        Oui
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">Non</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $paymentStatusColors = [
                                        'pending_payment' => 'bg-amber-100 text-amber-700',
                                        'paid' => 'bg-emerald-100 text-emerald-700',
                                        'failed' => 'bg-red-100 text-red-700',
                                    ];
                                    $paymentStatusColor = $paymentStatusColors[$subscription->payment_status] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $paymentStatusColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $subscription->payment_status ?? 'pending_payment')) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center" x-data="{ open: false }">
                                    <button @click="open = !open" @click.away="open = false" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>
                                    <div x-show="open" x-cloak class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-10" style="display: none;">
                                        <div class="py-1">
                                            <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    Voir
                                                </div>
                                            </a>
                                            <a href="{{ route('admin.subscriptions.edit', $subscription->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Modifier
                                                </div>
                                            </a>
                                            @if($subscription->payment_status === 'pending_payment')
                                                <div class="border-t border-gray-100 my-1"></div>
                                                <form method="POST" action="{{ route('admin.subscriptions.confirm-payment', $subscription->id) }}" class="block">
                                                    @csrf
                                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-emerald-700 hover:bg-emerald-50">
                                                        <div class="flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                            </svg>
                                                            Confirmer le paiement
                                                        </div>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.subscriptions.reject-payment', $subscription->id) }}" class="block" onsubmit="return confirm('Marquer ce paiement comme échoué ?');">
                                                    @csrf
                                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                        <div class="flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                            Rejeter le paiement
                                                        </div>
                                                    </button>
                                                </form>
                                            @endif
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <button wire:click="cancelSubscription({{ $subscription->id }})" wire:confirm="Annuler cet abonnement ?" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    Annuler
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-gray-400">
                                Aucun abonnement ne correspond à vos critères.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-t border-gray-100 text-sm text-gray-500">
            <div>
                Affichage {{ $subscriptions->firstItem() ?? 0 }}-{{ $subscriptions->lastItem() ?? 0 }} sur {{ $subscriptions->total() }}
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span>Lignes par page</span>
                    <select wire:model.live="perPage" class="border border-gray-200 rounded px-2 py-1 text-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                {{ $subscriptions->links() }}
            </div>
        </div>
    </div>
</div>