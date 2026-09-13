@extends('layouts.app')

@section('title', 'Détail email')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="flex items-center justify-between mb-4">
            <a href="{{ route('admin.send_email.index') }}" class="text-sm text-blue-600 hover:underline">← Retour aux emails</a>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $email->statusColor() }}">
                {{ $email->statusLabel() }}
            </span>
        </div>

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

        <div class="rounded-xl bg-white ring-1 ring-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h1 class="font-bold text-gray-900">{{ $email->subject }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    À : {{ $email->to_name ? $email->to_name.' ' : '' }}&lt;{{ $email->to_email }}&gt;
                    · {{ $email->created_at?->format('d/m/Y H:i') }}
                    @if ($email->sent_at)
                        · envoyé le {{ $email->sent_at->format('d/m/Y H:i') }}
                    @endif
                    · {{ $email->is_html ? 'HTML' : 'Texte brut' }}
                </p>
            </div>

            @if ($email->error)
                <div class="px-6 py-3 bg-red-50 border-b border-red-100 text-sm text-red-800">
                    <span class="font-medium">Erreur :</span> {{ $email->error }}
                </div>
            @endif

            <div class="p-6">
                @if ($email->is_html)
                    <div class="prose prose-sm max-w-none">{!! $email->body !!}</div>
                @else
                    <pre class="whitespace-pre-wrap text-sm text-gray-800">{{ $email->body }}</pre>
                @endif
            </div>

            @if ($email->status !== \Modules\SendEmail\Models\EmailLog::STATUS_SENT)
                <div class="px-6 py-4 border-t border-gray-100">
                    <form method="POST" action="{{ route('admin.send_email.resend', $email) }}">
                        @csrf
                        <button class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
                            Relancer l'envoi
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
