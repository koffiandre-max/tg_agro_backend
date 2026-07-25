@extends('layouts.app')

@section('title', 'Messagerie')
@section('page-title', 'Messagerie')

@push('styles')
<style>
@keyframes msgIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
@keyframes dotPulse { 0%, 80%, 100% { transform: scale(0.6); } 40% { transform: scale(1); } }
.msg-row { animation: msgIn 0.15s ease-out; }
.chat-box::-webkit-scrollbar { width: 5px; }
.chat-box::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Messagerie</h1>
            <p class="mt-2 text-sm text-gray-600">Communiquez avec le support TG'AGRO</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center gap-3">
                <div class="h-10 w-10 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-700 font-bold text-sm">TG</div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">Assistance TG'AGRO</p>
                    <p class="text-xs text-gray-500">Support en ligne</p>
                    <p class="text-[10px] text-emerald-600 hidden" id="typingIndicator">en train d'écrire...</p>
                </div>
            </div>

            <div class="p-4 h-[400px] overflow-y-auto space-y-3 bg-slate-50 chat-box" id="chatMessages">
                @forelse($messages as $message)
                    @php $isMine = $message->sender_id === auth()->id(); @endphp
                    <div class="msg-row flex items-start gap-2.5 max-w-[85%] {{ $isMine ? 'ml-auto flex-row-reverse' : '' }}">
                        <div class="h-8 w-8 rounded-lg text-xs font-bold shrink-0 flex items-center justify-center {{ $isMine ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100' }}">
                            <span>{{ $isMine ? 'M' : 'S' }}</span>
                        </div>
                        <div class="flex flex-col {{ $isMine ? 'items-end' : 'items-start' }}">
                            <div class="p-3 text-sm rounded-2xl shadow-sm border {{ $isMine ? 'bg-emerald-600 text-white border-emerald-600 rounded-tr-none' : 'bg-white text-slate-800 border-slate-200/60 rounded-tl-none' }}">
                                <p class="leading-relaxed whitespace-pre-line">{{ $message->message }}</p>
                            </div>
                            <span class="text-[9px] text-slate-400 mt-1">{{ $message->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-10" id="emptyMessage">Aucun message pour le moment.</p>
                @endforelse
            </div>

            <div class="p-3 bg-white border-t border-slate-150 flex items-center gap-2 shrink-0">
                <input type="text" id="messageInput" required placeholder="Saisissez votre message..."
                    class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                <button type="button" id="sendBtn" class="h-10 w-10 bg-emerald-600 text-white rounded-xl flex items-center justify-center hover:bg-emerald-700 active:scale-95 transition-all shrink-0 shadow-sm">
                    <svg class="h-4 w-4 transform rotate-45 -translate-x-0.5 translate-y-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
let sseSource = null;
let typingTimer = null;

function connectSSE() {
    if (sseSource) sseSource.close();
    sseSource = new EventSource('/portail/messages/sse');

    sseSource.addEventListener('message', function(e) {
        const data = JSON.parse(e.data);
        if (data.messages && data.messages.length) {
            data.messages.forEach(handleNewMessage);
        }
    });

    sseSource.addEventListener('typing', function(e) {
        const data = JSON.parse(e.data);
        const indicator = document.getElementById('typingIndicator');
        if (data && data.length) {
            indicator.classList.remove('hidden');
            clearTimeout(indicator._timeout);
            indicator._timeout = setTimeout(() => indicator.classList.add('hidden'), 3000);
        } else {
            indicator.classList.add('hidden');
        }
    });

    sseSource.onerror = function() {
        setTimeout(connectSSE, 3000);
    };
}

function handleNewMessage(msg) {
    const empty = document.getElementById('emptyMessage');
    if (empty) empty.remove();

    const container = document.getElementById('chatMessages');
    const div = document.createElement('div');
    div.className = 'msg-row flex items-start gap-2.5 max-w-[85%]';
    div.innerHTML = `
        <div class="h-8 w-8 rounded-lg text-xs font-bold shrink-0 flex items-center justify-center bg-emerald-50 text-emerald-700 border border-emerald-100">
            <span>S</span>
        </div>
        <div class="flex flex-col items-start">
            <div class="p-3 text-sm rounded-2xl shadow-sm border bg-white text-slate-800 border-slate-200/60 rounded-tl-none">
                <p class="leading-relaxed whitespace-pre-line">${escHtml(msg.message)}</p>
            </div>
            <span class="text-[9px] text-slate-400 mt-1">${msg.time || ''}</span>
        </div>
    `;
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}

function sendTyping(typing) {
    fetch('/portail/messages/typing', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ typing }),
    }).catch(console.error);
}

function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    if (!message) return;

    const formData = new FormData();
    formData.append('message', message);
    formData.append('_token', csrfToken);
    sendTyping(false);
    clearTimeout(typingTimer);

    fetch('/portail/messages', {
        method: 'POST',
        headers: { 'Accept': 'application/json' },
        body: formData,
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const empty = document.getElementById('emptyMessage');
            if (empty) empty.remove();

            const container = document.getElementById('chatMessages');
            const div = document.createElement('div');
            div.className = 'msg-row flex items-start gap-2.5 max-w-[85%] ml-auto flex-row-reverse';
            div.innerHTML = `
                <div class="h-8 w-8 rounded-lg text-xs font-bold shrink-0 flex items-center justify-center bg-indigo-50 text-indigo-700 border border-indigo-100">
                    <span>M</span>
                </div>
                <div class="flex flex-col items-end">
                    <div class="p-3 text-sm rounded-2xl shadow-sm border bg-emerald-600 text-white border-emerald-600 rounded-tr-none">
                        <p class="leading-relaxed whitespace-pre-line">${escHtml(message)}</p>
                    </div>
                    <span class="text-[9px] text-slate-400 mt-1">${data.message?.time || "À l'instant"}</span>
                </div>
            `;
            container.appendChild(div);
            input.value = '';
            container.scrollTop = container.scrollHeight;
        }
    })
    .catch(console.error);
}

document.addEventListener('DOMContentLoaded', function() {
    connectSSE();

    document.getElementById('sendBtn').addEventListener('click', sendMessage);
    document.getElementById('messageInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    const input = document.getElementById('messageInput');
    input.addEventListener('input', function() {
        sendTyping(true);
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => sendTyping(false), 2000);
    });

    // Scroll to bottom
    const container = document.getElementById('chatMessages');
    container.scrollTop = container.scrollHeight;
});

function escHtml(str) {
    if (!str) return '';
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}
</script>
@endpush