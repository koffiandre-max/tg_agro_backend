<?php

namespace App\Livewire;

use App\Enums\StatutRapport;
use App\Models\RapportVisite;
use Livewire\Component;
use Livewire\WithPagination;

class RapportsVisiteTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statut = '';
    public string $typeVisite = '';
    public string $technicien = '';
    public string $client = '';
    public string $dateFrom = '';
    public string $dateTo = '';

    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingStatut()
    {
        $this->resetPage();
    }
    public function updatingTypeVisite()
    {
        $this->resetPage();
    }
    public function updatingTechnicien()
    {
        $this->resetPage();
    }
    public function updatingClient()
    {
        $this->resetPage();
    }
    public function updatingDateFrom()
    {
        $this->resetPage();
    }
    public function updatingDateTo()
    {
        $this->resetPage();
    }
    public function updatingPerPage()
    {
        $this->resetPage();
    }

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
        $this->reset(['search', 'statut', 'typeVisite', 'technicien', 'client', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function viewRapport($id): void
    {
        $this->dispatch('view-rapport', id: $id);
    }

    public function deleteRapport($id): void
    {
        RapportVisite::find($id)->delete();
        $this->dispatch('rapport-deleted');
    }

    public function render()
    {
        $query = RapportVisite::query()
            ->with(['technicien', 'client.user', 'farm'])
            ->withCount('photos')
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('localisation_parcelle', 'like', "%{$this->search}%")
                        ->orWhere('type_visite', 'like', "%{$this->search}%")
                        ->orWhereHas('technicien', function ($q) {
                            $q->where('name', 'like', "%{$this->search}%");
                        })
                        ->orWhereHas('client.user', function ($q) {
                            $q->where('name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->statut, fn($q) => $q->where('statut', $this->statut))
            ->when($this->typeVisite, fn($q) => $q->where('type_visite', $this->typeVisite))
            ->when($this->technicien, function ($q) {
                $q->whereHas('technicien', function ($q) {
                    $q->where('name', 'like', "%{$this->technicien}%");
                });
            })
            ->when($this->client, function ($q) {
                $q->whereHas('client.user', function ($q) {
                    $q->where('name', 'like', "%{$this->client}%");
                });
            })
            ->when($this->dateFrom, fn($q) => $q->whereDate('date_visite', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('date_visite', '<=', $this->dateTo))
            ->orderBy($this->sortField, $this->sortDirection);

        $rapports = $query->paginate($this->perPage);

        $statuts = collect(StatutRapport::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);

        return view('livewire.rapports-visite-table', [
            'rapports' => $rapports,
            'statuts' => $statuts,
        ]);
    }
}
