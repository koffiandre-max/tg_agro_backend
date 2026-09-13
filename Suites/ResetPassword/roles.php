<?php

return [
    'permissions' => [
        'reset_password.view'   => 'Voir le module Réinitialisation',
        'reset_password.reset'  => 'Réinitialiser un mot de passe',
    ],
    'roles' => [
        'admin' => [
            'reset_password.view',
            'reset_password.reset',
        ],
        'manager' => [
            'reset_password.view',
            'reset_password.reset',
        ],
        'employee' => [
            'reset_password.view',
        ],
    ],
];
