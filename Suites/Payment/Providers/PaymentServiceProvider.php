<?php

namespace Modules\Payment\Providers;

use App\Core\Traits\RegistersModule;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Payment\Console\ProcessAutoRenewals;
use Modules\Payment\Services\GatewayManager;
use Modules\Payment\Services\SubscriptionService;

class PaymentServiceProvider extends ServiceProvider
{
    use RegistersModule;

    public function register(): void
    {
        // Config du module (surchargable par l'app hôte via config/payment.php).
        $this->mergeConfigFrom(__DIR__.'/../config/payment.php', 'payment');

        $this->app->singleton(GatewayManager::class);
        $this->app->singleton(SubscriptionService::class);
    }

    public function boot(Schedule $schedule): void
    {
        $this->registerModule(
            manifestPath:   __DIR__ . '/../module.json',
            navigationPath: __DIR__ . '/../navigation.php',
            rolesPath:      __DIR__ . '/../roles.php',
        );

        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/Views', 'payment');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ProcessAutoRenewals::class,
            ]);

            // Planification quotidienne du renouvellement automatique
            // (exécutée par le scheduler de l'app hôte : schedule:run).
            $schedule->command('payment:process-auto-renewals')->dailyAt('03:00');
        }

        // Gate de la feature (utilisé par le middleware "feature:payment").
        Gate::define('feature-payment', function ($user) {
            return true;
        });
    }
}

