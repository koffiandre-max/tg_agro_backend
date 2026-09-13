<?php

namespace Modules\SendEmail\Providers;

use App\Core\Traits\RegistersModule;
use Illuminate\Support\ServiceProvider;

class SendEmailServiceProvider extends ServiceProvider
{
    use RegistersModule;

    public function register(): void
    {
        // Config du module (surchargable par l'app hôte via config/send_email.php).
        $this->mergeConfigFrom(__DIR__.'/../config/send_email.php', 'send_email');
    }

    public function boot(): void
    {
        $this->registerModule(
            manifestPath:   __DIR__ . '/../module.json',
            navigationPath: __DIR__ . '/../navigation.php',
            rolesPath:      __DIR__ . '/../roles.php',
        );

        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/Views', 'send_email');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
