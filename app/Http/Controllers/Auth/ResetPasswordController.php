<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /**
     * Affiche le formulaire de nouveau mot de passe (via le lien reçu par email).
     */
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Valide le token puis applique le nouveau mot de passe.
     */
    public function reset(Request $request)
    {
        $validated = $request->validate([
            'email'                     => ['required', 'email'],
            'token'                     => ['required', 'string'],
            'new_password'              => ['required', 'string', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['required', 'string'],
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $validated['email'])->first();

        if (! $record || ! Hash::check($validated['token'], $record->token)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Ce lien de réinitialisation est invalide.']);
        }

        $expires    = config('auth.passwords.users.expire', 60);
        $createdAt  = Carbon::parse($record->created_at);

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
