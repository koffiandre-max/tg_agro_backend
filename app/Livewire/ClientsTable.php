<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Farm;
use App\Models\Technician;
use App\Models\User;
use App\Services\SendmailService;
use Livewire\Component;
use Livewire\WithPagination;

class ClientsTable extends Component
{
    use WithPagination;

    public function __construct(private ?SendmailService $mailer = null) {}

    public string $search = '';

    public string $code = '';

    public string $subscriptionType = '';

    public string $countryOfResidence = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    public ?int $selectedClientId = null;

    public string $selectedClientName = '';

    public ?int $selectedTechnicianId = null;

    public bool $showAssignTechnicianModal = false;

    public $technicians;

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
        $this->reset(['search', 'code', 'subscriptionType', 'countryOfResidence']);
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

    public function assignTechnicianToClient($id): void
    {
        $client = Client::with(['user', 'assignedFarms'])->findOrFail($id);
        $this->selectedClientId = $client->id;
        $this->selectedClientName = $client->user?->name ?? 'Client #' . $client->id;

        $currentTechnicianId = null;
        if ($client->farms()->whereNotNull('assigned_technician_id')->exists()) {
            $currentTechnicianId = $client->farms()->whereNotNull('assigned_technician_id')->value('assigned_technician_id');
        }
        $this->selectedTechnicianId = $currentTechnicianId;
        $this->showAssignTechnicianModal = true;
    }

    public function saveTechnicianAssignment($clientId = null, $technicianId = null): void
    {
        try {
            $this->selectedClientId = $clientId;
            $this->selectedTechnicianId = $technicianId ?: null;

            $this->validate([
                'selectedTechnicianId' => 'nullable|integer|exists:technicians,id',
            ]);

            $client = Client::with(['user', 'assignedFarms'])->findOrFail($this->selectedClientId);

            $ownedFarmIds = $client->farms()->pluck('id')->toArray();
            $assignedFarmIds = $client->assignedFarms()->pluck('id')->toArray();
            $allFarmIds = array_unique(array_merge($ownedFarmIds, $assignedFarmIds));

            if (empty($allFarmIds)) {
                session()->flash('error', 'Ce client n\'est lié à aucune exploitation : l\'assignation n\'a pas été enregistrée.');
                return;
            }

            Farm::whereIn('id', $allFarmIds)->update([
                'assigned_technician_id' => $this->selectedTechnicianId,
            ]);

            $technicianName = $this->selectedTechnicianId
                ? Technician::find($this->selectedTechnicianId)?->user?->name
                : 'Aucun';

            if ($this->mailer) {
                $admins = User::where('role', 'admin')
                    ->where('is_active', true)
                    ->get();

                foreach ($admins as $admin) {
                    if ($admin->email) {
                        $this->mailer->sendView(
                            $admin->email,
                            'Technicien assigné au client : ' . ($client->user?->name ?? 'Client'),
                            'emails.clients.technician_assigned',
                            ['client' => $client, 'technicianName' => $technicianName]
                        );
                    }
                }

                if ($client->user?->email) {
                    $this->mailer->sendView(
                        $client->user->email,
                        'Technicien assigné à votre compte : ' . ($technicianName ?? 'Aucun'),
                        'emails.clients.technician_assigned',
                        ['client' => $client, 'technicianName' => $technicianName]
                    );
                }

                if ($this->selectedTechnicianId) {
                    $technician = Technician::with('user')->find($this->selectedTechnicianId);
                    if ($technician?->user?->email) {
                        $this->mailer->sendView(
                            $technician->user->email,
                            'Vous avez été assigné au client : ' . ($client->user?->name ?? 'Client'),
                            'emails.clients.technician_assigned',
                            ['client' => $client, 'technicianName' => $technicianName]
                        );
                    }
                }
            }

            session()->flash('success', 'Le technicien a été assigné au client avec succès.');
        } catch (\Throwable $e) {
            logger()->error('Échec assignation technicien client', [
                'client_id' => $clientId,
                'technician_id' => $technicianId,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', 'Erreur lors de l\'assignation du technicien.');
        } finally {
            $this->showAssignTechnicianModal = false;
            $this->selectedClientId = null;
            $this->selectedClientName = '';
            $this->selectedTechnicianId = null;
        }
    }

    public function closeAssignTechnicianModal(): void
    {
        $this->showAssignTechnicianModal = false;
        $this->selectedClientId = null;
        $this->selectedClientName = '';
        $this->selectedTechnicianId = null;
    }

    public function deleteClient($id): void
    {
        Client::find($id)->delete();
        $this->dispatch('client-deleted');
    }

    public function render()
    {
        $query = Client::query()
            ->with(['user', 'farms.assignedTechnician'])
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

        $this->technicians = Technician::with('user')->orderBy('id')->get();

        return view('livewire.clients-table', [
            'clients' => $clients,
            'countries' => $countries,
            'technicians' => $this->technicians,
        ]);
    }
}
