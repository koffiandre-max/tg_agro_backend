<?php

namespace App\Livewire;

use App\Models\DataEntry;
use App\Models\Farm;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DataEntriesTable extends Component
{
    use WithPagination;

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

    public function viewEntry($id): void
    {
        $this->dispatch('view-data-entry', id: $id);
    }

    public function deleteEntry($id): void
    {
        DataEntry::findOrFail($id)->delete();
        $this->dispatch('data-entry-deleted');
    }

    public function render()
    {
        $technicianId = \App\Models\Technician::where('user_id', Auth::id())->value('id');

        $query = DataEntry::query()
            ->with(['farm', 'client', 'technician'])
            ->where('technician_id', $technicianId)
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('crop_stage', 'like', "%{$this->search}%")
                        ->orWhere('observations', 'like', "%{$this->search}%")
                        ->orWhere('weather_conditions', 'like', "%{$this->search}%")
                        ->orWhereHas('farm', function ($q) {
                            $q->where('name', 'like', "%{$this->search}%");
                        })
                        ->orWhereHas('client', function ($q) {
                            $q->where('code', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy($this->sortField, $this->sortDirection);

        $entries = $query->paginate($this->perPage);

        return view('livewire.data-entries-table', [
            'entries' => $entries,
        ]);
    }
}
