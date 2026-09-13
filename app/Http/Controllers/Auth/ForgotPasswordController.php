<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /**
     * Affiche le formulaire "mot de passe oublié".
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Génère un token et envoie le lien par email.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->email;
        $user  = \App\Models\User::where('email', $email)->first();

        // Si aucun compte ne correspond, on le signale clairement.
        if (! $user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Aucun compte n\'est associé à cette adresse email.']);
        }

        $token = \Illuminate\Support\Str::random(64);

        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'email'      => $email,
                'token'      => \Illuminate\Support\Facades\Hash::make($token),
                'created_at' => now(),
            ]
        );

        \Illuminate\Support\Facades\Mail::to($email)
            ->send(new \App\Mail\ResetPasswordMail($token, $email));

        return back()->with('status', 'Un lien de réinitialisation vient de vous être envoyé par email.');
    }
}
