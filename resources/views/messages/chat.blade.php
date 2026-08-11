@extends('layouts.app')

@section('title', $pageTitle ?? 'Messagerie')
@section('page-title', $pageTitle ?? 'Messagerie')

@push('styles')
<style>
@keyframes msgIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
@keyframes dotPulse { 0%, 80%, 100% { transform: scale(0.6); opacity: .4; } 40% { transform: scale(1); opacity: 1; } }
.msg-row { animation: msgIn 0.15s ease-out; }
.chat-box::-webkit-scrollbar { width: 5px; }
.chat-box::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.typing-dot {
    width: 6px; height: 6px; border-radius: 9999px; background: #94a3b8;
    display: inline-block; margin: 0 1px; animation: dotPulse 1.2s infinite ease-in-out;
}
.typing-dot:nth-child(2) { animation-delay: .15s; }
.typing-dot:nth-child(3) { animation-delay: .3s; }
.chat-list-item.active { background: #eef2ff; border-color: #c7d2fe; }
</style>
@endpush

@php
    $isAdmin = !empty($conversations);
    $peerName = $peer['name'] ?? 'Assistance TG\'AGRO';
    $peerInitials = $peer['initials'] ?? 'TG';
    $apiBase = $apiBase ?? '/portail/messages';
@endphp

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $isAdmin ? 'Support Client' : 'Chat Page' }}</h1>
                <p class="mt-2 text-sm text-gray-600">{{ $isAdmin ? 'Gérez les conversations de vos clients' : 'Communiquez avec le support TG\'AGRO' }}</p>
            </div>
            <nav class="hidden sm:flex items-center gap-2 text-sm text-gray-400">
                <span>Home</span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                <span class="text-gray-700 font-medium">{{ $isAdmin ? 'Support' : 'Chat Page' }}</span>
            </nav>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-5 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden" style="height: 600px;">

            {{-- ===== Colonne gauche : liste des conversations ===== --}}
            <div class="border-r border-gray-100 flex flex-col min-h-0">
                <div class="p-5 pb-3 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Chats</h2>
                    <button type="button" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4z"/></svg>
                    </button>
                </div>
                <div class="px-5 pb-4">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                        </svg>
                        <input type="text" placeholder="Search..." oninput="filterConversations(this.value)" class="w-full bg-gray-50 border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto chat-box px-3 pb-3 space-y-1" id="conversationList">
                    @if($isAdmin)
                        @forelse($conversations as $conv)
                            <div class="chat-list-item flex items-center gap-3 p-2.5 rounded-xl border border-transparent cursor-pointer hover:bg-gray-50"
                                 data-user-id="{{ $conv['user']['id'] }}"
                                 data-user-name="{{ $conv['user']['name'] }}"
                                 data-user-email="{{ $conv['user']['email'] ?? '' }}"
                                 onclick="loadConversation({{ $conv['user']['id'] }}, '{{ addslashes($conv['user']['name']) }}', '{{ addslashes($conv['user']['email'] ?? '') }}')">
                                <div class="relative shrink-0">
                                    <div class="h-11 w-11 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-700 font-bold text-sm">{{ strtoupper(substr($conv['user']['name'], 0, 1)) }}</div>
                                    <span class="absolute bottom-0 right-0 h-2.5 w-2.5 bg-emerald-500 rounded-full border-2 border-white"></span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $conv['user']['name'] }}</p>
                                    <p class="text-xs text-gray-500 truncate conv-preview">{{ $conv['last_message'] ? \Illuminate\Support\Str::limit($conv['last_message'], 28) : 'Support en ligne' }}</p>
                                </div>
                                <span class="text-[11px] text-gray-400 shrink-0 conv-time">{{ $conv['last_time'] ?? '' }}</span>
                                @if(!empty($conv['unread_count']))
                                    <span class="conv-unread flex-shrink-0 inline-flex items-center justify-center h-4 min-w-[16px] rounded-full bg-emerald-600 px-1 text-[9px] font-bold text-white">{{ $conv['unread_count'] }}</span>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 text-center py-10">Aucune conversation</p>
                        @endforelse
                    @else
                        <div class="chat-list-item active flex items-center gap-3 p-2.5 rounded-xl border border-transparent cursor-pointer">
                            <div class="relative shrink-0">
                                <div class="h-11 w-11 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-700 font-bold text-sm">{{ $peerInitials }}</div>
                                <span class="absolute bottom-0 right-0 h-2.5 w-2.5 bg-emerald-500 rounded-full border-2 border-white"></span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $peerName }}</p>
                                <p class="text-xs text-gray-500 truncate" id="sidebarLastMessage">
                                    @if(!empty($messages) && $messages->count())
                                        {{ \Illuminate\Support\Str::limit($messages->last()->message, 28) }}
                                    @else
                                        Support en ligne
                                    @endif
                                </p>
                            </div>
                            <span class="text-[11px] text-gray-400 shrink-0">
                                {{ !empty($messages) && $messages->count() ? $messages->last()->created_at->diffForHumans(null, true) : '' }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ===== Colonne droite : fenêtre de discussion ===== --}}
            <div class="flex flex-col min-h-0">
                <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="relative shrink-0">
                            <div class="h-10 w-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-700 font-bold text-sm">{{ $peerInitials }}</div>
                            <span class="absolute bottom-0 right-0 h-2.5 w-2.5 bg-emerald-500 rounded-full border-2 border-white"></span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900" id="chatHeaderName">{{ $peerName }}</p>
                            <p class="text-xs text-gray-500" id="statusLine">Support en ligne</p>
                            <p class="text-[10px] text-emerald-600 hidden" id="typingIndicator">en train d'écrire...</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 text-gray-400">
                        <button type="button" class="hover:text-gray-600" title="Appel">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h2.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                        </button>
                        <button type="button" class="hover:text-gray-600" title="Appel vidéo">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                        </button>
                        <button type="button" class="hover:text-gray-600" title="Options">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4z"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex-1 min-h-0 p-4 sm:p-6 overflow-y-auto space-y-5 chat-box" id="chatMessages">
                    @if($isAdmin)
                        <p class="text-sm text-gray-400 text-center py-10" id="emptyConversation">Sélectionnez une conversation pour commencer.</p>
                    @else
                        @forelse($messages as $message)
                            @php $isMine = $message->sender_id === auth()->id(); @endphp
                            <div class="msg-row flex items-start gap-2.5 max-w-[80%] {{ $isMine ? 'ml-auto flex-row-reverse' : '' }}">
                                @unless($isMine)
                                    <div class="h-9 w-9 rounded-full text-xs font-bold shrink-0 flex items-center justify-center bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <span>{{ $peerInitials[0] ?? 'S' }}</span>
                                    </div>
                                @endunless
                                <div class="flex flex-col {{ $isMine ? 'items-end' : 'items-start' }}">
                                    <div class="px-4 py-2.5 text-sm rounded-2xl {{ $isMine ? 'bg-indigo-600 text-white rounded-tr-sm' : 'bg-gray-100 text-gray-800 rounded-tl-sm' }}">
                                        <p class="leading-relaxed whitespace-pre-line">{{ $message->message }}</p>
                                    </div>
                                    <span class="text-[11px] text-gray-400 mt-1.5">
                                        {{ $message->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 text-center py-10" id="emptyMessage">Aucun message pour le moment.</p>
                        @endforelse
                    @endif

                    {{-- Indicateur de saisie (typing) --}}
                    <div id="typingIndicatorContainer" class="msg-row items-start gap-2.5 max-w-[80%]" style="display: none;">
                        <div class="h-9 w-9 rounded-full text-xs font-bold shrink-0 flex items-center justify-center bg-emerald-50 text-emerald-700 border border-emerald-100">
                            <span>{{ $peerInitials[0] ?? 'S' }}</span>
                        </div>
                        <div class="flex flex-col items-start">
                            <div class="px-4 py-3 rounded-2xl bg-gray-100 text-gray-800 rounded-tl-sm">
                                <div class="flex items-center gap-1">
                                    <span class="typing-dot"></span>
                                    <span class="typing-dot"></span>
                                    <span class="typing-dot"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-3 sm:p-4 bg-white border-t border-gray-100 flex items-center gap-3 shrink-0">
                    <button type="button" class="text-gray-400 hover:text-gray-600 shrink-0" title="Emoji">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9" /><path stroke-linecap="round" d="M9 10h.01M15 10h.01M8.5 14.5a4 4 0 007 0" /></svg>
                    </button>
                    <input type="text" id="messageInput" required placeholder="Type a message"
                        class="flex-1 bg-transparent text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none" {{ $isAdmin && empty($activeUserId) ? 'disabled' : '' }}>
                    <button type="button" class="text-gray-400 hover:text-gray-600 shrink-0" title="Joindre un fichier">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.44 11.05l-9.19 9.19a5 5 0 01-7.07-7.07l9.19-9.19a3.5 3.5 0 014.95 4.95l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48" /></svg>
                    </button>
                    <button type="button" class="text-gray-400 hover:text-gray-600 shrink-0" title="Message vocal">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3zM19 10v2a7 7 0 01-14 0v-2M12 19v4" /></svg>
                    </button>
                    <button type="button" id="sendBtn" class="h-10 w-10 bg-indigo-600 text-white rounded-full flex items-center justify-center hover:bg-indigo-700 active:scale-95 transition-all shrink-0 shadow-sm">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
const apiBase = '{{ $apiBase }}';
const isAdmin = {{ $isAdmin ? 'true' : 'false' }};
let currentUserId = {{ $activeUserId ?? 'null' }};
let typingTimer = null;
let pollTimer = null;

function getListUrl() {
    if (isAdmin) return `${apiBase}/${currentUserId}/list`;
    return '/chat/messages';
}

function startPolling() {
    if (pollTimer) clearInterval(pollTimer);
    pollTimer = setInterval(pollMessages, 3000);
}

function stopPolling() {
    if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
}

function pollMessages() {
    if (!currentUserId && isAdmin) {
        pollConversations();
        return;
    }
    fetch(getListUrl(), {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        credentials: 'same-origin',
    })
    .then(r => r.json())
    .then(messages => {
        if (!Array.isArray(messages)) return;
        const container = document.getElementById('chatMessages');
        const localIds = new Set();
        container.querySelectorAll('.msg-row[data-msg-id]').forEach(el => localIds.add(parseInt(el.dataset.msgId)));
        const newMsgs = messages.filter(m => m.id && !localIds.has(m.id));
        if (newMsgs.length > 0) {
            newMsgs.forEach(msg => {
                if (!msg.is_mine) handleNewMessage(msg);
            });
            scrollToBottom();
        }
    })
    .catch(() => {});
    if (isAdmin) pollConversations();
}

function pollConversations() {
    fetch(`${apiBase}/conversations`, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    })
    .then(r => r.json())
    .then(conversations => {
        const list = document.getElementById('conversationList');
        conversations.forEach(conv => {
            const item = list.querySelector(`.chat-list-item[data-user-id="${conv.user.id}"]`);
            if (item) {
                const preview = item.querySelector('.conv-preview');
                const time = item.querySelector('.conv-time');
                const badge = item.querySelector('.conv-unread');
                if (preview) preview.textContent = (conv.last_message ? conv.last_message.substring(0, 28) : 'Support en ligne');
                if (time) time.textContent = conv.last_time || '';
                if (conv.unread_count > 0 && currentUserId !== conv.user.id) {
                    if (badge) badge.textContent = conv.unread_count;
                    else item.insertAdjacentHTML('beforeend', `<span class="conv-unread flex-shrink-0 inline-flex items-center justify-center h-4 min-w-[16px] rounded-full bg-emerald-600 px-1 text-[9px] font-bold text-white">${conv.unread_count}</span>`);
                } else if (badge) {
                    badge.remove();
                }
            } else {
                location.reload();
            }
        });
    })
    .catch(() => {});
}

function filterConversations(query) {
    document.querySelectorAll('.chat-list-item').forEach(item => {
        const name = (item.dataset.userName || '').toLowerCase();
        const email = (item.dataset.userEmail || '').toLowerCase();
        const q = query.toLowerCase().trim();
        item.style.display = (!q || name.includes(q) || email.includes(q)) ? 'flex' : 'none';
    });
}

let autoScroll = true;

function scrollToBottom() {
    const container = document.getElementById('chatMessages');
    if (!container) return;
    if (autoScroll) container.scrollTop = container.scrollHeight;
}

function initAutoScroll() {
    const container = document.getElementById('chatMessages');
    if (!container) return;
    container.addEventListener('scroll', function() {
        const threshold = 40;
        autoScroll = (container.scrollHeight - container.scrollTop - container.clientHeight) < threshold;
    });
    autoScroll = true;
    scrollToBottom();
}

function showTyping() {
    const container = document.getElementById('typingIndicatorContainer');
    if (container) {
        container.style.display = 'flex';
        clearTimeout(container._timeout);
        container._timeout = setTimeout(() => { container.style.display = 'none'; }, 4000);
    }
}

function hideTyping() {
    const container = document.getElementById('typingIndicatorContainer');
    if (container) { container.style.display = 'none'; clearTimeout(container._timeout); }
}

function handleNewMessage(msg) {
    const empty = document.getElementById('emptyMessage') || document.getElementById('emptyConversation');
    if (empty) empty.remove();

    const container = document.getElementById('chatMessages');
    const div = document.createElement('div');
    div.className = 'msg-row flex items-start gap-2.5 max-w-[80%]';
    if (msg.id) div.dataset.msgId = msg.id;
    div.innerHTML = `
        <div class="h-9 w-9 rounded-full text-xs font-bold shrink-0 flex items-center justify-center bg-emerald-50 text-emerald-700 border border-emerald-100">
            <span>S</span>
        </div>
        <div class="flex flex-col items-start">
            <div class="px-4 py-2.5 text-sm rounded-2xl bg-gray-100 text-gray-800 rounded-tl-sm">
                <p class="leading-relaxed whitespace-pre-line">${escHtml(msg.message)}</p>
            </div>
            <span class="text-[11px] text-gray-400 mt-1.5">${msg.time || ''}</span>
        </div>
    `;
    container.appendChild(div);
    scrollToBottom();
}

function sendTyping(typing) {
    if (isAdmin && !currentUserId) return;
    const url = isAdmin ? `${apiBase}/${currentUserId}/typing` : apiBase + '/typing';
    fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ typing }),
    }).catch(console.error);
}

function markAsRead(userId) {
    fetch(`${apiBase}/${userId}/read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } }).catch(console.error);
    const item = document.querySelector(`.chat-list-item[data-user-id="${userId}"]`);
    if (item) { const b = item.querySelector('.conv-unread'); if (b) b.remove(); }
}

function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    if (!message) return;
    if (isAdmin && !currentUserId) return;

    const formData = new FormData();
    formData.append('message', message);
    formData.append('_token', csrfToken);
    sendTyping(false);
    clearTimeout(typingTimer);

    const sendUrl = isAdmin ? `${apiBase}/${currentUserId}` : apiBase;

    fetch(sendUrl, {
        method: 'POST',
        headers: { 'Accept': 'application/json' },
        body: formData,
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const empty = document.getElementById('emptyMessage') || document.getElementById('emptyConversation');
            if (empty) empty.remove();

            const container = document.getElementById('chatMessages');
            const div = document.createElement('div');
            div.className = 'msg-row flex items-start gap-2.5 max-w-[80%] ml-auto flex-row-reverse';
            if (data.message?.id) div.dataset.msgId = data.message.id;
            div.innerHTML = `
                <div class="flex flex-col items-end">
                    <div class="px-4 py-2.5 text-sm rounded-2xl bg-indigo-600 text-white rounded-tr-sm">
                        <p class="leading-relaxed whitespace-pre-line">${escHtml(message)}</p>
                    </div>
                    <span class="text-[11px] text-gray-400 mt-1.5">${data.message?.time || "À l'instant"}</span>
                </div>
            `;
            container.appendChild(div);
            input.value = '';
            autoScroll = true;
            scrollToBottom();
        }
    })
    .catch(console.error);
}

function loadConversation(userId, userName, userEmail) {
    currentUserId = userId;
    document.getElementById('chatHeaderName').textContent = userName || 'Utilisateur';
    document.getElementById('typingIndicator').classList.add('hidden');
    const input = document.getElementById('messageInput');
    input.disabled = false;

    document.querySelectorAll('.chat-list-item').forEach(i => i.classList.remove('active'));
    const item = document.querySelector(`.chat-list-item[data-user-id="${userId}"]`);
    if (item) item.classList.add('active');

    const chatMessages = document.getElementById('chatMessages');
    chatMessages.innerHTML = '<p class="text-sm text-gray-400 text-center py-10">Chargement...</p>';

    fetch(`${apiBase}/${userId}/list`, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    })
    .then(r => r.json())
    .then(messages => {
        chatMessages.innerHTML = '';
        if (!messages.length) {
            chatMessages.innerHTML = '<p class="text-sm text-gray-400 text-center py-10" id="emptyMessage">Aucun message pour le moment.</p>';
        } else {
            messages.forEach(msg => {
                const div = document.createElement('div');
                div.className = 'msg-row flex items-start gap-2.5 max-w-[80%] ' + (msg.is_mine ? 'ml-auto flex-row-reverse' : '');
                if (msg.id) div.dataset.msgId = msg.id;
                div.innerHTML = `
                    ${msg.is_mine ? '' : '<div class="h-9 w-9 rounded-full text-xs font-bold shrink-0 flex items-center justify-center bg-emerald-50 text-emerald-700 border border-emerald-100"><span>S</span></div>'}
                    <div class="flex flex-col ${msg.is_mine ? 'items-end' : 'items-start'}">
                        <div class="px-4 py-2.5 text-sm rounded-2xl ${msg.is_mine ? 'bg-indigo-600 text-white rounded-tr-sm' : 'bg-gray-100 text-gray-800 rounded-tl-sm'}">
                            <p class="leading-relaxed whitespace-pre-line">${escHtml(msg.message)}</p>
                        </div>
                        <span class="text-[11px] text-gray-400 mt-1.5">${msg.time || ''}</span>
                    </div>
                `;
                chatMessages.appendChild(div);
            });
        }
        chatMessages.insertAdjacentHTML('beforeend', `
            <div id="typingIndicatorContainer" class="msg-row items-start gap-2.5 max-w-[80%]" style="display: none;">
                <div class="h-9 w-9 rounded-full text-xs font-bold shrink-0 flex items-center justify-center bg-emerald-50 text-emerald-700 border border-emerald-100"><span>S</span></div>
                <div class="flex flex-col items-start">
                    <div class="px-4 py-3 rounded-2xl bg-gray-100 text-gray-800 rounded-tl-sm">
                        <div class="flex items-center gap-1"><span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span></div>
                    </div>
                </div>
            </div>
        `);
        markAsRead(userId);
        autoScroll = true;
        scrollToBottom();
    })
    .catch(() => {
        chatMessages.innerHTML = '<p class="text-sm text-red-500 text-center py-10">Erreur de chargement.</p>';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    startPolling();
    initAutoScroll();

    document.getElementById('sendBtn').addEventListener('click', sendMessage);
    document.getElementById('messageInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
    });

    const input = document.getElementById('messageInput');
    input.addEventListener('input', function() {
        sendTyping(true);
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => sendTyping(false), 2000);
    });

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
