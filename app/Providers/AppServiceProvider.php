<?php

namespace App\Providers;

use App\Auth\ApiTokenGuard;
use App\Events\ReportSubmitted;
use App\Events\ReportValidated;
use App\Events\ReportRejected;
use App\Listeners\SendReportSubmittedNotification;
use App\Listeners\SendReportValidatedNotification;
use App\Listeners\SendReportRejectedNotification;
use App\Models\Client;
use App\Models\DataEntry;
use App\Models\Farm;
use App\Models\Mission;
use App\Models\Photo;
use App\Models\Report;
use App\Policies\FarmPolicy;
use App\Observers\ChangelogObserver;
use App\Services\SendmailService;
use App\View\Composers\NavigationComposer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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

        require_once app_path('helpers.php');

        // ── Event → Listener mappings ──────────────────────────
        // Les e-mails sont toujours envoyés par les contrôleurs appelants
        // (SendmailService, comportement synchrone préservé).
        // Les listeners créent uniquement les notifications in-app.
        Event::listen(ReportSubmitted::class, SendReportSubmittedNotification::class);
        Event::listen(ReportValidated::class, SendReportValidatedNotification::class);
        Event::listen(ReportRejected::class, SendReportRejectedNotification::class);

        // ── Garde d'authentification API mobile ─────────────────
        Auth::extend('api-token', function ($app, $name, array $config) {
            return new ApiTokenGuard(
                Auth::createUserProvider($config['provider']),
                $app->make(Request::class)
            );
        });

        // ── Architecture changelog : observés automatiquement ──
        // Toute création / mise à jour / suppression alimente le journal
        // de synchronisation de l'application mobile.
        Mission::observe(ChangelogObserver::class);
        Farm::observe(ChangelogObserver::class);
        Client::observe(ChangelogObserver::class);
        Report::observe(ChangelogObserver::class);
        Photo::observe(ChangelogObserver::class);
        DataEntry::observe(ChangelogObserver::class);

        Gate::policy(Farm::class, \App\Policies\FarmPolicy::class);
        Gate::policy(Client::class, \App\Policies\ClientPolicy::class);
        Gate::policy(Mission::class, \App\Policies\MissionPolicy::class);
        Gate::policy(Photo::class, \App\Policies\PhotoPolicy::class);
        Gate::policy(Report::class, \App\Policies\ReportPolicy::class);
        Gate::policy(DataEntry::class, \App\Policies\DataEntryPolicy::class);
    }
}
