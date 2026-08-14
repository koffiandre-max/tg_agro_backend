<?php

namespace App\Http\Controllers;

use App\Data\LoginData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        // dd($request->all());
        // Validation via Laravel Data
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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
