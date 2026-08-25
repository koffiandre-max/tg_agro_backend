<?php

namespace App\Http\Controllers;

use App\Data\LoginData;
use App\Data\RegisterData;
use App\Models\Client;
use App\Models\User;
use App\Support\Helpers;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

class AuthController extends Controller
{
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
                'email' => 'required|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'phone' => 'required|max:15',
                'city_of_residence' => 'nullable|max:20',
                'country_of_residence' =>  'nullable|max:20'

            ]);
            $registerData = RegisterData::from($validated);

            $_client = User::where('email', $registerData->email)->first();

            if ($_client) {
                return response()->json([
                    'success' => false,
                    'message' => "ce compte existe deja",
                ]);
            }

            $user = User::create([
                'name' => $registerData->name,
                'email' => $registerData->email,
                'password' => Hash::make($registerData->password),
                'role' => 'client',
                'phone' => $registerData->phone ?: null,
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

            Auth::login($user);

            return response()->json([
                'success' => true,
                'redirect' => route('admin.portail.index'),
            ]);
        } catch (Exception $e) {
            \Log::error("File: " . $e->getFile() . " Line: " . $e->getLine() . " Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "une erreur est survenue",
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
}
