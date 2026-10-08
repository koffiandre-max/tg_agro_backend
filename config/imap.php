<?php

return [

    'default' => env('IMAP_CONNECTION', 'default'),

    'connections' => [

        'default' => [
            'host' => env('IMAP_HOST', 'imap-gildas.alwaysdata.net'),
            'port' => env('IMAP_PORT', 993),
            'encryption' => env('IMAP_ENCRYPTION', 'ssl'),
            'username' => env('IMAP_USERNAME'),
            'password' => env('IMAP_PASSWORD'),
            'validate_cert' => env('IMAP_VALIDATE_CERT', true),
            'folder' => env('IMAP_FOLDER', 'INBOX'),
        ],

    ],

];
