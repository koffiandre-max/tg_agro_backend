View [admin.farms.index] not found. # Intégration Livewire et Datatables - TG'AGRO Backend

## 📦 Installation

Livewire a été installé avec succès dans le projet :

```bash
composer require livewire/livewire
```

**Version installée** : Livewire 4.3

## 🎯 Composants créés

### 1. FarmsTable - Datatable des exploitations agricoles

**Fichiers créés :**

- `app/Livewire/FarmsTable.php` - Composant Livewire avec logique de filtrage
- `resources/views/livewire/farms-table.blade.php` - Vue du datatable
- `resources/views/livewire/partials/sort-icon.blade.php` - Icône de tri
- `resources/views/admin/farms/datatable.blade.php` - Page d'intégration

## ✨ Fonctionnalités du datatable

### Recherche et filtres

- **Recherche textuelle** : Par nom, localisation ou type de culture
- **Filtre par statut** : Active, Inactive, En jachère
- **Filtre par type de culture** : Liste dynamique des cultures existantes
- **Filtre par client** : Liste des clients avec exploitations
- **Filtre par date** : Plage de dates de création
- **Réinitialisation** : Bouton pour effacer tous les filtres

### Tri et pagination

- **Tri par colonnes** : Cliquez sur les en-têtes pour trier
- **Pagination** : Navigation entre les pages
- **Lignes par page** : Choix entre 10, 25, 50, 100 lignes

### URL synchronisée

- Tous les filtres sont sauvegardés dans l'URL
- Les liens sont partageables
- L'état est conservé lors du rafraîchissement

## 🚀 Utilisation

### Accéder au datatable

**Route** : `/farms/datatable`

**Nom de route** : `admin.farms.datatable`

### Intégrer le composant dans une vue

```blade
@extends('layouts.app')

@section('content')
    @livewire('farms-table')
@endsection

@push('scripts')
@livewireScripts
@endpush

@push('styles')
@livewireStyles
@endpush
```

## 🔧 Personnalisation

### Ajouter une colonne triable

1. Dans `FarmsTable.php`, ajouter la colonne dans le `orderBy()` :

```php
->orderBy($this->sortField, $this->sortDirection)
```

2. Dans `farms-table.blade.php`, ajouter le `<th>` :

```blade
<th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('nom_colonne')">
    <div class="flex items-center gap-1">Titre @include('livewire.partials.sort-icon', ['field' => 'nom_colonne'])</div>
</th>
```

### Ajouter un filtre

1. Dans `FarmsTable.php`, ajouter une propriété publique :

```php
#[Livewire\Attributes\Url]
public string $monFiltre = '';
```

2. Ajouter la méthode de reset :

```php
public function updatingMonFiltre() { $this->resetPage(); }
```

3. Dans `render()`, ajouter le filtre :

```php
->when($this->monFiltre, fn ($q) => $q->where('colonne', $this->monFiltre))
```

4. Dans la vue, ajouter le dropdown :

```blade
<div class="relative">
    <button @click="openFilter = openFilter === 'monFiltre' ? null : 'monFiltre'">
        Mon Filtre
    </button>
    <div x-show="openFilter === 'monFiltre'" x-cloak>
        @foreach($options as $value => $label)
            <button wire:click="$set('monFiltre', '{{ $value }}')">
                {{ $label }}
            </button>
        @endforeach
    </div>
</div>
```

## 📊 Créer d'autres datatables

### Template pour un nouveau composant

**1. Créer le composant Livewire** (`app/Livewire/NomTable.php`) :

```php
<?php

namespace App\Livewire;

use App\Models\NomModel;
use Livewire\Component;
use Livewire\WithPagination;

class NomTable extends Component
{
    use WithPagination;

    #[Livewire\Attributes\Url]
    public string $search = '';

    #[Livewire\Attributes\Url]
    public string $status = '';

    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'status']);
        $this->resetPage();
    }

    public function render()
    {
        $query = NomModel::query()
            ->when($this->search, function ($q) {
                $q->where('nom_colonne', 'like', "%{$this->search}%");
            })
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->orderBy($this->sortField, $this->sortDirection);

        $items = $query->paginate($this->perPage);

        return view('livewire.nom-table', [
            'items' => $items,
        ]);
    }
}
```

**2. Créer la vue** (`resources/views/livewire/nom-table.blade.php`) :

```blade
<div class="p-6 bg-gray-50 min-h-screen" x-data="{ openFilter: null }" @click.away="openFilter = null">
    <!-- Barre de recherche + filtres -->
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <!-- Recherche -->
        <div class="relative flex-1 min-w-[240px]">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Rechercher..."
                class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg bg-white"
            >
        </div>

        <!-- Filtres... -->

        <!-- Réinitialiser -->
        @if($search || $status)
            <button wire:click="resetFilters" class="text-sm text-indigo-600 hover:underline">
                Réinitialiser
            </button>
        @endif
    </div>

    <!-- Tableau -->
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/60 text-left text-gray-500">
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('nom_colonne')">
                            Colonne 1 @include('livewire.partials.sort-icon', ['field' => 'nom_colonne'])
                        </th>
                        <!-- Autres colonnes... -->
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50/60" wire:key="item-{{ $item->id }}">
                            <td class="px-4 py-3">{{ $item->nom_colonne }}</td>
                            <!-- Autres cellules... -->
                        </tr>
                    @empty
                        <tr>
                            <td colspan="X" class="px-4 py-10 text-center text-gray-400">
                                Aucun élément trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer : pagination -->
        <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-t border-gray-100 text-sm text-gray-500">
            <div>
                Affichage {{ $items->firstItem() ?? 0 }}-{{ $items->lastItem() ?? 0 }} sur {{ $items->total() }}
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
                {{ $items->links() }}
            </div>
        </div>
    </div>
</div>
```

**3. Créer la route** :

```php
Route::get('/nom/datatable', function () {
    return view('admin.nom.datatable');
})->name('admin.nom.datatable');
```

**4. Créer la page d'intégration** (`resources/views/admin/nom/datatable.blade.php`) :

```blade
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-900">Titre</h1>
        @livewire('nom-table')
    </div>
</div>
@endsection

@push('scripts')
@livewireScripts
@endpush

@push('styles')
@livewireStyles
@endpush
```

## 🎨 Styles et assets

### Tailwind CSS

Le projet utilise Tailwind CSS via Vite. Les classes utilitaires sont directement dans les vues.

### Alpine.js

Alpine.js est déjà chargé dans le layout principal pour les interactions UI (dropdowns, modales).

### Livewire

Livewire est automatiquement chargé via :

- `@livewireStyles` dans le `<head>`
- `@livewireScripts` avant la fermeture du `</body>`

## 📝 Notes importantes

### Namespace des modèles

Le projet utilise un namespace personnalisé pour les modèles :

```php
use Modules\Portail\Models\Farm;
use Modules\Portail\Models\Client;
// etc.
```

### Relations Eloquent

Assurez-vous que les relations sont définies dans les modèles pour les filtres avancés :

```php
// Exemple dans Farm.php
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

### Performance

- Les requêtes utilisent `with()` pour éviter le N+1
- Les filtres sont appliqués côté serveur (SQL)
- La pagination limite le nombre de résultats

## 🧪 Tests

Pour tester le composant Livewire :

```php
use App\Livewire\FarmsTable;
use Livewire\Livewire;

test('farms table can search', function () {
    Livewire::test(FarmsTable::class)
        ->set('search', 'Test Farm')
        ->assertSee('Test Farm');
});
```

## 📚 Ressources

- [Documentation Livewire](https://livewire.laravel.com/)
- [Livewire 3 Migration Guide](https://livewire.laravel.com/docs/3.x/upgrade)
- [Spatie Laravel Data](https://spatie.be/docs/laravel-data)

## 🔄 Prochaines étapes

1. **Créer d'autres datatables** :
    - ClientsTable
    - TechniciansTable
    - ReportsTable
    - PhotosTable
    - MessagesTable

2. **Ajouter des fonctionnalités** :
    - Export CSV/Excel
    - Actions en masse
    - Modales de détails
    - Graphiques et statistiques

3. **Optimisations** :
    - Cache des listes déroulantes
    - Debounce sur la recherche
    - Lazy loading des relations

## ⚠️ Points d'attention

- Livewire 4.x utilise des attributs PHP 8 (`#[Url]`)
- Les composants doivent être enregistrés (auto-découverts dans `app/Livewire`)
- Les vues doivent être dans `resources/views/livewire/`
- Utiliser `wire:model.live` pour les mises à jour en temps réel
- Utiliser `wire:key` pour optimiser le rendu des listes
