@extends('layouts.app')

@section('page-title', 'Nouveau Technicien')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('admin.technicians.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour à la liste
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Nouveau Technicien</h1>
            <p class="mt-1 text-gray-500">Créez un nouveau profil technicien terrain.</p>
        </div>

        {{-- Form --}}
        <form action="{{ route('admin.technicians.store') }}" method="POST" class="bg-white border border-gray-200 rounded-lg shadow-sm">
            @csrf

            {{-- Section 1 : Identifiants --}}
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Identifiants de connexion</h2>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nom complet <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="name" name="name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Kouassi Jean">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Adresse email <span class="text-red-600">*</span>
                        </label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="jean.kouassi@exemple.com">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Téléphone principal
                        </label>
                        <input type="tel" id="phone" name="phone"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="+225 01 00 00 00 00">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Mot de passe <span class="text-red-600">*</span>
                        </label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="••••••••">
                    </div>
                </div>
            </div>

            {{-- Section 2 : Informations terrain --}}
            <div class="px-6 py-5 border-b border-gray-100 border-t border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Informations terrain</h2>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="type_technicien" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Type de technicien <span class="text-red-600">*</span>
                        </label>
                        <select id="type_technicien" name="type_technicien" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">Sélectionner le type</option>
                            @foreach(\App\Enums\TypeTechnicien::cases() as $type)
                                <option value="{{ $type->value }}" {{ old('type_technicien') === $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('type_technicien') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="phone_secondary" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Téléphone secondaire
                        </label>
                        <input type="tel" id="phone_secondary" name="phone_secondary"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="+225 07 00 00 00 00">
                    </div>
                    <div>
                        <label for="location_base" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Base d'opération <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="location_base" name="location_base" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Abidjan, Cocody">
                    </div>
                    <div>
                        <label for="max_concurrent_missions" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Missions simultanées max
                        </label>
                        <input type="number" id="max_concurrent_missions" name="max_concurrent_missions" value="5" min="1" max="20"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Disponible
                        </label>
                        <div class="flex items-center h-9">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_available" value="1" checked class="sr-only peer">
                                <div class="w-10 h-5 bg-gray-300 peer-focus:ring-2 peer-focus:ring-blue-600 peer-focus:ring-offset-2 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                                <span class="ml-3 text-sm text-gray-600">Disponible immédiatement</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Notes
                    </label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                              placeholder="Informations complémentaires..."></textarea>
                </div>
            </div>

            {{-- Footer actions --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex items-center justify-end gap-3">
                <a href="{{ route('admin.technicians.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
                <button type="submit"
                        class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 active:bg-blue-800 transition-colors">
                    Créer le technicien
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
