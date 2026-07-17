<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;

class ClientsTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $subscriptionType = '';
    public string $countryOfResidence = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingSubscriptionType()
    {
        $this->resetPage();
    }
    public function updatingCountryOfResidence()
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
        $this->reset(['search', 'subscriptionType', 'countryOfResidence']);
        $this->resetPage();
    }

    public function viewClient($id): void
    {
        $this->dispatch('view-client', id: $id);
    }

    public function viewFarms($id): void
    {
        $this->dispatch('view-client-farms', id: $id);
    }

    public function deleteClient($id): void
    {
        Client::find($id)->delete();
        $this->dispatch('client-deleted');
    }

    public function render()
    {
        $query = Client::query()
            ->with('user')
            ->when($this->search, function ($q) {
                $q->whereHas('user', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                })
                    ->orWhere('country_of_residence', 'like', "%{$this->search}%")
                    ->orWhere('country_of_origin', 'like', "%{$this->search}%");
            })
            ->when($this->subscriptionType, fn($q) => $q->where('subscription_type', $this->subscriptionType))
            ->when($this->countryOfResidence, fn($q) => $q->where('country_of_residence', $this->countryOfResidence))
            ->orderBy($this->sortField, $this->sortDirection);

        $clients = $query->paginate($this->perPage);

        $countries = Client::query()
            ->distinct()
            ->pluck('country_of_residence')
            ->filter()
            ->sort()
            ->values();

        return view('livewire.clients-table', [
            'clients' => $clients,
            'countries' => $countries,
        ]);
    }
}
