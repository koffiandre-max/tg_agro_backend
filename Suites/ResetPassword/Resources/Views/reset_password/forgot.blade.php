@extends('layouts.auth')
@section('title', 'Mot de passe oublié')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 px-4 py-10 sm:px-6">
    <div class="w-full max-w-sm">
        <div class="mb-8 text-center">
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-sm">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </span>
            <h1 class="mt-4 text-lg font-semibold text-slate-900">{{ config('app.name', 'TG Agro') }}</h1>
            <p class="mt-0.5 text-sm text-slate-500">Réinitialisation du mot de passe</p>
        </div>

        <div class="rounded-xl bg-white border border-slate-200 shadow-sm">
            <div class="px-6 py-5 sm:px-8 sm:py-6">
                <div class="mb-6">
                    <h2 class="text-sm font-semibold text-slate-900">Mot de passe oublié ?</h2>
                    <p class="mt-1 text-xs text-slate-500">Saisissez votre adresse email pour recevoir un lien de réinitialisation.</p>
                </div>

                @if (session('status'))
                    <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-100 px-3 py-2.5 text-xs text-emerald-700" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-md bg-red-50 border border-red-100 px-3 py-2.5 text-xs text-red-700" role="alert">
                        <ul class="list-disc space-y-1 pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('reset_password.email') }}" class="space-y-4" novalidate>
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-xs font-medium text-slate-700">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            inputmode="email"
                            placeholder="vous@exemple.com"
                            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-600 focus:ring-0 focus:outline-none transition-colors"
                        >
                    </div>

                    <button type="submit"
                            class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-emerald-500 focus:outline-none transition-colors">
                        Envoyer le lien
                    </button>
                </form>
            </div>

            <div class="border-t border-slate-100 px-6 py-4 text-center sm:px-8">
                <p class="text-xs text-slate-500">
                    Vous vous souvenez de votre mot de passe ?
                    <a href="{{ route('login') }}" class="font-medium text-emerald-700 hover:text-emerald-800 transition-colors">Se connecter</a>
                </p>
            </div>
        </div>

        <p class="mt-6 text-center text-[11px] text-slate-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'TG Agro') }}
        </p>
    </div>
</div>
@endsection
