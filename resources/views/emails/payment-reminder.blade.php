<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rappel de paiement</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background-color: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h1 style="color: #1f2937; font-size: 24px; margin-bottom: 10px;">Rappel de paiement</h1>
            <p style="color: #6b7280; font-size: 14px; margin-bottom: 20px;">Bonjour {{ $subscription->user->name ?? 'Client' }},</p>

            <p style="color: #374151; font-size: 14px; line-height: 1.6; margin-bottom: 20px;">
                Nous vous rappelons que votre abonnement <strong>{{ strtoupper($subscription->type) }}</strong>
                arrive à échéance le <strong>{{ $subscription->next_payment_date?->format('d/m/Y') ?? '—' }}</strong>.
            </p>

            <div style="background-color: #f9fafb; border-radius: 6px; padding: 20px; margin-bottom: 20px;">
                <h2 style="color: #1f2937; font-size: 18px; margin-bottom: 15px;">Détails de l'abonnement</h2>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">Type</td>
                        <td style="padding: 8px 0; color: #1f2937; font-size: 14px; font-weight: bold;">{{ strtoupper($subscription->type) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">Montant</td>
                        <td style="padding: 8px 0; color: #1f2937; font-size: 14px; font-weight: bold;">{{ number_format($subscription->amount, 2) }} {{ $subscription->currency }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">Prochain paiement</td>
                        <td style="padding: 8px 0; color: #1f2937; font-size: 14px; font-weight: bold;">{{ $subscription->next_payment_date?->format('d/m/Y') ?? '—' }}</td>
                    </tr>
                </table>
            </div>

            <p style="color: #374151; font-size: 14px; line-height: 1.6; margin-bottom: 20px;">
                Si vous avez déjà effectué le paiement, veuillez ignorer ce message.
                Sinon, nous vous invitons à procéder au règlement dès que possible.
            </p>

            <p style="color: #374151; font-size: 14px; line-height: 1.6;">
                Cordialement,<br>
                <strong>TG'INVEST CONSULTING</strong>
            </p>
        </div>
    </div>
</body>
</html>
