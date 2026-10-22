<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Models\EmailAttempt;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('email:retry-failed {--include-abandoned : Réessayer aussi les emails abandonnés} {--dry-run : Afficher sans réessayer}')]
#[Description('Relance les emails échoués ou abandonnés')]
class RetryFailedEmails extends Command
{
    public function handle(): int
    {
        $includeAbandoned = $this->option('include-abandoned');
        $dryRun = $this->option('dry-run');

        $query = EmailAttempt::where('status', 'failed')
            ->where('attempts', '<', EmailAttempt::raw('max_attempts'));

        if ($includeAbandoned) {
            $query->orWhere('status', 'abandoned');
        }

        $failedEmails = $query->get();

        if ($failedEmails->isEmpty()) {
            $this->info('Aucun email à relancer.');
            return self::SUCCESS;
        }

        $this->info("Trouvé {$failedEmails->count()} email(s) à relancer.");

        if ($dryRun) {
            foreach ($failedEmails as $email) {
                $this->line("  - {$email->recipient_email} | {$email->subject} | Tentatives: {$email->attempts}/{$email->max_attempts} | Statut: {$email->status}");
            }
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($failedEmails->count());
        $bar->start();

        $retried = 0;
        foreach ($failedEmails as $email) {
            $email->update([
                'status' => 'pending',
                'last_error' => null,
            ]);
            SendEmailJob::dispatch($email);
            $retried++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("{$retried} email(s) remis en file d'attente.");

        return self::SUCCESS;
    }
}
