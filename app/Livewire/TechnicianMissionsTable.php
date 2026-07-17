<?php

namespace App\Livewire;

use App\Models\Mission;
use Livewire\Component;
use Livewire\WithPagination;

class TechnicianMissionsTable extends Component
{
    use WithPagination;

    public int $technicianId;
    public string $search = '';
    public string $status = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
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
        $this->reset(['search', 'status']);
        $this->resetPage();
    }

    public function viewMission($id): void
    {
        $this->dispatch('view-mission', id: $id);
    }

    public function render()
    {
        $query = Mission::query()
            ->where('technician_id', $this->technicianId)
            ->with('farm')
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%")
                        ->orWhereHas('farm', function ($q) {
                            $q->where('name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy($this->sortField, $this->sortDirection);

        $missions = $query->paginate($this->perPage);

        return view('livewire.technician-missions-table', [
            'missions' => $missions,
        ]);
    }
}
