<?php

/*
|--------------------------------------------------------------------------
| Module SendEmail — Configuration
|--------------------------------------------------------------------------
|
| Configuration propre au module SendEmail. Elle peut être surchargée par
| l'application hôte : publiez ce fichier (ou recopiez-le dans config/)
| puis ajustez les valeurs, SANS modifier le code du module.
|
| L'expédition réelle utilise la configuration mail standard de Laravel
| (MAIL_MAILER, MAIL_HOST, ... dans le .env de l'app hôte).
|
*/

return [

    // Expéditeur par défaut des emails du module (null = laisser Laravel décider).
    'from' => [
        'address' => env('SEND_EMAIL_FROM_ADDRESS'),
        'name' => env('SEND_EMAIL_FROM_NAME'),
    ],

    // Préfixe ajouté au sujet de chaque email envoyé via le module.
    'subject_prefix' => env('SEND_EMAIL_SUBJECT_PREFIX', ''),

];
