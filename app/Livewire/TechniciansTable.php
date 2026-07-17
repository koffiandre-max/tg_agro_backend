<?php

namespace App\Livewire;

use App\Models\Technician;
use Livewire\Component;
use Livewire\WithPagination;

class TechniciansTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $availability = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingAvailability()
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
        $this->reset(['search', 'availability']);
        $this->resetPage();
    }

    public function viewTechnician($id): void
    {
        $this->dispatch('view-technician', id: $id);
    }

    public function viewMissions($id): void
    {
        $this->dispatch('view-technician-missions', id: $id);
    }

    public function viewReports($id): void
    {
        $this->dispatch('view-technician-reports', id: $id);
    }

    public function deleteTechnician($id): void
    {
        Technician::find($id)->delete();
        $this->dispatch('technician-deleted');
    }

    public function render()
    {
        $query = Technician::query()
            ->with('user')
            ->withCount('missions')
            ->withCount(['reports' => function ($q) {
                $q->whereNotNull('technician_id');
            }])
            ->addSelect('technicians.*')
            ->when($this->search, function ($q) {
                $q->whereHas('user', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                })
                    ->orWhere('location_base', 'like', "%{$this->search}%")
                    ->orWhere('phone_secondary', 'like', "%{$this->search}%");
            })
            ->when($this->availability !== '', fn($q) => $q->where('is_available', $this->availability === 'available'))
            ->orderBy($this->sortField, $this->sortDirection);

        $technicians = $query->paginate($this->perPage);

        return view('livewire.technicians-table', [
            'technicians' => $technicians,
        ]);
    }
}
