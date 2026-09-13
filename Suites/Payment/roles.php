<?php

return [
    'permissions' => [
        'payment.view'   => 'Voir le module Payment',
        'payment.create' => 'Créer dans Payment',
        'payment.edit'   => 'Modifier dans Payment',
        'payment.delete' => 'Supprimer dans Payment',
    ],
    'roles' => [
        'admin' => [
            'payment.view',
            'payment.create',
            'payment.edit',
            'payment.delete',
        ],
        'manager' => [
            'payment.view',
            'payment.create',
            'payment.edit',
        ],
        'employee' => [
            'payment.view',
        ],
    ],
];
