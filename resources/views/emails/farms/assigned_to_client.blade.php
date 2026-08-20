<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exploitation assignée</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f4f6; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6; padding: 24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; max-width:600px; width:100%;">
                    <tr>
                        <td style="background-color:#059669; padding:24px; color:#ffffff;">
                            <h1 style="margin:0; font-size:20px; font-weight:bold;">Nouvelle exploitation assignée</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px; color:#1f2937; font-size:14px; line-height:1.6;">
                            <p style="margin:0 0 16px 0;">Bonjour,</p>
                            <p style="margin:0 0 16px 0;">
                                Une exploitation vous a été assignée : <strong>{{ $farm->name }}</strong>.
                            </p>

                            <h2 style="font-size:16px; margin:24px 0 8px 0; color:#111827;">Détails de l'exploitation</h2>
                            <ul style="padding-left:20px; margin:0 0 16px 0; color:#374151;">
                                <li style="margin-bottom:4px;"><strong>Nom</strong> : {{ $farm->name }}</li>
                                <li style="margin-bottom:4px;"><strong>Localisation</strong> : {{ $farm->location }}</li>
                                <li style="margin-bottom:4px;"><strong>Type de culture</strong> : {{ $farm->culture_type }}</li>
                                <li style="margin-bottom:4px;"><strong>Surface</strong> : {{ number_format($farm->total_area_hectares, 2) }} ha</li>
                                <li style="margin-bottom:4px;"><strong>Statut</strong> : {{ $farm->status }}</li>
                            </ul>

                            <p style="margin:24px 0 0 0;">
                                <a href="{{ route('admin.portail.farms') }}" style="display:inline-block; background-color:#059669; color:#ffffff; padding:10px 20px; border-radius:6px; text-decoration:none; font-weight:bold;">Voir mes exploitations</a>
                            </p>

                            <p style="margin:24px 0 0 0; color:#6b7280;">Merci,<br>{{ config('app.name') }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
