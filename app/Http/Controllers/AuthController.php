<?php

namespace App\Http\Controllers;

use App\Data\LoginData;
use App\Data\RegisterData;
use App\Models\Client;
use App\Models\User;
use App\Services\SendmailService;
use App\Support\Helpers;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

class AuthController extends Controller
{
    protected $emailService;

    public function __construct(SendmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function showLoginForm()
    {
        return view('login');
    }

    public function showRegisterForm()
    {
        return view('register');
    }

    public function login(Request $request)
    {
        $loginData = LoginData::from($request->all());

        $credentials = [
            'email' => $loginData->email,
            'password' => $loginData->password,
        ];

        $remember = $request->boolean('remember', false);

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->is_verified === false) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->json([
                    'success' => false,
                    'message' => 'Votre compte n\'a pas encore été vérifié. Veuillez cliquer sur le lien de vérification envoyé par email.',
                ], 403);
            }

            $redirect = match ($user->role ?? null) {
                'admin' => Route::has('dashboard') ? route('dashboard') : route('admin.technicians.index'),
                'client' => Route::has('admin.portail.index') ? route('admin.portail.index') : route('login'),
                'technician' => route('admin.technitian.index'),
                default => route('admin.portail.index'),
            };

            return response()->json([
                'success' => true,
                'redirect' => $redirect,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email ou mot de passe incorrect.',
        ], 401);
    }

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'phone' => 'required|max:15',
                'city_of_residence' => 'nullable|max:20',
                'country_of_residence' =>  'nullable|max:20',
                'country_of_origin' =>  'nullable|max:20',
            ]);
            $registerData = RegisterData::from($validated);

            $existingUser = User::where('email', $registerData->email)->first();

            if ($existingUser) {
                return response()->json([
                    'success' => false,
                    'message' => "Ce compte existe déjà.",
                ]);
            }

            $token = bin2hex(random_bytes(32));

            $user = User::create([
                'name' => $registerData->name,
                'email' => $registerData->email,
                'password' => Hash::make($registerData->password),
                'role' => 'client',
                'phone' => $registerData->phone ?: null,
                'is_active' => false,
                'is_verified' => false,
                'verified_at' => null,
                'verification_token' => $token,
            ]);

            $code = Helpers::generateUniqueClientCode();

            Client::create([
                'user_id' => $user->id,
                'code' => $code,
                'country_of_residence' => $registerData->country_of_residence ?: null,
                'country_of_origin' => $registerData->country_of_origin ?: null,
                'city_of_residence' => $registerData->city_of_residence ?: null,
                'subscription_type' => 'basic',
                'subscription_expires_at' => now()->addMonth(),
            ]);

            $verificationUrl = route('verification.verify', ['token' => $token]);

            try {
                $this->emailService->sendView(
                    $user->email,
                    'Vérification de votre compte ' . config('app.name', 'TG Agro'),
                    'emails.verify-account',
                    [
                        'name' => $user->name,
                        'verificationUrl' => $verificationUrl,
                    ]
                );
            } catch (\Exception $e) {
                \Log::error("Verification email failed: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'requires_verification' => true,
                'message' => 'Inscription réussie ! Veuillez vérifier votre adresse email pour activer votre compte. Vous allez recevoir un lien de confirmation.',
            ]);
        } catch (Exception $e) {
            \Log::error("File: " . $e->getFile() . " Line: " . $e->getLine() . " Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Une erreur est survenue lors de l'inscription.",
            ]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function refresh(Request $request)
    {
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'csrf_token' => csrf_token(),
            'user' => Auth::check() ? [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'role' => Auth::user()->role,
            ] : null,
        ]);
    }
}
