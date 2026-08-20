<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
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

        return redirect()->back()->with('success', 'Rapport rejeté avec succès.');
    }
}
