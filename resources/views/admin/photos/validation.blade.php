@extends('layouts.app')

@section('page-title', 'Validation des photos')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- En-tête --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Validation des photos</h1>
                <p class="mt-2 text-sm text-gray-600">Photos en attente de validation</p>
            </div>
            <div class="flex gap-3">
                <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-semibold text-yellow-800" id="pending-count">
                    {{ $photos->flatten()->count() }} photo(s) en attente
                </span>
            </div>
        </div>

        {{-- Grille de photos par exploitation --}}
        @if($photos->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @foreach($photos as $farmName => $farmPhotos)
                    @foreach($farmPhotos as $photo)
                        <div class="photo-card relative group rounded-2xl overflow-hidden border border-amber-200 bg-white shadow-sm hover:shadow-md transition-all" data-photo-id="{{ $photo->id }}">
                            <div class="aspect-square">
                                <img src="{{ asset('storage/' . $photo->photo_path) }}"
                                     alt="{{ $photo->caption ?? 'Photo' }}"
                                     class="w-full h-full object-cover"
                                     loading="lazy" />
                            </div>

                            {{-- Badge En attente --}}
                            <div class="absolute top-2 left-2">
                                <span class="inline-flex items-center rounded-full bg-amber-100 text-amber-800 px-2 py-0.5 text-xs font-semibold shadow-sm border border-amber-200">
                                    En attente
                                </span>
                            </div>

                            {{-- Badge date --}}
                            <div class="absolute top-2 right-2">
                                <span class="inline-flex items-center rounded-full bg-white/90 backdrop-blur-sm px-2 py-0.5 text-xs text-gray-500 shadow-sm border border-gray-200">
                                    {{ $photo->created_at?->format('d/m') ?? '' }}
                                </span>
                            </div>

                            {{-- Actions --}}
                            <div class="absolute inset-x-0 bottom-0 p-3 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                <div class="flex items-center justify-center gap-3">
                                    <button type="button" onclick="approvePhoto({{ $photo->id }}, this)" class="h-10 w-10 rounded-full bg-emerald-600/90 text-white flex items-center justify-center hover:bg-emerald-700 transition-colors shadow-lg backdrop-blur-sm" title="Valider">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/>
                                        </svg>
                                    </button>
                                    <button type="button" onclick="rejectPhoto({{ $photo->id }}, this)" class="h-10 w-10 rounded-full bg-rose-600/90 text-white flex items-center justify-center hover:bg-rose-700 transition-colors shadow-lg backdrop-blur-sm" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Click overlay for details --}}
                            <div class="absolute inset-0 cursor-pointer z-0" onclick="openPhotoModal({{ $photo->id }}, '{{ asset('storage/' . $photo->photo_path) }}', '{{ addslashes($photo->client?->user?->name ?? 'Client') }}', '{{ addslashes($photo->farm?->name ?? 'Plantation') }}', '{{ addslashes($photo->technician?->name ?? '—') }}', '{{ $photo->created_at?->format('d/m/Y H:i') ?? '' }}', '{{ $photo->caption ?? '' }}', {{ $photo->latitude ?? 'null' }}, {{ $photo->longitude ?? 'null' }})"></div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        @else
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/>
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">Aucune photo en attente</h3>
                <p class="mt-1 text-sm text-gray-500">Toutes les photos ont été validées.</p>
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

    async function approvePhoto(id, button) {
        try {
            const response = await fetch('/photos/' + id + '/approve', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                window.location.reload();
                return;
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                window.location.reload();
                return;
            }

            const data = await response.json();

            if (data.success) {
                const card = button.closest('.photo-card');
                card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                setTimeout(() => card.remove(), 300);

                const countEl = document.getElementById('pending-count');
                if (countEl) {
                    let count = parseInt(countEl.textContent) || 0;
                    countEl.textContent = Math.max(0, count - 1) + ' photo(s) en attente';
                }
            } else {
                alert('Erreur lors de la validation.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Erreur lors de la validation.');
        }
    }

    async function rejectPhoto(id, button) {
        if (!confirm('Supprimer cette photo ?')) return;

        try {
            const response = await fetch('/photos/' + id + '/reject', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                window.location.reload();
                return;
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                window.location.reload();
                return;
            }

            const data = await response.json();

            if (data.success) {
                const card = button.closest('.photo-card');
                card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                setTimeout(() => card.remove(), 300);

                const countEl = document.getElementById('pending-count');
                if (countEl) {
                    let count = parseInt(countEl.textContent) || 0;
                    countEl.textContent = Math.max(0, count - 1) + ' photo(s) en attente';
                }
            } else {
                alert('Erreur lors de la suppression.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Erreur lors de la suppression.');
        }
    }
</script>
@endpush
