<?php

namespace App\Livewire;

use App\Data\ReportData;
use App\Models\Client;
use App\Models\Farm;
use App\Models\Report;
use App\Services\SendmailService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class ReportForm extends Component
{
    use WithFileUploads;

    public string $title = '';

    public string $type = 'inspection';

    public ?int $farm_id = null;

    public ?int $client_id = null;

    public ?int $technician_id = null;

    public $file;

    public ?string $file_path = null;

    public ?string $file_original_name = null;

    public ?int $file_size = null;

    public ?string $notes = null;

    public function mount()
    {
        $user = Auth::user();
        if ($user && $user->role === 'technician') {
            $this->technician_id = $user->id;
        }
    }

    public function submit()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:monthly,soil_analysis,harvest,other,inspection,diagnostic,suivi',
            'farm_id' => 'required|integer|exists:farms,id',
            'client_id' => 'required|integer|exists:clients,id',
            'file' => 'required|file|mimes:pdf|max:10240',
            'notes' => 'nullable|string|max:500',
        ], [
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'type.required' => 'Le type est obligatoire.',
            'type.in' => 'Le type sélectionné n\'est pas valide.',
            'farm_id.required' => 'Veuillez sélectionner une exploitation.',
            'farm_id.exists' => 'L\'exploitation sélectionnée n\'existe pas.',
            'client_id.required' => 'Veuillez sélectionner un client.',
            'client_id.exists' => 'Le client sélectionné n\'existe pas.',
            'file.required' => 'Veuillez sélectionner un fichier PDF.',
            'file.mimes' => 'Seuls les fichiers PDF sont autorisés.',
            'file.max' => 'Le fichier ne peut pas dépasser 10 Mo.',
        ]);

        $path = $this->file->store('reports', 'public');

        $report = Report::create([
            'title' => $this->title,
            'type' => $this->type,
            'farm_id' => $this->farm_id,
            'client_id' => $this->client_id,
            'technician_id' => $this->technician_id,
            'file_path' => $path,
            'file_original_name' => $this->file->getClientOriginalName(),
            'file_size' => $this->file->getSize(),
            'notes' => $this->notes,
            'status' => 'pending',
        ]);

        // Notification par e-mail à l'administrateur pour validation
        try {
            app(SendmailService::class)->sendView(
                env('ADMINSTOR_EMAIL'),
                'Nouveau rapport à valider : ' . $report->title,
                'emails.reports.submitted',
                ['report' => $report]
            );
        } catch (\Throwable $e) {
            logger()->error('Échec envoi e-mail rapport soumis', ['message' => $e->getMessage()]);
        }

        $this->reset(['file', 'title', 'type', 'farm_id', 'client_id', 'notes']);

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Rapport créé avec succès et en attente de validation.']);

        return $this->redirect(route('admin.technitian.reports.create'));
    }

    public function cancel()
    {
        return redirect()->route('admin.technitian.reports.create');
    }

    public function render()
    {
        $user = Auth::user();

        $farms = Farm::query();

        // Filtrer selon le rôle de l'utilisateur
        if ($user && $user->role === 'technician' && $user->technician) {
            // Technicien : montrer seulement ses farms assignées
            $farms = $farms->where('assigned_technician_id', $user->technician->id);
        }

        $farms = $farms->with(['user', 'clients.user'])
            ->orderBy('name')
            ->get();

        // Récupérer les clients associés aux farms (via la relation clients())
        $clientIds = $farms->flatMap(function ($farm) {
            return $farm->clients->pluck('id');
        })->unique();

        $clients = Client::query()
            ->whereIn('id', $clientIds)
            ->join('users', 'users.id', '=', 'clients.user_id')
            ->orderBy('users.name')
            ->select('clients.*')
            ->with('user')
            ->get();

        // Si aucun client trouvé via les farms, prendre tous les clients (pour l'admin)
        if ($clients->isEmpty()) {
            $clients = Client::query()
                ->join('users', 'users.id', '=', 'clients.user_id')
                ->orderBy('users.name')
                ->select('clients.*')
                ->with('user')
                ->get();
        }

        return view('livewire.report-form', [
            'farms' => $farms,
            'clients' => $clients,
        ]);
    }
}
