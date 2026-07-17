<?php

namespace App\Livewire;

use App\Models\Permission;
use Livewire\Component;
use Livewire\WithPagination;

class PermissionsTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';
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

    public function resetFilters(): void
    {
        $this->reset(['search']);
        $this->resetPage();
    }

    public function viewPermission($id): void
    {
        $this->dispatch('view-permission', id: $id);
    }

    public function deletePermission($id): void
    {
        Permission::find($id)->delete();
        $this->dispatch('permission-deleted');
    }

    public function render()
    {
        $query = Permission::query()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('slug', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $permissions = $query->paginate($this->perPage);

        return view('livewire.permissions-table', [
            'permissions' => $permissions,
        ]);
    }
}
