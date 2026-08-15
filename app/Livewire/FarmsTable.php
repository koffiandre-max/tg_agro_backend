<?php

namespace App\Livewire;

use App\Models\Farm;
use App\Models\Technician;
use Livewire\Component;
use Livewire\WithPagination;

class FarmsTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public string $cultureType = '';

    public string $client = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    public ?int $selectedFarmId = null;
    public string $selectedFarmName = '';
    public ?int $selectedTechnicianId = null;
    public bool $showAssignModal = false;
    public $technicians;

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingStatus()
    {
        $this->resetPage();
    }
    public function updatingCultureType()
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
        $this->reset(['search', 'status', 'cultureType', 'client', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function viewFarm($id): void
    {
        $this->dispatch('view-farm', id: $id);
    }

    public function assignTechnician($id): void
    {
        $farm = Farm::findOrFail($id);
        $this->selectedFarmId = $farm->id;
        $this->selectedFarmName = $farm->name;
        $this->selectedTechnicianId = $farm->assigned_technician_id;
        $this->showAssignModal = true;
    }

    public function assignTechnicianToFarm(): void
    {
        $this->validate([
            'selectedTechnicianId' => 'nullable|exists:technicians,id',
        ]);

        $farm = Farm::findOrFail($this->selectedFarmId);
        $farm->update([
            'assigned_technician_id' => $this->selectedTechnicianId,
        ]);

        $this->showAssignModal = false;
        $this->selectedFarmId = null;
        $this->selectedFarmName = '';
        $this->selectedTechnicianId = null;

        session()->flash('success', 'Le technicien a été assigné à l\'exploitation avec succès.');
    }

    public function closeAssignModal(): void
    {
        $this->showAssignModal = false;
        $this->selectedFarmId = null;
        $this->selectedFarmName = '';
        $this->selectedTechnicianId = null;
    }

    public function deleteFarm($id): void
    {
        Farm::find($id)->delete();
        $this->dispatch('farm-deleted');
    }

    public function render()
    {
        $query = Farm::query()
            ->with('user')
            ->withCount('photos')
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('location', 'like', "%{$this->search}%")
                        ->orWhere('culture_type', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->cultureType, fn($q) => $q->where('culture_type', $this->cultureType))
            ->when($this->client, function ($q) {
                $q->whereHas('user', function ($q) {
                    $q->where('name', 'like', "%{$this->client}%");
                });
            })
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy($this->sortField, $this->sortDirection);

        $farms = $query->paginate($this->perPage);

        $clients = Farm::query()
            ->with('user')
            ->whereHas('user')
            ->get()
            ->unique('user_id')
            ->pluck('user.name', 'user_id');

        $cultureTypes = Farm::query()
            ->distinct()
            ->pluck('culture_type')
            ->filter()
            ->sort()
            ->values();

        $this->technicians = Technician::with('user')->orderBy('id')->get();

        return view('livewire.farms-table', [
            'farms' => $farms,
            'clients' => $clients,
            'cultureTypes' => $cultureTypes,
            'technicians' => $this->technicians,
        ]);
    }
}
