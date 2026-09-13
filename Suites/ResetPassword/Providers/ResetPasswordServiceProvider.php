<?php

namespace Modules\ResetPassword\Providers;

use App\Core\Traits\RegistersModule;
use Illuminate\Support\ServiceProvider;

class ResetPasswordServiceProvider extends ServiceProvider
{
    use RegistersModule;

    public function boot(): void
    {
        $this->registerModule(
            manifestPath:   __DIR__ . '/../module.json',
            navigationPath: __DIR__ . '/../navigation.php',
            rolesPath:      __DIR__ . '/../roles.php',
        );

        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/Views', 'reset_password');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
