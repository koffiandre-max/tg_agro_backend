<?php

return [
    'permissions' => [
        'send_email.view'   => 'Voir le module SendEmail',
        'send_email.create' => 'Créer dans SendEmail',
        'send_email.edit'   => 'Modifier dans SendEmail',
        'send_email.delete' => 'Supprimer dans SendEmail',
    ],
    'roles' => [
        'admin' => [
            'send_email.view',
            'send_email.create',
            'send_email.edit',
            'send_email.delete',
        ],
        'manager' => [
            'send_email.view',
            'send_email.create',
            'send_email.edit',
        ],
        'employee' => [
            'send_email.view',
        ],
    ],
];
