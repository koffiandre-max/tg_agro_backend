<?php

namespace App\Livewire;

use App\Models\Report;
use App\Models\User;
use App\Services\SendmailService;
use Livewire\Component;
use Livewire\WithPagination;

class ReportsTable extends Component
{
    use WithPagination;

    public function __construct(private ?SendmailService $mailer = null) {}

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

    public function viewReport($id): void
    {
        $this->dispatch('view-report', id: $id);
    }

    public function validateReport($id): void
    {
        $report = Report::findOrFail($id);
        $report->update([
            'status' => 'validated',
            'is_validated' => true,
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        if ($this->mailer) {
            $admins = User::where('role', 'admin')
                ->where('is_active', true)
                ->get();

            foreach ($admins as $admin) {
                if ($admin->email) {
                    $this->mailer->sendView(
                        $admin->email,
                        'Rapport validé : ' . $report->title,
                        'emails.reports.validated',
                        ['report' => $report]
                    );
                }
            }

            if ($report->technician?->email) {
                $this->mailer->sendView(
                    $report->technician->email,
                    'Rapport validé : ' . $report->title,
                    'emails.reports.validated',
                    ['report' => $report]
                );
            }

            if ($report->client?->user?->email) {
                $this->mailer->sendView(
                    $report->client->user->email,
                    'Rapport validé : ' . $report->title,
                    'emails.reports.validated',
                    ['report' => $report]
                );
            }
        }

        $this->dispatch('report-validated');
    }

    public function rejectReport($id): void
    {
        $report = Report::findOrFail($id);
        $report->update([
            'status' => 'rejected',
            'rejection_reason' => null,
        ]);

        if ($this->mailer) {
            $admins = User::where('role', 'admin')
                ->where('is_active', true)
                ->get();

            foreach ($admins as $admin) {
                if ($admin->email) {
                    $this->mailer->sendView(
                        $admin->email,
                        'Rapport rejeté : ' . $report->title,
                        'emails.reports.rejected',
                        ['report' => $report]
                    );
                }
            }

            if ($report->technician?->email) {
                $this->mailer->sendView(
                    $report->technician->email,
                    'Rapport rejeté : ' . $report->title,
                    'emails.reports.rejected',
                    ['report' => $report]
                );
            }

            if ($report->client?->user?->email) {
                $this->mailer->sendView(
                    $report->client->user->email,
                    'Rapport rejeté : ' . $report->title,
                    'emails.reports.rejected',
                    ['report' => $report]
                );
            }
        }

        $this->dispatch('report-rejected');
    }

    public function render()
    {
        $query = Report::query()
            ->with(['farm', 'client.user', 'technician'])
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhereHas('farm', function ($q) {
                            $q->where('name', 'like', "%{$this->search}%");
                        })
                        ->orWhereHas('client.user', function ($q) {
                            $q->where('name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->type, fn($q) => $q->where('type', $this->type))
            ->orderBy($this->sortField, $this->sortDirection);

        $reports = $query->paginate($this->perPage);

        $reportTypes = Report::query()
            ->distinct()
            ->pluck('type')
            ->filter()
            ->sort()
            ->values();

        return view('livewire.reports-table', [
            'reports' => $reports,
            'reportTypes' => $reportTypes,
        ]);
    }
}
