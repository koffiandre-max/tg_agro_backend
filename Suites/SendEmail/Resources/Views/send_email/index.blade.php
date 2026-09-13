@extends('layouts.app')

@section('title', 'Emails')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- En-tête + statistiques --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Emails</h1>
                <p class="text-sm text-gray-500">Composez vos emails et suivez leurs envois.</p>
            </div>
            <div class="flex gap-3 text-center">
                <div class="rounded-xl bg-white ring-1 ring-gray-200 px-4 py-3">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Envoyés</p>
                    <p class="text-lg font-bold text-green-600">{{ $stats['sent'] }}</p>
                </div>
                <div class="rounded-xl bg-white ring-1 ring-gray-200 px-4 py-3">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Échecs</p>
                    <p class="text-lg font-bold text-red-600">{{ $stats['failed'] }}</p>
                </div>
                <div class="rounded-xl bg-white ring-1 ring-gray-200 px-4 py-3">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Total</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        {{-- Messages flash / erreurs --}}
        @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Formulaire de composition --}}
        <div class="mb-8 rounded-xl bg-white ring-1 ring-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Nouvel email</h2>
            </div>
            <form method="POST" action="{{ route('admin.send_email.store') }}" class="p-5 space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Destinataire (email) *</label>
                        <input type="email" name="to_email" value="{{ old('to_email') }}" required
                               class="w-full rounded-lg border-gray-300 text-sm" placeholder="client@exemple.com">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nom du destinataire</label>
                        <input type="text" name="to_name" value="{{ old('to_name') }}"
                               class="w-full rounded-lg border-gray-300 text-sm" placeholder="Nom complet">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sujet *</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" required
                           class="w-full rounded-lg border-gray-300 text-sm" placeholder="Objet de votre email">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Message *</label>
                    <textarea name="body" rows="8" required
                              class="w-full rounded-lg border-gray-300 text-sm font-mono"
                              placeholder="Contenu du message (HTML ou texte)">{{ old('body') }}</textarea>
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="hidden" name="is_html" value="0">
                    <input type="checkbox" name="is_html" value="1" {{ old('is_html', true) ? 'checked' : '' }} class="rounded border-gray-300">
                    Interpréter le message comme du HTML
                </label>
                <div class="pt-2">
                    <button class="px-5 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
                        Envoyer l'email
                    </button>
                </div>
            </form>
        </div>

{{-- PART2 --}}

        {{-- Historique --}}
        <div class="rounded-xl bg-white ring-1 ring-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h2 class="font-semibold text-gray-900">Historique des envois</h2>
                <form method="GET" action="{{ route('admin.send_email.index') }}" class="flex gap-2">
                    <input type="text" name="q" value="{{ $search }}" placeholder="Rechercher (email, sujet)..."
                           class="rounded-lg border-gray-300 text-sm">
                    <button class="px-3 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm hover:bg-gray-200">Rechercher</button>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3 text-left">Date</th>
                            <th class="px-5 py-3 text-left">Destinataire</th>
                            <th class="px-5 py-3 text-left">Sujet</th>
                            <th class="px-5 py-3 text-left">Statut</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($emails as $email)
                            <tr>
                                <td class="px-5 py-3 whitespace-nowrap">{{ $email->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-3">
                                    {{ $email->to_name ? $email->to_name.' — ' : '' }}<span class="text-gray-500">{{ $email->to_email }}</span>
                                </td>
                                <td class="px-5 py-3 max-w-xs truncate">{{ $email->subject }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $email->statusColor() }}">
                                        {{ $email->statusLabel() }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('admin.send_email.show', $email) }}" class="text-blue-600 hover:underline text-xs">Détails</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-6 text-center text-gray-500">Aucun email envoyé pour le moment.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($emails->hasPages())
                <div class="px-5 py-3 border-t border-gray-100">{{ $emails->links() }}</div>
            @endif
        </div>

    </div>
</div>
@endsection
