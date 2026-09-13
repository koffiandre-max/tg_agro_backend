<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Vérifier l'adresse email d'un utilisateur via le token.
     */
    public function verify(Request $request, string $token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Le lien de vérification est invalide ou a expiré.');
        }

        if ($user->is_verified) {
            return redirect()->route('login')->with('info', 'Votre compte a déjà été vérifié. Vous pouvez vous connecter.');
        }

        $user->update([
            'is_verified' => true,
            'verified_at' => now(),
            'email_verified_at' => now(),
            'is_active' => true,
            'verification_token' => null,
        ]);

        return redirect()->route('login')->with('success', 'Votre compte a été vérifié avec succès ! Vous pouvez maintenant vous connecter.');
    }

    /**
     * Renvoyer l'email de vérification.
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun compte trouvé avec cette adresse email.',
            ], 404);
        }

        if ($user->is_verified) {
            return response()->json([
                'success' => false,
                'message' => 'Ce compte a déjà été vérifié.',
            ], 422);
        }

        // Générer un nouveau token
        $token = bin2hex(random_bytes(32));
        $user->update(['verification_token' => $token]);

        // Renvoyer l'email de vérification
        $verificationUrl = route('verification.verify', ['token' => $token]);

        try {
            $emailService = app(\App\Services\SendmailService::class);
            $emailService->sendView(
                $user->email,
                'Vérification de votre compte ' . config('app.name', 'TG Agro'),
                'emails.verify-account',
                [
                    'name' => $user->name,
                    'verificationUrl' => $verificationUrl,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Un nouvel email de vérification a été envoyé à votre adresse.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email. Veuillez réessayer plus tard.',
            ], 500);
        }
    }
}
