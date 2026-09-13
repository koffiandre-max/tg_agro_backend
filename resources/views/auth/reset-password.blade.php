@extends('layouts.auth')
@section('title', 'Nouveau mot de passe')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 px-4 py-10 sm:px-6">
    <div class="w-full max-w-sm">
        <div class="mb-8 text-center">
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-sm">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </span>
            <h1 class="mt-4 text-lg font-semibold text-slate-900">{{ config('app.name', 'TG Agro') }}</h1>
            <p class="mt-0.5 text-sm text-slate-500">Définir un nouveau mot de passe</p>
        </div>

        <div class="rounded-xl bg-white border border-slate-200 shadow-sm">
            <div class="px-6 py-5 sm:px-8 sm:py-6">
                <div class="mb-6">
                    <h2 class="text-sm font-semibold text-slate-900">Nouveau mot de passe</h2>
                    <p class="mt-1 text-xs text-slate-500">Choisissez un mot de passe d'au moins 8 caractères.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-4 rounded-md bg-red-50 border border-red-100 px-3 py-2.5 text-xs text-red-700" role="alert">
                        <ul class="list-disc space-y-1 pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.store') }}" class="space-y-4" novalidate>
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div>
                        <label for="new_password" class="mb-1.5 block text-xs font-medium text-slate-700">Nouveau mot de passe</label>
                        <input
                            type="password"
                            id="new_password"
                            name="new_password"
                            required
                            minlength="8"
                            autocomplete="new-password"
                            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-600 focus:ring-0 focus:outline-none transition-colors"
                        >
                    </div>

                    <div>
                        <label for="new_password_confirmation" class="mb-1.5 block text-xs font-medium text-slate-700">Confirmer le mot de passe</label>
                        <input
                            type="password"
                            id="new_password_confirmation"
                            name="new_password_confirmation"
                            required
                            minlength="8"
                            autocomplete="new-password"
                            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-600 focus:ring-0 focus:outline-none transition-colors"
                        >
                    </div>

                    <button type="submit"
                            class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-emerald-500 focus:outline-none transition-colors">
                        Réinitialiser le mot de passe
                    </button>
                </form>
            </div>

            <div class="border-t border-slate-100 px-6 py-4 text-center sm:px-8">
                <p class="text-xs text-slate-500">
                    <a href="{{ route('login') }}" class="font-medium text-emerald-700 hover:text-emerald-800 transition-colors">Retour à la connexion</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
