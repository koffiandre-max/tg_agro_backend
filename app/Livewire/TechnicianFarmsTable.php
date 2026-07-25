<?php

namespace App\Livewire;

use App\Models\Farm;
use App\Models\Technician;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TechnicianFarmsTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    public function updatingSearch()
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

    public function render()
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->firstOrFail();

        $query = Farm::query()
            ->with(['user'])
            ->where('assigned_technician_id', $technician->id)
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('location', 'like', "%{$this->search}%")
                        ->orWhere('culture_type', 'like', "%{$this->search}%");
                });
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $farms = $query->paginate($this->perPage);

        return view('livewire.technician-farms-table', [
            'farms' => $farms,
            'technician' => $technician,
        ]);
    }
}
