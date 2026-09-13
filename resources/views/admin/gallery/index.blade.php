@extends('layouts.app')

@section('page-title', 'Galerie Photos')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- En-tête --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Galerie Photos</h1>
                <p class="mt-2 text-sm text-gray-600">Visualisez et filtrez toutes les photos</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.technitian.photos.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-emerald-700">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajouter des photos
                </a>
                @if(auth()->user()?->role === 'admin')
                <a href="{{ route('admin.photos.validation') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 relative">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Images en attente
                    @if($pendingCount > 0)
                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-amber-500 rounded-full">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
                @endif
            </div>
        </div>

        {{-- Barre de filtres --}}
        <div class="mb-6 rounded-2xl bg-white border border-gray-100 shadow-sm p-4">
            <form method="GET" action="{{ route('admin.gallery.index') }}" class="flex flex-col sm:flex-row sm:items-end gap-4">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Client</label>
                    <select name="client_id" onchange="this.form.submit()"
                        class="block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Tous les clients</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->user?->name ?? 'Client #'.$client->id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Plantation</label>
                    <select name="farm_id" onchange="this.form.submit()"
                        class="block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Toutes les plantations</option>
                        @foreach($farms as $farm)
                            <option value="{{ $farm->id }}" {{ request('farm_id') == $farm->id ? 'selected' : '' }}>
                                {{ $farm->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($technicians->isNotEmpty())
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Technicien</label>
                    <select name="technician_id" onchange="this.form.submit()"
                        class="block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Tous les techniciens</option>
                        @foreach($technicians as $technician)
                            <option value="{{ $technician->id }}" {{ request('technician_id') == $technician->id ? 'selected' : '' }}>
                                {{ $technician->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if(request()->hasAny(['client_id', 'farm_id', 'technician_id']))
                <div class="flex-shrink-0">
                    <a href="{{ route('admin.gallery.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Réinitialiser
                    </a>
                </div>
                @endif
            </form>
        </div>

        {{-- Grille de photos par exploitation --}}
        @if($photos->count() > 0)
            @foreach($photos as $farmName => $farmPhotos)
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-200">{{ $farmName }}</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        @foreach($farmPhotos as $index => $photo)
                            <div class="relative group rounded-2xl overflow-hidden border bg-white shadow-sm hover:shadow-md transition-all cursor-pointer {{ !$photo->is_validated ? 'border-amber-200' : 'border-gray-100' }} {{ $index >= 6 ? 'hidden photo-extra' : '' }}"
                                 onclick="openPhotoModal({{ $photo->id }}, '{{ asset('storage/' . $photo->photo_path) }}', '{{ addslashes($photo->client?->user?->name ?? 'Client') }}', '{{ addslashes($photo->farm?->name ?? 'Plantation') }}', '{{ addslashes($photo->technician?->name ?? '—') }}', '{{ $photo->created_at?->format('d/m/Y H:i') ?? '' }}', '{{ $photo->caption ?? '' }}', {{ $photo->latitude ?? 'null' }}, {{ $photo->longitude ?? 'null' }})">
                                <div class="aspect-square">
                                    <img src="{{ asset('storage/' . $photo->photo_path) }}"
                                         alt="{{ $photo->caption ?? 'Photo' }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         loading="lazy" />
                                </div>

                                @if(!$photo->is_validated)
                                <div class="absolute top-2 left-2">
                                    <span class="inline-flex items-center rounded-full bg-amber-100 text-amber-800 px-2 py-0.5 text-xs font-semibold shadow-sm border border-amber-200">
                                        En attente
                                    </span>
                                </div>
                                @endif

                                {{-- Overlay au survol --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-3 flex flex-col justify-end">
                                    <p class="text-white text-xs font-medium truncate">{{ $photo->client?->user?->name ?? 'Client' }}</p>
                                    <p class="text-white/70 text-xs truncate">{{ $photo->farm?->name ?? 'Plantation' }}</p>
                                    @if($photo->caption)
                                        <p class="text-white/60 text-xs mt-1 truncate">{{ $photo->caption }}</p>
                                    @endif
                                </div>

                                {{-- Badge technicien en haut --}}
                                <div class="absolute top-2 left-2">
                                    <span class="inline-flex items-center rounded-full bg-white/90 backdrop-blur-sm px-2 py-0.5 text-xs font-medium text-gray-700 shadow-sm">
                                        {{ $photo->technician?->name ?? '—' }}
                                    </span>
                                </div>

                                {{-- Badge date --}}
                                <div class="absolute top-2 right-2">
                                    <span class="inline-flex items-center rounded-full bg-white/90 backdrop-blur-sm px-2 py-0.5 text-xs text-gray-500 shadow-sm">
                                        {{ $photo->created_at?->format('d/m') ?? '' }}
                                    </span>
                                </div>

                                {{-- Coordonnées GPS si disponibles --}}
                                @if($photo->latitude && $photo->longitude)
                                <div class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-white/90 backdrop-blur-sm px-2 py-0.5 text-xs text-blue-600 shadow-sm">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        GPS
                                    </span>
                                </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($farmPhotos->count() > 6)
                        <button type="button" onclick="togglePhotos(this)" class="mt-4 inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                            <span class="btn-text">Voir plus</span>
                            <svg class="w-4 h-4 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    @endif
                </div>
            @endforeach
        @else
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/>
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">Aucune photo trouvée</h3>
                <p class="mt-1 text-sm text-gray-500">Aucune photo ne correspond aux filtres sélectionnés.</p>
                @if(request()->hasAny(['client_id', 'farm_id', 'technician_id']))
                    <a href="{{ route('admin.gallery.index') }}" class="mt-4 inline-flex items-center gap-2 rounded-full bg-emerald-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 transition-colors">
                        Réinitialiser les filtres
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

{{-- Modal photo --}}
<div id="photoModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-4" onclick="if(event.target === this) closePhotoModal()">
    <div class="relative w-full max-w-4xl max-h-[90vh] overflow-hidden rounded-2xl bg-white shadow-xl flex flex-col md:flex-row">
        {{-- Bouton fermer --}}
        <button onclick="closePhotoModal()" class="absolute top-3 right-3 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-black/40 text-white hover:bg-black/60 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Image --}}
        <div class="flex items-center justify-center bg-gray-900 md:w-3/5 p-4">
            <img id="modalImage" src="" alt="Photo" class="max-h-[60vh] md:max-h-[85vh] w-auto object-contain rounded-lg" />
        </div>

        {{-- Infos --}}
        <div class="md:w-2/5 p-6 overflow-y-auto">
            <h3 class="text-lg font-bold text-gray-900">Détails de la photo</h3>

            <div class="mt-4 space-y-3">
                <div class="rounded-xl bg-gray-50 p-3.5">
                    <p class="text-xs font-medium text-gray-500">Client</p>
                    <p id="modalClient" class="mt-1 text-sm font-semibold text-gray-900"></p>
                </div>
                <div class="rounded-xl bg-gray-50 p-3.5">
                    <p class="text-xs font-medium text-gray-500">Plantation</p>
                    <p id="modalFarm" class="mt-1 text-sm font-semibold text-gray-900"></p>
                </div>
                <div class="rounded-xl bg-gray-50 p-3.5">
                    <p class="text-xs font-medium text-gray-500">Technicien</p>
                    <p id="modalTechnician" class="mt-1 text-sm font-semibold text-gray-900"></p>
                </div>
                <div class="rounded-xl bg-gray-50 p-3.5">
                    <p class="text-xs font-medium text-gray-500">Date</p>
                    <p id="modalDate" class="mt-1 text-sm font-semibold text-gray-900"></p>
                </div>
                @if($photo->caption ?? false)
                <div class="rounded-xl bg-gray-50 p-3.5">
                    <p class="text-xs font-medium text-gray-500">Légende</p>
                    <p id="modalCaption" class="mt-1 text-sm text-gray-700"></p>
                </div>
                @endif
                <div class="rounded-xl bg-gray-50 p-3.5">
                    <p class="text-xs font-medium text-gray-500 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Coordonnées GPS
                    </p>
                    <p id="modalCoords" class="mt-1 text-sm font-semibold text-gray-900"></p>
                    <p id="modalAddress" class="mt-1 text-xs text-gray-500 italic hidden">
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Recherche d'adresse...
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openPhotoModal(id, image, client, farm, technician, date, caption, lat, lng) {
        document.getElementById('modalImage').src = image;
        document.getElementById('modalClient').textContent = client;
        document.getElementById('modalFarm').textContent = farm;
        document.getElementById('modalTechnician').textContent = technician;
        document.getElementById('modalDate').textContent = date;

        const captionEl = document.getElementById('modalCaption');
        if (captionEl) captionEl.textContent = caption;

        const coordsEl = document.getElementById('modalCoords');
        const addressEl = document.getElementById('modalAddress');

        if (lat !== null && lng !== null) {
            coordsEl.textContent = lat + ', ' + lng;
            addressEl.classList.remove('hidden');

            // Appel API reverse geocoding
            fetch('{{ route('admin.gallery.geocode') }}?latitude=' + lat + '&longitude=' + lng)
                .then(r => r.json())
                .then(data => {
                    if (data.address) {
                        addressEl.classList.remove('italic');
                        addressEl.classList.add('text-gray-700');
                        addressEl.textContent = data.address;
                    } else {
                        addressEl.textContent = 'Adresse non disponible';
                    }
                })
                .catch(() => {
                    addressEl.textContent = 'Erreur de géolocalisation';
                });
        } else {
            coordsEl.textContent = 'Non renseignées';
            addressEl.classList.add('hidden');
        }

        const modal = document.getElementById('photoModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closePhotoModal() {
        const modal = document.getElementById('photoModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closePhotoModal();
    });

    function togglePhotos(button) {
        const container = button.closest('.mb-8');
        const hiddenPhotos = container.querySelectorAll('.photo-extra');
        const textSpan = button.querySelector('.btn-text');
        const icon = button.querySelector('svg');

        hiddenPhotos.forEach(photo => {
            photo.classList.toggle('hidden');
        });

        if (textSpan) {
            textSpan.textContent = hiddenPhotos[0]?.classList.contains('hidden') ? 'Voir plus' : 'Voir moins';
        }
        if (icon) {
            icon.style.transform = hiddenPhotos[0]?.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        }
    }
</script>
@endpush