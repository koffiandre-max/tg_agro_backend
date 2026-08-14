<?php

namespace App\Providers;

use App\Events\ReportSubmitted;
use App\Events\ReportValidated;
use App\Events\ReportRejected;
use App\Listeners\SendReportSubmittedNotification;
use App\Listeners\SendReportValidatedNotification;
use App\Listeners\SendReportRejectedNotification;
use App\Services\SendmailService;
use App\View\Composers\NavigationComposer;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SendmailService::class, function ($app) {
            return new SendmailService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', NavigationComposer::class);

        // ── Event → Listener mappings ──────────────────────────
        // Les e-mails sont toujours envoyés par les contrôleurs appelants
        // (SendmailService, comportement synchrone préservé).
        // Les listeners créent uniquement les notifications in-app.
        Event::listen(ReportSubmitted::class, SendReportSubmittedNotification::class);
        Event::listen(ReportValidated::class, SendReportValidatedNotification::class);
        Event::listen(ReportRejected::class, SendReportRejectedNotification::class);
    }
}
