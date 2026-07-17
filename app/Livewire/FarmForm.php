<?php

namespace App\Livewire;

use App\Models\Farm;
use App\Models\User;
use Livewire\Component;

class FarmForm extends Component
{
    public ?int $farmId = null;

    public string $name = '';

    public string $location = '';

    public ?float $latitude = null;

    public ?float $longitude = null;

    public ?float $total_area_hectares = null;

    public string $culture_type = '';

    public string $status = 'active';

    public ?string $expected_harvest_date = null;

    public ?string $crop_stage = null;

    public int $crop_stage_progress = 0;

    public ?string $last_visit_date = null;

    public ?string $notes = null;

    public ?int $user_id = null;

    public bool $isEdit = false;

    public function mount(?int $farmId = null)
    {
        $this->farmId = $farmId;

        if ($farmId) {
            $this->isEdit = true;
            $farm = Farm::with('user')->findOrFail($farmId);

            $this->name = $farm->name;
            $this->location = $farm->location;
            $this->latitude = $farm->latitude;
            $this->longitude = $farm->longitude;
            $this->total_area_hectares = $farm->total_area_hectares;
            $this->culture_type = $farm->culture_type;
            $this->status = $farm->status;
            $this->expected_harvest_date = $farm->expected_harvest_date?->format('Y-m-d');
            $this->crop_stage = $farm->crop_stage;
            $this->crop_stage_progress = $farm->crop_stage_progress;
            $this->last_visit_date = $farm->last_visit_date?->format('Y-m-d');
            $this->notes = $farm->notes;
            $this->user_id = $farm->user_id;
        }
    }

    public function submit()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'total_area_hectares' => 'required|numeric|min:0',
            'culture_type' => 'required|string|max:255',
            'status' => 'required|in:active,inactive,fallow',
            'expected_harvest_date' => 'nullable|date',
            'crop_stage' => 'nullable|string|max:100',
            'crop_stage_progress' => 'nullable|integer|min:0|max:100',
            'last_visit_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ];

        $this->validate($rules, [
            'name.required' => 'Le nom de l\'exploitation est obligatoire.',
            'location.required' => 'La localisation est obligatoire.',
            'total_area_hectares.required' => 'La surface est obligatoire.',
            'culture_type.required' => 'Le type de culture est obligatoire.',
            'status.required' => 'Le statut est obligatoire.',
            'user_id.required' => 'Le propriétaire est obligatoire.',
        ]);

        $data = [
            'name' => $this->name,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'total_area_hectares' => $this->total_area_hectares,
            'culture_type' => $this->culture_type,
            'status' => $this->status,
            'expected_harvest_date' => $this->expected_harvest_date ?: null,
            'crop_stage' => $this->crop_stage,
            'crop_stage_progress' => $this->crop_stage_progress,
            'last_visit_date' => $this->last_visit_date ?: null,
            'notes' => $this->notes,
            'user_id' => $this->user_id,
        ];

        if ($this->isEdit) {
            $farm = Farm::findOrFail($this->farmId);
            $farm->update($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Exploitation mise à jour avec succès.']);
        } else {
            Farm::create($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Exploitation créée avec succès.']);
        }

        return $this->redirect(route('admin.farms.index'));
    }

    public function render()
    {
        $clients = User::where('role', 'client')->get(['id', 'name']);

        return view('livewire.farm-form', [
            'clients' => $clients,
        ]);
    }
}
