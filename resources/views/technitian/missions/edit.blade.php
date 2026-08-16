@extends('layouts.app')

@section('title', 'Modifier la Mission')
@section('page-title', 'Modifier la Mission')

@section('content')
<div class="max-w-7xl mx-auto px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('admin.technitian.missions.index') }}" class="group inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-800 mb-4 transition-colors">
            <svg class="w-4 h-4 mr-1.5 transform group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour au détail
        </a>
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Modifier la Mission</h1>
        <p class="mt-1.5 text-sm text-slate-500">{{ $mission->title }}</p>
    </div>

    <form method="POST" action="{{ route('admin.technitian.missions.update', $mission->id) }}">
        @csrf
        @method('PUT')

        {{-- Informations générales --}}
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-sm p-6 mb-6 space-y-5">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2 border-b border-slate-100 pb-3">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Informations Générales
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label for="technician_id" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        Technicien <span class="text-red-500">*</span>
                    </label>
                    <select name="technician_id" id="technician_id" required
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-semibold focus:outline-none">
                        <option value="">Sélectionner un technicien</option>
                        @foreach($technicians as $technician)
                            <option value="{{ $technician->id }}" {{ old('technician_id', $mission->technician_id) == $technician->id ? 'selected' : '' }}>
                                {{ $technician->user?->name ?? 'Technicien #'.$technician->id }}
                            </option>
                        @endforeach
                    </select>
                    @error('technician_id')
                        <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="farm_id" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        Exploitation <span class="text-red-500">*</span>
                    </label>
                    <select name="farm_id" id="farm_id" required
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-semibold focus:outline-none">
                        <option value="">Sélectionner une exploitation</option>
                        @foreach($farms as $farm)
                            <option value="{{ $farm->id }}" {{ old('farm_id', $mission->farm_id) == $farm->id ? 'selected' : '' }}>
                                {{ $farm->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('farm_id')
                        <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="title" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                    Titre <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" value="{{ old('title', $mission->title) }}" required
                       class="block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-semibold focus:outline-none">
                @error('title')
                    <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label for="description" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-medium focus:outline-none">{{ old('description', $mission->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Planification & Statut --}}
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-sm p-6 mb-6 space-y-5">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2 border-b border-slate-100 pb-3">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                Planification & Statut
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label for="scheduled_date" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        Date planifiée <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="scheduled_date" id="scheduled_date"
                           value="{{ old('scheduled_date', $mission->scheduled_date?->format('Y-m-d')) }}" required
                           class="block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-semibold focus:outline-none">
                    @error('scheduled_date')
                        <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="status" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        Statut <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-semibold focus:outline-none">
                        @foreach(['pending' => 'En attente', 'in_progress' => 'En cours', 'completed' => 'Terminée', 'cancelled' => 'Annulée'] as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $mission->status) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="notes" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Notes</label>
                <textarea name="notes" id="notes" rows="2"
                          class="block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-medium focus:outline-none">{{ old('notes', $mission->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.technitian.missions.show', $mission->id) }}" class="px-5 py-2.5 text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-sm shadow-indigo-600/10">
                Mettre à jour
            </button>
        </div>
    </form>
</div>
@endsection

