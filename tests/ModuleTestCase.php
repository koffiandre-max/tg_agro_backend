<?php

/**
 * Contrat hôte pour les tests des modules Business Suite.
 *
 * Les modules (Suites/*) étendent cette classe dans leurs tests :
 * toute app Laravel hôte qui installe des modules doit fournir cette
 * classe (généralement via tests/ModuleTestCase.php + classmap composer).
 *
 * Avant chaque test, les migrations de l'application ET de tous les
 * modules déclarés dans config/suite.php sont exécutées (utile pour les
 * bases en mémoire comme sqlite :memory:).
 */
abstract class ModuleTestCase extends \Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Migrations de l'application hôte.
        $this->artisan('migrate', ['--force' => true]);

        // Migrations des modules enregistrés dans config/suite.php.
        $modulesPath = rtrim((string) config('suite.modules_path', 'Suites'), '/\\');
        $modulesPath = str_contains($modulesPath, '/')
            ? $modulesPath
            : base_path($modulesPath);

        foreach ((array) config('suite.modules', []) as $name => $moduleConfig) {
            $migrationsPath = $modulesPath.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.'Database'.DIRECTORY_SEPARATOR.'Migrations';

            if (is_dir($migrationsPath)) {
                $this->artisan('migrate', [
                    '--path' => $migrationsPath,
                    '--force' => true,
                ]);
            }
        }
    }
}

