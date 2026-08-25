<?php

namespace App\Livewire;

use App\Models\Farm;
use App\Models\Technician;
use App\Models\User;
use App\Services\SendmailService;
use Livewire\Component;
use Livewire\WithPagination;

class FarmsTable extends Component
{
    use WithPagination;

    public function __construct(private ?SendmailService $mailer = null) {}

    public string $search = '';

    public string $status = '';

    public string $type = '';

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
    public function updatingType()
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
        $this->reset(['search', 'status', 'type', 'cultureType', 'client', 'dateFrom', 'dateTo']);
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

    public function assignTechnicianToFarm($farmId = null, $technicianId = null): void
    {
        $this->selectedFarmId = $farmId;
        $this->selectedTechnicianId = $technicianId ?: null;

        $this->validate([
            'selectedTechnicianId' => 'nullable|exists:technicians,id',
        ]);

        $farm = Farm::findOrFail($this->selectedFarmId);
        $farm->update([
            'assigned_technician_id' => $this->selectedTechnicianId,
        ]);

        $technicianName = $farm->assignedTechnician?->user?->name ?? 'Aucun';

        if ($this->mailer) {
            $admins = User::where('role', 'admin')
                ->where('is_active', true)
                ->get();

            foreach ($admins as $admin) {
                if ($admin->email) {
                    $this->mailer->sendView(
                        $admin->email,
                        'Technicien assigné à l\'exploitation : ' . $farm->name,
                        'emails.farms.technician_assigned',
                        ['farm' => $farm, 'technicianName' => $technicianName]
                    );
                }
            }

            if ($farm->user?->email) {
                $this->mailer->sendView(
                    $farm->user->email,
                    'Technicien assigné à votre exploitation : ' . $farm->name,
                    'emails.farms.technician_assigned',
                    ['farm' => $farm, 'technicianName' => $technicianName]
                );
            }

            if ($farm->assignedTechnician?->user?->email) {
                $this->mailer->sendView(
                    $farm->assignedTechnician->user->email,
                    'Vous avez été assigné à l\'exploitation : ' . $farm->name,
                    'emails.farms.technician_assigned',
                    ['farm' => $farm, 'technicianName' => $technicianName]
                );
            }
        }

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
            ->when($this->type, fn($q) => $q->where('type', $this->type))
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
