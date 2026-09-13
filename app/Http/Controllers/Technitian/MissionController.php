<?php

namespace App\Http\Controllers\Technitian;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\Mission;
use App\Models\Technician;
use App\Models\User;
use App\Services\SendmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MissionController extends Controller
{
    public function __construct(private SendmailService $mailer) {}

    public function index()
    {
        return view('technitian.missions.index');
    }

    // Partie Kanban des missions (désactivée)
    // public function kanban()
    // {
    //     return view('technitian.missions.kanban');
    // }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],
        ]);

        $user = Auth::user();
        $query = Mission::where('id', $id);

        if ($user && $user->role === 'technician') {
            $technicianId = Technician::where('user_id', $user->id)->value('id');
            $query->where('technician_id', $technicianId);
        }

        $mission = $query->firstOrFail();
        $mission->update(['status' => $validated['status']]);
        $this->syncCompletion($mission);

        if ($validated['status'] === 'completed') {
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

        return response()->json([
            'success' => true,
            'message' => 'Le statut de la mission a été mis à jour.',
        ]);
    }

    public function create()
    {
        $this->authorizeAdmin();

        $technicians = Technician::with('user:id,name')->orderBy('id')->get();
        $farms = Farm::orderBy('name')->get(['id', 'name']);

        return view('technitian.missions.create', compact('technicians', 'farms'));
    }

    public function show($id)
    {
        $mission = Mission::with(['farm', 'technician.user:id,name'])->findOrFail($id);

        $this->authorizeAccessible($mission);

        return view('technitian.missions.show', compact('mission'));
    }

    public function edit($id)
    {
        $this->authorizeAdmin();

        $mission = Mission::findOrFail($id);
        $technicians = Technician::with('user:id,name')->orderBy('id')->get();
        $farms = Farm::orderBy('name')->get(['id', 'name']);

        return view('technitian.missions.edit', compact('mission', 'technicians', 'farms'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $this->validated($request);

        $mission = Mission::create(array_merge($validated, [
            'technician_id' => $validated['technician_id'],
        ]));

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

        return redirect()->route('admin.technitian.missions.index')
            ->with('success', 'La mission a été créée avec succès.');
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';

        $data = $this->validated($request);

        if ($isAdmin) {
            $mission = Mission::findOrFail($id);
            $mission->update(array_merge($data, [
                'technician_id' => $data['technician_id'] ?? $mission->technician_id,
            ]));
        } else {
            $technician = Technician::where('user_id', $user->id)->firstOrFail();
            $mission = Mission::where('id', $id)
                ->where('technician_id', $technician->id)
                ->firstOrFail();

            $mission->update(array_merge($data, [
                'technician_id' => $technician->id,
            ]));
        }

        $this->syncCompletion($mission);

        if ($data['status'] === 'completed') {
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

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'La mission a été mise à jour avec succès.',
            ]);
        }

        return redirect()->route('admin.technitian.missions.show', $mission->id)
            ->with('success', 'La mission a été mise à jour avec succès.');
    }

    public function complete(Request $request, $id)
    {
        $technician = Technician::where('user_id', Auth::id())->firstOrFail();
        $mission = Mission::where('id', $id)
            ->where('technician_id', $technician->id)
            ->firstOrFail();

        $mission->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

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

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'La mission a été marquée comme terminée.',
            ]);
        }

        return redirect()->route('admin.technitian.missions.index')
            ->with('success', 'La mission a été marquée comme terminée.');
    }

    public function destroy($id)
    {
        $this->authorizeAdmin();

        $mission = Mission::findOrFail($id);
        $mission->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'La mission a été supprimée avec succès.',
            ]);
        }

        return redirect()->route('admin.technitian.missions.index')
            ->with('success', 'La mission a été supprimée avec succès.');
    }

    /**
     * Valide et retourne les données communes d'une mission.
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'technician_id' => ['nullable', 'exists:technicians,id'],
            'farm_id' => ['required', 'exists:farms,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'scheduled_date' => ['required', 'date'],
            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],
            'notes' => ['nullable', 'string'],
        ], [
            'farm_id.required' => 'Veuillez sélectionner une exploitation.',
            'farm_id.exists' => 'L\'exploitation sélectionnée est invalide.',
            'title.required' => 'Le titre de la mission est requis.',
            'scheduled_date.required' => 'La date planifiée est requise.',
            'status.required' => 'Veuillez sélectionner un statut.',
        ]);
    }

    /**
     * Met à jour completed_at en fonction du statut.
     */
    private function syncCompletion(Mission $mission): void
    {
        if ($mission->status === 'completed' && !$mission->completed_at) {
            $mission->update(['completed_at' => now()]);
        } elseif ($mission->status !== 'completed' && $mission->completed_at) {
            $mission->update(['completed_at' => null]);
        }
    }

    /**
     * Autorise uniquement les administrateurs.
     */
    private function authorizeAdmin(): void
    {
        if (auth()->user()?->role !== 'admin') {
            abort(403, 'Cette action est réservée aux administrateurs.');
        }
    }

    /**
     * Vérifie que l'utilisateur peut consulter la mission.
     */
    private function authorizeAccessible(Mission $mission): void
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return;
        }

        $technicianId = Technician::where('user_id', $user->id)->value('id');

        if ($mission->technician_id !== $technicianId) {
            abort(403, 'Accès non autorisé.');
        }
    }
}
