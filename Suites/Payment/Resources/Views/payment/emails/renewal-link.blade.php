<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:32px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="560" cellpadding="0" cellspacing="0"
                       style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.1);">
                    <tr>
                        <td style="background:#111827;padding:24px 32px;">
                            <h1 style="margin:0;font-size:18px;color:#ffffff;">Renouvellement de votre abonnement</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 16px;font-size:14px;color:#374151;line-height:1.6;">
                                Bonjour,<br><br>
                                Votre abonnement <strong>{{ $planName }}</strong> arrive à échéance.
                                Pour continuer à profiter du service, merci de régler le montant de
                                <strong>{{ $amount }}</strong> pour la nouvelle période.
                            </p>

                            <p style="margin:24px 0;text-align:center;">
                                <a href="{{ $link }}"
                                   style="display:inline-block;background:#16a34a;color:#ffffff;text-decoration:none;
                                          padding:12px 28px;border-radius:8px;font-size:14px;font-weight:bold;">
                                    Payer {{ $amount }}
                                </a>
                            </p>

                            <p style="margin:0 0 8px;font-size:12px;color:#6b7280;line-height:1.6;">
                                Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
                                <a href="{{ $link }}" style="color:#2563eb;word-break:break-all;">{{ $link }}</a>
                            </p>

                            <p style="margin:16px 0 0;font-size:12px;color:#9ca3af;line-height:1.6;">
                                Sans paiement sous {{ $graceDays }} jours après l'échéance, l'abonnement sera suspendu.
                                Si vous ne souhaitez pas renouveler, ignorez simplement cet email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
