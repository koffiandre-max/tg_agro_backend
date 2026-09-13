<?php

namespace Modules\ResetPassword\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\ResetPassword\Http\Requests\ResetPasswordRequest;
use Modules\ResetPassword\Http\Requests\SendResetLinkRequest;
use Modules\ResetPassword\Mail\ResetPasswordMail;

class ResetPasswordController
{
    /**
     * Affiche le formulaire "mot de passe oublié" (saisie de l'email).
     */
    public function showLinkRequestForm()
    {
        return view('reset_password::forgot');
    }

    /**
     * Génère un token et envoie le lien de réinitialisation par email.
     */
    public function sendResetLinkEmail(SendResetLinkRequest $request)
    {
        $email = $request->validated()['email'];
        $user  = User::where('email', $email)->first();

        // Si aucun compte ne correspond, on le signale clairement.
        if (! $user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Aucun compte n\'est associé à cette adresse email.']);
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'email'      => $email,
                'token'      => Hash::make($token),
                'created_at' => now(),
            ]
        );

        Mail::to($email)->send(new ResetPasswordMail($token, $email));

        return back()->with('status', 'Un lien de réinitialisation vient de vous être envoyé par email.');
    }

    /**
     * Affiche le formulaire de nouveau mot de passe (via le lien reçu par email).
     */
    public function showResetForm(Request $request, string $token)
    {
        return view('reset_password::reset', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Valide le token puis applique le nouveau mot de passe.
     */
    public function reset(ResetPasswordRequest $request)
    {
        $validated = $request->validated();

        $record = DB::table('password_reset_tokens')->where('email', $validated['email'])->first();

        if (! $record || ! Hash::check($validated['token'], $record->token)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Ce lien de réinitialisation est invalide.']);
        }

        $expires = config('auth.passwords.users.expire', 60);
        $createdAt = Carbon::parse($record->created_at);

        if ($createdAt->addMinutes($expires)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Ce lien de réinitialisation a expiré. Veuillez renouveler votre demande.']);
        }

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Aucun compte ne correspond à cette adresse email.']);
        }

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        return redirect()->route('login')
            ->with('status', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
    }
}
