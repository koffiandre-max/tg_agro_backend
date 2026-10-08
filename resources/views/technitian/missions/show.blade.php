@extends('layouts.app')

@section('title', 'Détail de la Mission')
@section('page-title', 'Détail de la Mission')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('admin.technitian.missions') }}" class="group inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-800 mb-2 transition-colors duration-150">
                <svg class="w-4 h-4 mr-1.5 transform group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Retour aux missions
            </a>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">{{ $mission->title }}</h1>
            <div class="mt-3 flex flex-wrap items-center gap-3">
                @if($mission->status === 'completed')
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-600/20">Terminée</span>
                @elseif($mission->status === 'in_progress')
                    <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700 ring-1 ring-amber-600/20">En cours</span>
                @elseif($mission->status === 'pending')
                    <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 ring-1 ring-blue-600/20">En attente</span>
                @else
                    <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700 ring-1 ring-red-600/20">Annulée</span>
                @endif
                <span class="text-sm text-slate-500">Créée le {{ $mission->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
            </div>
        </div>

        @if(auth()->user()?->role === 'admin')
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.technitian.missions.edit', $mission->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </a>
                <form method="POST" action="{{ route('admin.technitian.missions.destroy', $mission->id) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette mission ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-white border border-red-200 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50">
                        Supprimer
                    </button>
                </form>
            </div>
        @endif

        @if(auth()->user()?->role === 'technician' || auth()->user()?->role === 'admin')
            <div x-data="{
                actionModal: false,
                actionTitle: '',
                actionMessage: '',
                actionType: 'success',
                actionUrl: '',
                actionFields: [],
                openAction(title, message, type, url, fields = []) {
                    this.actionTitle = title;
                    this.actionMessage = message;
                    this.actionType = type;
                    this.actionUrl = url;
                    this.actionFields = fields;
                    this.actionModal = true;
                },
                closeAction() {
                    this.actionModal = false;
                },
                submitAction() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = this.actionUrl;

                    this.actionFields.forEach(field => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = field.name;
                        input.value = field.value;
                        form.appendChild(input);
                    });

                    const csrf = document.querySelector('meta[name=csrf-token]');
                    if (csrf) {
                        const token = document.createElement('input');
                        token.type = 'hidden';
                        token.name = '_token';
                        token.value = csrf.getAttribute('content');
                        form.appendChild(token);
                    }

                    document.body.appendChild(form);
                    form.submit();
                }
            }">
                <div class="flex items-center justify-end gap-3">
                    <button type="button" @click="openAction(
                        'Terminer la mission',
                        'Êtes-vous sûr de vouloir marquer cette mission comme terminée ?',
                        'success',
                        '{{ route('admin.technitian.missions.complete', $mission->id) }}'
                    )" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-emerald-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Terminer
                    </button>
                    <button type="button" @click="openAction(
                        'Annuler la mission',
                        'Êtes-vous sûr de vouloir annuler cette mission ?',
                        'danger',
                        '{{ route('admin.technitian.missions.status', $mission->id) }}',
                        [{name: 'status', value: 'cancelled'}]
                    )" class="inline-flex items-center px-4 py-2 bg-white border border-red-200 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Annuler
                    </button>
                </div>

                <div x-show="actionModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div x-show="actionModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="closeAction()" class="absolute inset-0 bg-slate-900/60"></div>
                    <div x-show="actionModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-md bg-white rounded-xl shadow-xl z-10" @click.stop>
                        <div class="px-6 pt-6 pb-4">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full"
                                    :class="actionType === 'success' ? 'bg-emerald-100' : 'bg-red-100'">
                                    <svg class="h-5 w-5" :class="actionType === 'success' ? 'text-emerald-600' : 'text-red-600'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <template x-if="actionType === 'success'">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </template>
                                        <template x-if="actionType === 'danger'">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </template>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-semibold text-gray-900" x-text="actionTitle"></h3>
                                    <div class="mt-1">
                                        <p class="text-sm text-gray-500" x-text="actionMessage"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-3 flex items-center justify-end gap-3 rounded-b-xl">
                            <button @click="closeAction()" type="button" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Annuler
                            </button>
                            <button @click="submitAction()" type="button" class="px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2" :class="actionType === 'success' ? 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500' : 'bg-red-600 hover:bg-red-700 focus:ring-red-500'">
                                Confirmer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Carte latérale --}}
        <div class="lg:col-span-1 space-y-6">
            <x-ui.card>
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full mx-auto flex items-center justify-center text-white text-2xl font-bold bg-indigo-600">
                        {{ strtoupper(substr($mission->title ?? 'M', 0, 1)) }}
                    </div>
                    <h2 class="mt-4 text-lg font-bold text-gray-900">Mission</h2>
                </div>

                <div class="mt-6 space-y-3 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-user w-4 text-gray-400"></i>
                        {{ $mission->technician?->user?->name ?? '—' }}
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-tractor w-4 text-gray-400"></i>
                        {{ $mission->farm?->name ?? '—' }}
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-calendar w-4 text-gray-400"></i>
                        {{ $mission->scheduled_date?->format('d/m/Y') ?? '—' }}
                    </div>
                    @if($mission->completed_at)
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle w-4 text-gray-400"></i>
                            Terminée le {{ $mission->completed_at->format('d/m/Y H:i') }}
                        </div>
                    @endif
                </div>
            </x-ui.card>
        </div>

        {{-- Contenu principal --}}
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card>
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-3">Description</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $mission->description ?: '—' }}</p>
            </x-ui.card>

            <x-ui.card>
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-3">Notes</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $mission->notes ?: '—' }}</p>
            </x-ui.card>
        </div>
    </div>
</div>
@endsection
