<?php

namespace App\Livewire;

use App\Models\Mission;
use App\Models\Technician;
use App\Models\Farm;
use App\Models\User;
use App\Services\SendmailService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class MissionsKanban extends Component
{
    public function __construct(private ?SendmailService $mailer = null) {}
    public array $columns = [
        'pending' => ['label' => 'En attente', 'color' => 'bg-sky-50 border-sky-200', 'header' => 'bg-sky-100 text-sky-800', 'badge' => 'bg-sky-100 text-sky-700'],
        'in_progress' => ['label' => 'En cours', 'color' => 'bg-amber-50 border-amber-200', 'header' => 'bg-amber-100 text-amber-800', 'badge' => 'bg-amber-100 text-amber-700'],
        'completed' => ['label' => 'Terminée', 'color' => 'bg-emerald-50 border-emerald-200', 'header' => 'bg-emerald-100 text-emerald-800', 'badge' => 'bg-emerald-100 text-emerald-700'],
        'cancelled' => ['label' => 'Annulée', 'color' => 'bg-rose-50 border-rose-200', 'header' => 'bg-rose-100 text-rose-800', 'badge' => 'bg-rose-100 text-rose-700'],
    ];

    public array $missions = [];
    public ?int $technicianId = null;
    public bool $showCreateModal = false;
    public ?int $new_technician_id = null;
    public ?int $new_farm_id = null;
    public string $new_title = '';
    public string $new_description = '';
    public string $new_scheduled_date = '';
    public string $new_status = 'pending';
    public array $technicians = [];
    public array $farms = [];

    public function mount()
    {
        $user = Auth::user();

        if ($user && $user->role === 'admin') {
            $this->technicianId = null;
        } elseif ($this->technicianId === null) {
            $this->technicianId = Technician::where('user_id', Auth::id())->value('id');
        }

        $this->technicians = Technician::with('user')->get()->map(function ($technician) {
            return [
                'id' => $technician->id,
                'name' => $technician->user?->name ?? 'Technicien #' . $technician->id,
            ];
        })->toArray();

        $this->farms = Farm::select('id', 'name')->orderBy('name')->get()->toArray();

        $this->loadMissions();
    }

    public function loadMissions(): void
    {
        $query = Mission::query()
            ->with(['farm:id,name', 'technician.user:id,name'])
            ->orderBy('scheduled_date', 'asc');

        if ($this->technicianId !== null) {
            $query->where('technician_id', $this->technicianId);
        }

        $missions = $query->get(['id', 'title', 'description', 'scheduled_date', 'status', 'farm_id', 'technician_id', 'notes']);

        $grouped = [];
        foreach ($this->columns as $status => $_) {
            $grouped[$status] = [];
        }

        foreach ($missions as $mission) {
            $status = $mission->status ?: 'pending';
            if (!isset($grouped[$status])) {
                $grouped[$status] = [];
            }
            $grouped[$status][] = $mission;
        }

        $this->missions = $grouped;
    }

    public function updateMissionStatus($missionId, $newStatus): void
    {
        $user = Auth::user();
        $query = Mission::where('id', $missionId);

        if ($user && $user->role === 'technician') {
            $query->where('technician_id', $this->technicianId);
        }

        $mission = $query->firstOrFail();

        $mission->update([
            'status' => $newStatus,
            'completed_at' => $newStatus === 'completed' ? now() : ($mission->status === 'completed' && $newStatus !== 'completed' ? null : $mission->completed_at),
        ]);

        if ($newStatus === 'completed' && $this->mailer) {
            $admins = User::where('role', 'admin')
                ->where('is_active', true)
                ->get();

            foreach ($admins as $admin) {
                if ($admin->email) {
                    $this->mailer->sendView(
                        $admin->email,
                        'Mission terminée : ' . $mission->title,
                        'emails.missions.completed',
                        ['mission' => $mission]
                    );
                }
            }

            if ($mission->technician?->user?->email) {
                $this->mailer->sendView(
                    $mission->technician->user->email,
                    'Mission terminée : ' . $mission->title,
                    'emails.missions.completed',
                    ['mission' => $mission]
                );
            }

            if ($mission->farm?->user?->email) {
                $this->mailer->sendView(
                    $mission->farm->user->email,
                    'Mission terminée : ' . $mission->title,
                    'emails.missions.completed',
                    ['mission' => $mission]
                );
            }
        }

        $this->loadMissions();
    }

    public function createMission(): void
    {
        $user = Auth::user();

        if ($user?->role !== 'admin') {
            $this->addError('new_title', 'Action non autorisée.');
            return;
        }

        $validated = $this->validate([
            'new_technician_id' => ['required', 'exists:technicians,id'],
            'new_farm_id' => ['required', 'exists:farms,id'],
            'new_title' => ['required', 'string', 'max:255'],
            'new_description' => ['nullable', 'string'],
            'new_scheduled_date' => ['required', 'date'],
            'new_status' => ['required', Rule::in(array_keys($this->columns))],
        ], [
            'new_technician_id.required' => 'Veuillez sélectionner un technicien.',
            'new_farm_id.required' => 'Veuillez sélectionner une exploitation.',
            'new_title.required' => 'Le titre de la mission est requis.',
            'new_scheduled_date.required' => 'La date planifiée est requise.',
            'new_status.required' => 'Veuillez sélectionner un statut.',
        ]);

        Mission::create([
            'technician_id' => $validated['new_technician_id'],
            'farm_id' => $validated['new_farm_id'],
            'title' => $validated['new_title'],
            'description' => $validated['new_description'],
            'scheduled_date' => $validated['new_scheduled_date'],
            'status' => $validated['new_status'],
        ]);

        $mission = Mission::where('technician_id', $validated['new_technician_id'])
            ->where('farm_id', $validated['new_farm_id'])
            ->where('title', $validated['new_title'])
            ->latest()
            ->first();

        if ($this->mailer && $mission) {
            $admins = User::where('role', 'admin')
                ->where('is_active', true)
                ->get();

            foreach ($admins as $admin) {
                if ($admin->email) {
                    $this->mailer->sendView(
                        $admin->email,
                        'Nouvelle mission assignée : ' . $mission->title,
                        'emails.missions.assigned',
                        ['mission' => $mission]
                    );
                }
            }

            if ($mission->technician?->user?->email) {
                $this->mailer->sendView(
                    $mission->technician->user->email,
                    'Nouvelle mission assignée : ' . $mission->title,
                    'emails.missions.assigned',
                    ['mission' => $mission]
                );
            }

            if ($mission->farm?->user?->email) {
                $this->mailer->sendView(
                    $mission->farm->user->email,
                    'Nouvelle mission sur votre exploitation : ' . $mission->title,
                    'emails.missions.assigned',
                    ['mission' => $mission]
                );
            }
        }

        $this->reset(['new_technician_id', 'new_farm_id', 'new_title', 'new_description', 'new_scheduled_date', 'new_status']);
        $this->new_status = 'pending';
        $this->showCreateModal = false;

        $this->loadMissions();
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->reset(['new_technician_id', 'new_farm_id', 'new_title', 'new_description', 'new_scheduled_date', 'new_status']);
        $this->new_status = 'pending';
    }

    public function render()
    {
        return view('livewire.missions-kanban', []);
    }
}
