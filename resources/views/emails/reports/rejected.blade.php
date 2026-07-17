<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport rejeté</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f4f6; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6; padding: 24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; max-width:600px; width:100%;">
                    <tr>
                        <td style="background-color:#dc2626; padding:24px; color:#ffffff;">
                            <h1 style="margin:0; font-size:20px; font-weight:bold;">Rapport rejeté</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px; color:#1f2937; font-size:14px; line-height:1.6;">
                            <p style="margin:0 0 16px 0;">Bonjour,</p>
                            <p style="margin:0 0 16px 0;">
                                Le rapport <strong>{{ $report->title }}</strong> a été rejeté.
                            </p>

                            <h2 style="font-size:16px; margin:24px 0 8px 0; color:#111827;">Détails du rapport</h2>
                            <ul style="padding-left:20px; margin:0 0 16px 0; color:#374151;">
                                <li style="margin-bottom:4px;"><strong>Titre</strong> : {{ $report->title }}</li>
                                <li style="margin-bottom:4px;"><strong>Type</strong> : {{ $report->type }}</li>
                                <li style="margin-bottom:4px;"><strong>Exploitation</strong> : {{ $report->farm?->name ?? '—' }}</li>
                                <li style="margin-bottom:4px;"><strong>Date de création</strong> : {{ $report->created_at?->format('d/m/Y H:i') ?? '—' }}</li>
                            </ul>

                            @if($report->rejection_reason)
                                <h2 style="font-size:16px; margin:24px 0 8px 0; color:#111827;">Motif du rejet</h2>
                                <p style="margin:0 0 16px 0; color:#374151;">{{ $report->rejection_reason }}</p>
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