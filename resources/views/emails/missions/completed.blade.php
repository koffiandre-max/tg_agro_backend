<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mission terminée</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f4f6; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6; padding: 24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; max-width:600px; width:100%;">
                    <tr>
                        <td style="background-color:#059669; padding:24px; color:#ffffff;">
                            <h1 style="margin:0; font-size:20px; font-weight:bold;">Mission terminée</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px; color:#1f2937; font-size:14px; line-height:1.6;">
                            <p style="margin:0 0 16px 0;">Bonjour,</p>
                            <p style="margin:0 0 16px 0;">
                                La mission <strong>{{ $mission->title }}</strong> a été marquée comme terminée.
                            </p>

                            <h2 style="font-size:16px; margin:24px 0 8px 0; color:#111827;">Détails de la mission</h2>
                            <ul style="padding-left:20px; margin:0 0 16px 0; color:#374151;">
                                <li style="margin-bottom:4px;"><strong>Titre</strong> : {{ $mission->title }}</li>
                                <li style="margin-bottom:4px;"><strong>Exploitation</strong> : {{ $mission->farm?->name ?? '—' }}</li>
                                <li style="margin-bottom:4px;"><strong>Technicien</strong> : {{ $mission->technician?->user?->name ?? '—' }}</li>
                                <li style="margin-bottom:4px;"><strong>Date de complétion</strong> : {{ $mission->completed_at?->format('d/m/Y H:i') ?? '—' }}</li>
                                @if($mission->description)
                                <li style="margin-bottom:4px;"><strong>Description</strong> : {{ $mission->description }}</li>
                                @endif
                            </ul>

                            <p style="margin:24px 0 0 0;">
                                <a href="{{ route('admin.technitian.missions.show', $mission->id) }}" style="display:inline-block; background-color:#059669; color:#ffffff; padding:10px 20px; border-radius:6px; text-decoration:none; font-weight:bold;">Voir la mission</a>
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
