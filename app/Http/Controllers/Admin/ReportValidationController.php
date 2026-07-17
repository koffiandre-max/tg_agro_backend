<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\SendmailService;
use Illuminate\Http\Request;

class ReportValidationController extends Controller
{
    public function __construct(private SendmailService $mailer) {}

    public function validate(Report $report)
    {
        $report->update([
            'status' => 'validated',
            'is_validated' => true,
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        if ($report->client?->user?->email) {
            $this->mailer->sendView(
                $report->client->user->email,
                'Rapport validé : ' . $report->title,
                'emails.reports.validated',
                ['report' => $report]
            );
        }

        return redirect()->back()->with('success', 'Rapport validé avec succès.');
    }

    public function reject(Request $request, Report $report)
    {
        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $report->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);

        if ($report->client?->user?->email) {
            $this->mailer->sendView(
                $report->client->user->email,
                'Rapport rejeté : ' . $report->title,
                'emails.reports.rejected',
                ['report' => $report]
            );
        }

        return redirect()->back()->with('success', 'Rapport rejeté avec succès.');
    }
}
