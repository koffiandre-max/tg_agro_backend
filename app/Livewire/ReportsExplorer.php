<?php

namespace App\Livewire;

use App\Models\Farm;
use App\Models\Report;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class ReportsExplorer extends Component
{
    use WithPagination;

    public ?int $selectedFarmId = null;
    public string $search = '';
    public bool $showCheck = false;
    public array $selected = [];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedFarmId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $farms = Farm::orderBy('name')->get();

        $query = Report::query()
            ->with(['farm', 'client.user'])
            ->when($this->selectedFarmId, fn($q) => $q->where('farm_id', $this->selectedFarmId))
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhereHas('farm', function ($q) {
                            $q->where('name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->orderBy('created_at', 'desc');

        $reports = $query->paginate(12);

        return view('livewire.reports-explorer', [
            'farms' => $farms,
            'reports' => $reports,
        ]);
    }

    public function deleteFarm($id): void
    {
        Farm::findOrFail($id)->delete();
        $this->dispatch('farm-deleted');
    }

    public function downloadReport($id)
    {
        $report = Report::findOrFail($id);

        if ($report->file_path && Storage::disk('public')->exists($report->file_path)) {
            return response()->download(storage_path("app/public/{$report->file_path}"), $report->file_original_name);
        }

        $this->dispatch('report-download-failed');
    }

    public function deleteReport($id): void
    {
        $report = Report::findOrFail($id);

        if ($report->file_path && Storage::disk('public')->exists($report->file_path)) {
            Storage::disk('public')->delete($report->file_path);
        }

        $report->delete();
        $this->dispatch('report-deleted');
    }
}
