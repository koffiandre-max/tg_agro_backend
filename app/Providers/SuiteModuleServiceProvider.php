<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

/**
 * Charge dynamiquement les modules Business Suite déclarés dans
 * config/suite.php, enregistre leur ServiceProvider (routes, vues,
 * migrations) et ajoute l'alias middleware "feature".
 */
class SuiteModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/suite.php', 'suite');

        foreach (config('suite.modules', []) as $name => $options) {
            $enabled = is_array($options) ? ($options['enabled'] ?? true) : $options;
            if (! $enabled) {
                continue;
            }

            $namespace = is_array($options) ? ($options['namespace'] ?? 'Modules\\'.$name.'\\') : 'Modules\\'.$name.'\\';

            $this->registerAutoload($namespace, config('suite.modules_path', 'Suites').'/'.$name);

            $providerClass = $namespace.'Providers\\'.$name.'ServiceProvider';
            if (class_exists($providerClass)) {
                $this->app->register($providerClass);
            }
        }
    }

    public function boot(): void
    {
        // L'alias middleware est également géré dans bootstrap/app.php
        // pour rester compatible avec la config d'alias Laravel 11+.
    }

    /**
     * Enregistre un autoloader PSR-4 pointant vers le dossier du module.
     */
    protected function registerAutoload(string $namespace, string $path): void
    {
        $root = base_path($path);

        if (! is_dir($root)) {
            return;
        }

        spl_autoload_register(function (string $class) use ($namespace, $root): void {
            if (! str_starts_with($class, $namespace)) {
                return;
            }

            $relative = substr($class, strlen($namespace));
            $file = $root.'/'.str_replace('\\', '/', $relative).'.php';

            if (is_file($file)) {
                require_once $file;
            }
        });
    }
}
