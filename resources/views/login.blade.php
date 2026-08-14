@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')
<div
    class="min-h-screen flex flex-col items-center justify-center px-4 py-10 sm:px-6"
    x-data="loginForm()"
    x-cloak
>
    {{-- En-tête marque --}}
    <div class="mb-8 text-center">
        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-600 shadow-lg shadow-emerald-600/25">
            <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ config('app.name', 'TG Agro') }}</h1>
        <p class="mt-1 text-sm text-gray-500">Plateforme de gestion agricole</p>
    </div>

    {{-- Carte login --}}
    <x-ui.card class="w-full max-w-sm" :padding="false">
        <div class="h-1 bg-emerald-600 rounded-t-xl"></div>

        <div class="p-6 sm:p-8">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900">Connexion</h2>
                <p class="mt-1 text-sm text-gray-500">Accédez à votre espace de travail</p>
            </div>

            <div
                x-show="error"
                x-transition
                class="mb-5"
                style="display: none;"
            >
                <x-ui.alert type="error" x-bind:message="error" />
            </div>

            @if($errors->any())
                <div class="mb-5">
                    <x-ui.alert type="error" :message="$errors->first()" />
                </div>
            @endif

            <form @submit.prevent="submit" class="space-y-4" novalidate>
                <x-ui.input
                    type="email"
                    name="email"
                    label="Adresse email"
                    placeholder="vous@exemple.com"
                    required
                    x-model="email"
                    :value="old('email')"
                    :icon="'<svg class=\'h-5 w-5\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\' stroke-width=\'1.75\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z\'/></svg>'"
                />

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Mot de passe <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            id="password"
                            name="password"
                            x-model="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="block bg-white w-full rounded-md border border-slate-300 text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 sm:text-sm transition-colors pl-10 pr-10 py-2.5"
                        >
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                            :aria-label="showPassword ? 'Masquer' : 'Afficher'"
                        >
                            <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            x-model="remember"
                            class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                        >
                        <span class="text-sm text-gray-600">Se souvenir de moi</span>
                    </label>
                    <a href="#" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 transition-colors">
                        Mot de passe oublié ?
                    </a>
                </div>

                <x-ui.btn
                    type="submit"
                    variant="success"
                    class="w-full mt-2"
                    x-bind:loading="loading"
                    x-bind:disabled="loading"
                >
                    <span x-text="loading ? 'Connexion...' : 'Se connecter'"></span>
                </x-ui.btn>
            </form>
        </div>
    </x-ui.card>

    <p class="mt-8 text-center text-xs text-gray-400">
        &copy; {{ date('Y') }} {{ config('app.name', 'TG Agro') }}
    </p>
</div>

<style>[x-cloak] { display: none !important; }</style>

<script>
    function loginForm() {
        return {
            email: @json(old('email', '')),
            password: '',
            remember: false,
            showPassword: false,
            loading: false,
            error: '',

            async submit() {
                this.loading = true;
                this.error = '';

                try {
                    const response = await fetch(@json(url('/login')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            email: this.email,
                            password: this.password,
                            remember: this.remember,
                        }),
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        window.location.href = data.redirect;
                        return;
                    }

                    if (response.status === 422 && data.errors) {
                        this.error = Object.values(data.errors).flat()[0] || 'Veuillez vérifier vos informations.';
                        return;
                    }

                    this.error = data.message || 'Email ou mot de passe incorrect.';
                } catch {
                    this.error = 'Une erreur est survenue. Veuillez réessayer.';
                } finally {
                    this.loading = false;
                }
            },
        };
    }
</script>
@endsection
