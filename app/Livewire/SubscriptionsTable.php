<?php

namespace App\Livewire;

use App\Models\Subscription;
use Livewire\Component;
use Livewire\WithPagination;

class SubscriptionsTable extends Component
{
    use WithPagination;

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

    public function render()
    {
        $query = Subscription::query()
            ->with('user')
            ->when($this->search, function ($q) {
                $q->whereHas('user', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                })
                    ->orWhere('payment_reference', 'like', "%{$this->search}%");
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->type, fn($q) => $q->where('type', $this->type))
            ->orderBy($this->sortField, $this->sortDirection);

        $subscriptions = $query->paginate($this->perPage);

        return view('livewire.subscriptions-table', [
            'subscriptions' => $subscriptions,
        ]);
    }
}
