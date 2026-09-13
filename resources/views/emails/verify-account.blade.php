<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification de votre compte</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background-color: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h1 style="color: #1f2937; font-size: 24px; margin-bottom: 10px;">Bienvenue sur {{ config('app.name', 'TG Agro') }} !</h1>
            <p style="color: #6b7280; font-size: 14px; margin-bottom: 20px;">Bonjour {{ $name }},</p>

            <p style="color: #374151; font-size: 14px; line-height: 1.6; margin-bottom: 20px;">
                Merci de vous être inscrit sur notre plateforme. Pour activer votre compte et accéder à tous nos services, 
                veuillez vérifier votre adresse email en cliquant sur le bouton ci-dessous.
            </p>

            <div style="text-align: center; margin-bottom: 20px;">
                <a href="{{ $verificationUrl }}"
                   style="display: inline-block; background-color: #059669; color: #ffffff; padding: 14px 32px; border-radius: 6px; font-size: 16px; font-weight: bold; text-decoration: none;">
                    Vérifier mon adresse email
                </a>
            </div>

            <p style="color: #6b7280; font-size: 13px; line-height: 1.6; margin-bottom: 20px;">
                Ce lien de vérification expirera dans 24 heures. Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :<br>
                <span style="color: #059669; word-break: break-all;">{{ $verificationUrl }}</span>
            </p>

            <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px 16px; margin-bottom: 20px; border-radius: 4px;">
                <p style="color: #92400e; font-size: 13px; margin: 0;">
                    <strong>Important :</strong> Vous ne pourrez pas accéder à votre compte tant que votre adresse email n'aura pas été vérifiée.
                </p>
            </div>

            <p style="color: #6b7280; font-size: 13px; line-height: 1.6; margin-bottom: 20px;">
                Si vous n'avez pas créé de compte sur {{ config('app.name', 'TG Agro') }}, vous pouvez ignorer cet email.
            </p>

            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">

            <p style="color: #374151; font-size: 14px; line-height: 1.6; margin-top: 24px;">
                Cordialement,<br>
                <strong>L'équipe {{ config('app.name', 'TG Agro') }}</strong>
            </p>
        </div>

        <p style="color: #9ca3af; font-size: 12px; text-align: center; margin-top: 16px;">
            &copy; {{ date('Y') }} {{ config('app.name', 'TG Agro') }} - Tous droits réservés
        </p>
    </div>
</body>
</html>
