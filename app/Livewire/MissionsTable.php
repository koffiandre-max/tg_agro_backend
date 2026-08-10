<?php

namespace App\Livewire;

use App\Models\Mission;
use App\Models\Technician;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MissionsTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public string $sortField = 'scheduled_date';

    public string $sortDirection = 'asc';

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

    public function render()
    {
        $user = Auth::user();
        $isAdmin = $user && $user->role === 'admin';

        $query = Mission::query()
            ->with(['farm:id,name', 'technician.user:id,name'])
            ->when(!$isAdmin, function ($q) use ($user) {
                $technicianId = Technician::where('user_id', $user->id)->value('id');
                $q->where('technician_id', $technicianId);
            })
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%")
                        ->orWhereHas('farm', function ($q) {
                            $q->where('name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->orderBy($this->sortField, $this->sortDirection);

        $missions = $query->paginate($this->perPage);

        return view('livewire.missions-table', [
            'missions' => $missions,
            'isAdmin' => $isAdmin,
        ]);
    }
}
