<?php

/*
|--------------------------------------------------------------------------
| Suite Modules - Config de liaison
|--------------------------------------------------------------------------
|
| Ce fichier relie les modules Business Suite (présents dans "modules_path")
| à l'application Laravel hôte, SANS modifier le code des modules.
|
| Pour installer un module :
|   1. Copiez son dossier dans le chemin "modules_path" (ex: Suites/ResetPassword).
|   2. Ajoutez-le dans le tableau "modules" ci-dessous.
|   3. Exécutez les migrations du module, puis videz le cache de config :
|        php artisan migrate --path=Suites/ResetPassword/Database/Migrations
|        php artisan config:clear && php artisan route:clear
|
*/

return [

    /*
    | Chemin racine (relatif à base_path) où sont stockés les modules.
    | Ex: vendor/koffiandre/suite-modules, Modules, Suites, ...
    */
    'modules_path' => env('SUITE_MODULES_PATH', 'Suites'),

    /*
    | Liste des modules à charger automatiquement.
    | La clé = nom du dossier du module ; "namespace" = namespace PSR-4 généré.
    | "enabled" = false pour désactiver un module sans le supprimer.
    */
    'modules' => [
        'ResetPassword' => [
            'namespace' => 'Modules\\ResetPassword\\',
            'enabled'   => env('SUITE_MODULE_RESET_PASSWORD', true),
        ],
        'Payment' => [
            'namespace' => 'Modules\\Payment\\',
            'enabled'   => env('SUITE_MODULE_PAYMENT', true),
        ],
        'SendEmail' => [
            'namespace' => 'Modules\\SendEmail\\',
            'enabled'   => env('SUITE_MODULE_SEND_EMAIL', true),
        ],
        'Invoice' => [
            'namespace' => 'Modules\\Invoice\\',
            'enabled'   => env('SUITE_MODULE_INVOICE', true),
        ],
    ],

    /*
    | Contrats hôte fournis à l'application pour que les modules
    | puissent être résolus sans être modifiés.
    | L'app peut surcharger ces classes (injection de vos propres implémentations).
    */
    'contracts' => [
        'base_controller' => App\Http\Controllers\BaseController::class,
        'module_sdk'      => App\Core\ModuleSDK::class,
        'registers_module'=> App\Core\Traits\RegistersModule::class,
        'feature_middleware' => App\Http\Middleware\CheckFeatureMiddleware::class,
    ],

];
