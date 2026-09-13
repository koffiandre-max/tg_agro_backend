<?php

namespace App\Livewire;

use App\Models\Report;
use Livewire\Component;
use Livewire\WithPagination;

class TechnicianReportsTable extends Component
{
    use WithPagination;

    public int $userId;
    public string $search = '';
    public string $status = '';
    public string $type = '';
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

    public function updatingType()
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
        $this->reset(['search', 'status', 'type']);
        $this->resetPage();
    }

    public function viewReport($id): void
    {
        $this->dispatch('view-report', id: $id);
    }

    public function render()
    {
        $query = Report::query()
            ->where('technician_id', $this->userId)
            ->with(['farm', 'client'])
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhereHas('farm', function ($q) {
                            $q->where('name', 'like', "%{$this->search}%");
                        })
                        ->orWhereHas('client', function ($q) {
                            $q->where('code', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->type, fn($q) => $q->where('type', $this->type))
            ->orderBy($this->sortField, $this->sortDirection);

        $reports = $query->paginate($this->perPage);

        $reportTypes = Report::query()
            ->where('technician_id', $this->userId)
            ->distinct()
            ->pluck('type')
            ->filter()
            ->sort()
            ->values();

        return view('livewire.technician-reports-table', [
            'reports' => $reports,
            'reportTypes' => $reportTypes,
        ]);
    }
}
