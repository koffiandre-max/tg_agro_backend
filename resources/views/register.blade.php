@extends('layouts.auth')

@section('title', 'Inscription')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 px-4 py-10 sm:px-6" x-data="registerForm()" x-cloak style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%232D6A4F&quot; fill-opacity=&quot;0.04&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
    <div class="w-full max-w-md animate-rise">
        {{-- Brand --}}
        <div class="mb-8 text-center">
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-sm">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4-2.5-6-6-6-10a6 6 0 0112 0c0 4-2 7.5-6 10z"/>
                    <path stroke-linecap="round" d="M12 21V9"/>
                </svg>
            </span>
            <h1 class="mt-4 text-lg font-semibold text-slate-900">{{ config('app.name', 'TG Agro') }}</h1>
            <p class="mt-0.5 text-sm text-slate-500">Créez votre compte client</p>
        </div>

        {{-- Card --}}
        <div class="rounded-xl bg-white border border-slate-200 shadow-sm">
            <div class="px-6 py-5 sm:px-8 sm:py-6">
                <div class="mb-6">
                    <h2 class="text-sm font-semibold text-slate-900">Inscription</h2>
                    <p class="mt-1 text-xs text-slate-500">Remplissez vos informations pour commencer.</p>
                </div>

                {{-- Error --}}
                <div class="mb-5" role="alert" aria-live="polite">
                    <div x-show="error" x-transition style="display: none;">
                        <div class="rounded-md bg-red-50 border border-red-100 px-3 py-2.5 text-xs text-red-700" x-text="error"></div>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-4" novalidate>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label for="name" class="mb-1.5 block text-xs font-medium text-slate-700">Nom complet</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                x-model="name"
                                required
                                autocomplete="name"
                                placeholder="Jean Kouassi"
                                class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-600 focus:ring-0 focus:outline-none transition-colors"
                            >
                        </div>

                        <div class="sm:col-span-2">
                            <label for="email" class="mb-1.5 block text-xs font-medium text-slate-700">Email</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                x-model="email"
                                required
                                autocomplete="email"
                                inputmode="email"
                                placeholder="vous@exemple.com"
                                class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-600 focus:ring-0 focus:outline-none transition-colors"
                            >
                        </div>

                        <div>
                            <label for="phone" class="mb-1.5 block text-xs font-medium text-slate-700">Téléphone</label>
                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                x-model="phone"
                                autocomplete="tel"
                                placeholder="+225 01 00 00 00 00"
                                class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-600 focus:ring-0 focus:outline-none transition-colors"
                            >
                        </div>

                        <div>
                            <label for="city_of_residence" class="mb-1.5 block text-xs font-medium text-slate-700">Ville</label>
                            <input
                                type="text"
                                id="city_of_residence"
                                name="city_of_residence"
                                x-model="city_of_residence"
                                placeholder="Abidjan"
                                class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-600 focus:ring-0 focus:outline-none transition-colors"
                            >
                        </div>

                        <div>
                            <label for="country_of_residence" class="mb-1.5 block text-xs font-medium text-slate-700">Pays de résidence</label>
                            <input
                                type="text"
                                id="country_of_residence"
                                name="country_of_residence"
                                x-model="country_of_residence"
                                placeholder="Côte d'Ivoire"
                                class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-600 focus:ring-0 focus:outline-none transition-colors"
                            >
                        </div>

                        <div>
                            <label for="country_of_origin" class="mb-1.5 block text-xs font-medium text-slate-700">Pays d'origine</label>
                            <input
                                type="text"
                                id="country_of_origin"
                                name="country_of_origin"
                                x-model="country_of_origin"
                                placeholder="Côte d'Ivoire"
                                class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-600 focus:ring-0 focus:outline-none transition-colors"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="mb-1.5 block text-xs font-medium text-slate-700">Mot de passe</label>
                            <div class="relative">
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    id="password"
                                    name="password"
                                    x-model="password"
                                    required
                                    autocomplete="new-password"
                                    placeholder="••••••••"
                                    class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 pr-10 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-600 focus:ring-0 focus:outline-none transition-colors"
                                >
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 transition-colors"
                                    :aria-label="showPassword ? 'Masquer' : 'Afficher'"
                                >
                                    <svg x-show="!showPassword" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7 -1.274 4.057-5.064 7 -9.542 7 -4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showPassword" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" style="display:none" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-1.5 block text-xs font-medium text-slate-700">Confirmer le mot de passe</label>
                            <div class="relative">
                                <input
                                    :type="showPasswordConfirm ? 'text' : 'password'"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    x-model="password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    placeholder="••••••••"
                                    class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 pr-10 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-600 focus:ring-0 focus:outline-none transition-colors"
                                >
                                <button
                                    type="button"
                                    @click="showPasswordConfirm = !showPasswordConfirm"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 transition-colors"
                                    :aria-label="showPasswordConfirm ? 'Masquer' : 'Afficher'"
                                >
                                    <svg x-show="!showPasswordConfirm" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7 -1.274 4.057-5.064 7 -9.542 7 -4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showPasswordConfirm" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" style="display:none" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button
                        type="submit"
                        x-bind:disabled="loading"
                        class="mt-1 flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 active:bg-emerald-800 disabled:cursor-not-allowed disabled:opacity-60 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-1"
                    >
                        <svg x-show="loading" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                            <path class="opacity-90" d="M22 12a10 10 0 00-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                        </svg>
                        <span x-text="loading ? 'Inscription…' : 'S\'inscrire'"></span>
                    </button>
                </form>
            </div>

            <div class="border-t border-slate-100 px-6 py-4 text-center sm:px-8">
                <p class="text-xs text-slate-500">
                    Vous avez déjà un compte ?
                    <a href="{{ route('login') }}" class="font-medium text-emerald-700 hover:text-emerald-800 transition-colors">Se connecter</a>
                </p>
            </div>
        </div>

        <p class="mt-6 text-center text-[11px] text-slate-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'TG Agro') }}
        </p>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    .animate-rise { animation: rise 600ms cubic-bezier(0.16, 1, 0.3, 1) both; }
    @keyframes rise {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @media (prefers-reduced-motion: reduce) {
        .animate-rise { animation: none; }
    }
</style>

<script>
    function registerForm() {
        return {
            name: @json(old('name', '')),
            email: @json(old('email', '')),
            phone: @json(old('phone', '')),
            country_of_residence: @json(old('country_of_residence', '')),
            country_of_origin: @json(old('country_of_origin', '')),
            city_of_residence: @json(old('city_of_residence', '')),
            password: '',
            password_confirmation: '',
            showPassword: false,
            showPasswordConfirm: false,
            loading: false,
            error: '',

            async submit() {
                if (this.loading) return;

                this.loading = true;
                this.error = '';

                if (this.password !== this.password_confirmation) {
                    this.error = 'Les mots de passe ne correspondent pas.';
                    this.loading = false;
                    return;
                }

                try {
                    const response = await fetch(@json(url('/register')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                        },
                        body: JSON.stringify({
                            name: this.name.trim(),
                            email: this.email.trim(),
                            phone: this.phone.trim(),
                            country_of_residence: this.country_of_residence.trim(),
                            country_of_origin: this.country_of_origin.trim(),
                            city_of_residence: this.city_of_residence.trim(),
                            password: this.password,
                            password_confirmation: this.password_confirmation,
                        }),
                    });

                    let data = {};
                    try { data = await response.json(); } catch {}

                    if (response.ok && data.success) {
                        window.location.href = data.redirect;
                        return;
                    }

                    if (response.status === 429) {
                        this.error = data.message || 'Trop de tentatives. Veuillez réessayer dans quelques instants.';
                        return;
                    }

                    if (response.status === 422 && data.errors) {
                        this.error = Object.values(data.errors).flat()[0] || 'Veuillez vérifier vos informations.';
                        return;
                    }

                    this.error = data.message || 'L\'inscription a échoué. Veuillez réessayer.';
                } catch {
                    this.error = 'Une erreur est survenue. Vérifiez votre connexion et réessayez.';
                } finally {
                    this.loading = false;
                }
            },
        };
    }
</script>
@endsection
