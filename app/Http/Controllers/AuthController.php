<?php

namespace App\Http\Controllers;

use App\Data\LoginData;
use App\Data\RegisterData;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
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
        $registerData = RegisterData::from($request->all());

        $user = User::create([
            'name' => $registerData->name,
            'email' => $registerData->email,
            'password' => Hash::make($registerData->password),
            'role' => 'client',
        ]);

        Client::create([
            'user_id' => $user->id,
            'phone' => $registerData->phone,
            'country_of_residence' => $registerData->country_of_residence,
            'country_of_origin' => $registerData->country_of_origin,
            'city_of_residence' => $registerData->city_of_residence,
            'subscription_type' => 'basic',
        ]);

        Auth::login($user);

        return response()->json([
            'success' => true,
            'redirect' => route('admin.portail.index'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
