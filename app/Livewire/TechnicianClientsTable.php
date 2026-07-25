<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Farm;
use App\Models\Technician;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TechnicianClientsTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $code = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCode()
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
        $this->reset(['search', 'code']);
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->firstOrFail();

        $farmIds = Farm::where('assigned_technician_id', $technician->id)->pluck('id');

        $clientIds = Client::whereIn('user_id', function ($query) use ($farmIds) {
                $query->select('user_id')->from('farms')->whereIn('id', $farmIds);
            })
            ->orWhereHas('assignedFarms', function ($query) use ($farmIds) {
                $query->whereIn('farms.id', $farmIds);
            })
            ->distinct()
            ->pluck('id');

        $query = Client::query()
            ->with(['user', 'farms', 'assignedFarms'])
            ->whereIn('id', $clientIds)
            ->when($this->search, function ($q) {
                $q->whereHas('user', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                })
                    ->orWhere('country_of_residence', 'like', "%{$this->search}%")
                    ->orWhere('country_of_origin', 'like', "%{$this->search}%")
                    ->orWhere('code', 'like', "%{$this->search}%");
            })
            ->when($this->code, fn($q) => $q->where('code', 'like', "%{$this->code}%"))
            ->orderBy($this->sortField, $this->sortDirection);

        $clients = $query->paginate($this->perPage);

        return view('livewire.technician-clients-table', [
            'clients' => $clients,
            'technician' => $technician,
        ]);
    }
}
