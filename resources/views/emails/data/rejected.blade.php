<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisie de données rejetée</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f4f6; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6; padding: 24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; max-width:600px; width:100%;">
                    <tr>
                        <td style="background-color:#dc2626; padding:24px; color:#ffffff;">
                            <h1 style="margin:0; font-size:20px; font-weight:bold;">Saisie de données rejetée</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px; color:#1f2937; font-size:14px; line-height:1.6;">
                            <p style="margin:0 0 16px 0;">Bonjour,</p>
                            <p style="margin:0 0 16px 0;">
                                Une saisie de données agronomiques pour votre exploitation <strong>{{ $dataEntry->farm?->name ?? '' }}</strong> a été rejetée.
                            </p>

                            <h2 style="font-size:16px; margin:24px 0 8px 0; color:#111827;">Détails de la saisie</h2>
                            <ul style="padding-left:20px; margin:0 0 16px 0; color:#374151;">
                                @if($dataEntry->crop_stage)
                                <li style="margin-bottom:4px;"><strong>Stade cultural</strong> : {{ $dataEntry->crop_stage }}</li>
                                @endif
                                @if($dataEntry->crop_stage_progress !== null)
                                <li style="margin-bottom:4px;"><strong>Progression</strong> : {{ $dataEntry->crop_stage_progress }}%</li>
                                @endif
                                @if($dataEntry->weather_conditions)
                                <li style="margin-bottom:4px;"><strong>Conditions météo</strong> : {{ $dataEntry->weather_conditions }}</li>
                                @endif
                            </ul>

                            @if($dataEntry->rejection_reason)
                                <h2 style="font-size:16px; margin:24px 0 8px 0; color:#111827;">Motif du rejet</h2>
                                <p style="margin:0 0 16px 0; color:#374151;">{{ $dataEntry->rejection_reason }}</p>
                            @endif

                            <p style="margin:0 0 16px 0; color:#374151;">
                                Pour plus d'informations, veuillez contacter votre administrateur.
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