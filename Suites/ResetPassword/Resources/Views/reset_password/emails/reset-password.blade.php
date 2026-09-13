<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de votre mot de passe</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background-color: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h1 style="color: #1f2937; font-size: 24px; margin-bottom: 10px;">Réinitialisation du mot de passe</h1>
            <p style="color: #6b7280; font-size: 14px; margin-bottom: 20px;">Bonjour,</p>

            <p style="color: #374151; font-size: 14px; line-height: 1.6; margin-bottom: 20px;">
                Vous recevez cet email car nous avons reçu une demande de réinitialisation de mot de passe
                pour votre compte. Cliquez sur le bouton ci-dessous pour définir un nouveau mot de passe.
            </p>

            <div style="text-align: center; margin-bottom: 20px;">
                <a href="{{ route('reset_password.reset', ['token' => $token, 'email' => $email]) }}"
                   style="display: inline-block; background-color: #059669; color: #ffffff; padding: 12px 28px; border-radius: 6px; font-size: 14px; font-weight: bold; text-decoration: none;">
                    Réinitialiser mon mot de passe
                </a>
            </div>

            <p style="color: #6b7280; font-size: 13px; line-height: 1.6; margin-bottom: 20px;">
                Si vous n'êtes pas à l'origine de cette demande, aucune action n'est nécessaire. Ce lien expirera dans {{ config('auth.passwords.users.expire', 60) }} minutes.
            </p>

            <p style="color: #6b7280; font-size: 13px; line-height: 1.6;">
                Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :<br>
                <span style="color: #059669; word-break: break-all;">{{ route('reset_password.reset', ['token' => $token, 'email' => $email]) }}</span>
            </p>

            <p style="color: #374151; font-size: 14px; line-height: 1.6; margin-top: 24px;">
                Cordialement,<br>
                <strong>{{ config('app.name', 'TG Agro') }}</strong>
            </p>
        </div>
    </div>
</body>
</html>
