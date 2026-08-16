<?php

namespace App\Livewire;

use App\Models\Mission;
use App\Models\Technician;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TechnicianMissionsTable extends Component
{
    use WithPagination;

    public ?int $technicianId = null;
    public string $search = '';
    public string $status = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    public $confirmingMissionId = null;
    public string $confirmingMissionTitle = '';

    public function mount()
    {
        if ($this->technicianId === null) {
            $this->technicianId = Technician::where('user_id', Auth::id())->value('id');
        }
    }

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

    public function confirmDelete($id): void
    {
        $mission = Mission::where('id', $id)
            ->where('technician_id', $this->technicianId)
            ->first();

        if ($mission) {
            $this->confirmingMissionId = $mission->id;
            $this->confirmingMissionTitle = $mission->title;
        }
    }

    public function deleteMission(): void
    {
        if ($this->confirmingMissionId) {
            Mission::where('id', $this->confirmingMissionId)
                ->where('technician_id', $this->technicianId)
                ->delete();

            $this->confirmingMissionId = null;
            $this->confirmingMissionTitle = '';

            session()->flash('success', 'La mission a été supprimée avec succès.');
        }
    }

    public function render()
    {
        $missions = Mission::query()
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
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.technician-missions-table', [
            'missions' => $missions,
        ]);
    }
}
