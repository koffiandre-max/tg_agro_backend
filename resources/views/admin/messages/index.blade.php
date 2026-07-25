@extends('layouts.app')

@section('page-title', 'Messages - Support')

@push('styles')
<style>
.chat-container { height: calc(100vh - 120px); display: flex; background: white; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.08); overflow: hidden; }
.conv-sidebar { width: 320px; border-right: 1px solid #e5e7eb; display: flex; flex-direction: column; background: #f9fafb; }
.conv-list { flex: 1; overflow-y: auto; }
.conv-list::-webkit-scrollbar { width: 4px; }
.conv-list::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
.chat-messages { flex: 1; overflow-y: auto; background: #eef2f5; display: flex; flex-direction: column; gap: 4px; }
.chat-messages::-webkit-scrollbar { width: 5px; }
.chat-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
@keyframes msgIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
@keyframes toastIn { from { opacity: 0; transform: translateX(100px); } to { opacity: 1; transform: translateX(0); } }
@keyframes toastOut { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(100px); } }
@keyframes dotPulse { 0%, 80%, 100% { transform: scale(0.6); } 40% { transform: scale(1); } }
.msg-row { display: flex; animation: msgIn 0.15s ease-out; }
.typing-dot { width: 7px; height: 7px; border-radius: 50%; background: #9ca3af; display: inline-block; animation: dotPulse 1.4s infinite ease-in-out both; }
.typing-dot:nth-child(1) { animation-delay: -0.32s; }
.typing-dot:nth-child(2) { animation-delay: -0.16s; }
.toast-msg { animation: toastIn 0.3s ease-out; }
.toast-msg.hide { animation: toastOut 0.3s ease-in forwards; }
</style>
@endpush

@section('content')
<div class="chat-container">
    {{-- Sidebar --}}
    <div class="conv-sidebar">
        <div class="px-4 pt-4 pb-3 bg-white border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900 mb-2">💬 Messages</h2>
            <div class="relative">
                <svg class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 0114 0z" />
                </svg>
                <input type="text" id="conversationSearch" placeholder="Rechercher..." oninput="filterConversations(this.value)" class="w-full pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:bg-white">
            </div>
        </div>
        <div class="conv-list p-1.5" id="conversationList">
            <div class="text-center text-gray-400 text-xs mt-8">Chargement...</div>
        </div>
    </div>

    {{-- Chat --}}
    <div class="flex-1 flex flex-col bg-white">
        <div class="px-5 py-2.5 border-b border-gray-100 flex items-center gap-2.5 bg-white" id="chatHeader">
            <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-sm flex-shrink-0" id="chatAvatar">?</div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-bold text-gray-900" id="chatUserName">Sélectionnez une conversation</h3>
                <p class="text-[10px] text-gray-500" id="chatUserEmail"></p>
                <p class="text-[10px] text-emerald-600 hidden" id="chatTypingIndicator">en train d'écrire...</p>
            </div>
        </div>

        <div class="chat-messages px-4 py-3" id="chatMessages">
            <div class="text-center text-gray-400 text-xs mt-8">👈 Sélectionnez une conversation</div>
            {{-- Indicateur de saisie (typing) - style WhatsApp --}}
            <div id="typingIndicatorContainer" class="msg-row items-start gap-2.5 max-w-[70%]" style="display: none;">
                <div class="h-8 w-8 rounded-lg text-xs font-bold shrink-0 flex items-center justify-center bg-emerald-50 text-emerald-700 border border-emerald-100">
                    <span>?</span>
                </div>
                <div class="flex flex-col items-start">
                    <div class="px-4 py-3 rounded-2xl bg-white text-slate-800 border border-slate-200/60 rounded-tl-none shadow-sm">
                        <div class="flex items-center gap-1">
                            <span class="typing-dot"></span>
                            <span class="typing-dot"></span>
                            <span class="typing-dot"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-5 py-3 bg-white border-t border-gray-100" id="chatInputArea" style="display: none;">
            <form class="flex items-end gap-2" id="chatForm">
                @csrf
                <textarea name="message" rows="1" placeholder="Écrivez un message..." class="flex-1 border border-gray-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none bg-gray-50 focus:bg-white max-h-24" required></textarea>
                <button type="submit" class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center hover:bg-emerald-700 transition-colors flex-shrink-0 hover:scale-105 active:scale-95">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Toast container --}}
<div id="toastContainer" class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 max-w-sm"></div>
@endsection

@push('scripts')
<script>
let currentUserId = null;
let sseSource = null;
let typingTimer = null;
let pollTimer = null;
let lastMessageCount = {};
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

// ===== Polling temps réel (remplace SSE pour fiabilité) =====
function startPolling() {
    if (pollTimer) clearInterval(pollTimer);
    pollTimer = setInterval(pollNewData, 3000);
}

function stopPolling() {
    if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
}

function pollNewData() {
    if (!currentUserId) {
        pollConversations();
        return;
    }
    // Refresh messages for active conversation
    fetch(`/messages/${currentUserId}/list`, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    })
    .then(r => r.json())
    .then(messages => {
        const chatMessages = document.getElementById('chatMessages');
        const localMsgs = chatMessages.querySelectorAll('.msg-row[data-msg-id]');
        const localIds = new Set();
        localMsgs.forEach(el => localIds.add(parseInt(el.dataset.msgId)));

        const newMsgs = messages.filter(m => m.id && !localIds.has(m.id));
        if (newMsgs.length > 0) {
            newMsgs.forEach(msg => {
                appendMessage(msg, msg.is_mine);
                if (!msg.is_mine) {
                    showToast({
                        message: msg.message,
                        sender_name: msg.sender_name || 'Utilisateur',
                        sender_id: msg.sender_id
                    });
                }
            });
            scrollToBottom();
            markAsRead(currentUserId);
        }
    })
    .catch(() => {});
    pollConversations();
}

function pollConversations() {
    fetch('/messages/conversations', {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    })
    .then(r => r.json())
    .then(conversations => {
        const list = document.getElementById('conversationList');
        const existingItems = list.querySelectorAll('.conv-item');
        const existingIds = new Set();
        existingItems.forEach(item => existingIds.add(parseInt(item.dataset.userId)));

        // Update existing items
        conversations.forEach(conv => {
            const item = list.querySelector(`.conv-item[data-user-id="${conv.user.id}"]`);
            if (item) {
                const preview = item.querySelector('p');
                const time = item.querySelector('.text-\\[9px\\]');
                if (preview) preview.textContent = escHtml(conv.last_message || 'Aucun message');
                if (time) time.textContent = conv.last_time || '';

                // Mise à jour badge non-lu depuis le serveur (évite cumul côté client)
                const existingBadge = item.querySelector('.conv-unread');
                if (conv.unread_count > 0 && currentUserId !== conv.user.id) {
                    if (existingBadge) {
                        existingBadge.textContent = conv.unread_count;
                    } else {
                        item.querySelector('.flex-1').insertAdjacentHTML('afterend',
                            `<span class="conv-unread flex-shrink-0 inline-flex items-center justify-center h-4 min-w-[16px] rounded-full bg-emerald-600 px-1 text-[9px] font-bold text-white">${conv.unread_count}</span>`
                        );
                    }
                } else if (existingBadge) {
                    existingBadge.remove();
                }
            } else {
                // New conversation - reload list
                loadConversations();
            }
        });
    })
    .catch(() => {});
}

function notifyNewMessage(userName) {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = 'toast-msg bg-white border border-gray-200 rounded-xl shadow-lg px-4 py-3 flex items-start gap-3 cursor-pointer hover:shadow-xl transition-shadow';
    toast.innerHTML = `
        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs flex-shrink-0">${escHtml(userName.charAt(0))}</div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-bold text-gray-900">${escHtml(userName)}</p>
            <p class="text-[11px] text-gray-600">Nouveau message reçu</p>
        </div>
        <button onclick="this.closest('.toast-msg').classList.add('hide');setTimeout(()=>this.closest('.toast-msg').remove(),300)" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    `;
    container.appendChild(toast);
    setTimeout(() => {
        if (toast.parentNode) { toast.classList.add('hide'); setTimeout(() => toast.remove(), 300); }
    }, 5000);
}

function handleNewMessage(msg) {
    // If we're viewing this conversation, add the message
    if (currentUserId === msg.sender_id) {
        appendMessage(msg, false);
        scrollToBottom();
        markAsRead(currentUserId);
    }

    // Show toast
    showToast(msg);

    // Update conversation list
    updateConversationFromSSE(msg);
}

function showToast(msg) {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = 'toast-msg bg-white border border-gray-200 rounded-xl shadow-lg px-4 py-3 flex items-start gap-3 cursor-pointer hover:shadow-xl transition-shadow';
    toast.innerHTML = `
        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs flex-shrink-0">${(msg.sender_name || '?').charAt(0).toUpperCase()}</div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-bold text-gray-900">${escHtml(msg.sender_name)}</p>
            <p class="text-[11px] text-gray-600 truncate">${escHtml(msg.message)}</p>
        </div>
        <button onclick="this.closest('.toast-msg').classList.add('hide');setTimeout(()=>this.closest('.toast-msg').remove(),300)" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    `;
    toast.addEventListener('click', function(e) {
        if (e.target.closest('button')) return;
        loadConversation(msg.sender_id, msg.sender_name, '');
        this.classList.add('hide');
        setTimeout(() => this.remove(), 300);
    });
    container.appendChild(toast);

    // Auto-hide after 5s
    setTimeout(() => {
        if (toast.parentNode) {
            toast.classList.add('hide');
            setTimeout(() => toast.remove(), 300);
        }
    }, 5000);
}

function updateConversationFromSSE(msg) {
    const items = document.querySelectorAll('.conv-item');
    let found = false;
    items.forEach(item => {
        if (parseInt(item.dataset.userId) === msg.sender_id) {
            found = true;
            const preview = item.querySelector('p');
            const time = item.querySelector('.text-\\[9px\\]');
            if (preview) preview.textContent = escHtml(msg.message);
            if (time) time.textContent = 'À l\'instant';

            // Increment unread if not the active conversation
            if (currentUserId !== msg.sender_id) {
                let badge = item.querySelector('.conv-unread');
                if (badge) {
                    const count = parseInt(badge.textContent) + 1;
                    badge.textContent = count;
                } else {
                    item.querySelector('.flex-1').insertAdjacentHTML('afterend',
                        `<span class="conv-unread flex-shrink-0 inline-flex items-center justify-center h-4 min-w-[16px] rounded-full bg-emerald-600 px-1 text-[9px] font-bold text-white">1</span>`
                    );
                }
            }
        }
    });

    // If not in list, reload conversations
    if (!found) {
        setTimeout(loadConversations, 500);
    }
}

// ===== Indicateur de saisie (typing) style WhatsApp =====
function showTypingBubble() {
    const container = document.getElementById('typingIndicatorContainer');
    if (container && currentUserId) {
        container.style.display = 'flex';
        const avatar = container.querySelector('span');
        if (avatar) avatar.textContent = document.getElementById('chatUserName')?.textContent?.charAt(0) || '?';
        clearTimeout(container._timeout);
        container._timeout = setTimeout(() => { container.style.display = 'none'; }, 4000);
    }
}

function hideTypingBubble() {
    const container = document.getElementById('typingIndicatorContainer');
    if (container) { container.style.display = 'none'; clearTimeout(container._timeout); }
}

function showTypingIndicators(typingUsers) {
    const indicator = document.getElementById('chatTypingIndicator');
    if (!currentUserId || !typingUsers.length) {
        indicator.classList.add('hidden');
        hideTypingBubble();
        return;
    }

    const typing = typingUsers.find(u => u.id === currentUserId);
    if (typing) {
        indicator.classList.remove('hidden');
        indicator.textContent = typing.name + ' écrit...';
        showTypingBubble();
        clearTimeout(indicator._timeout);
        indicator._timeout = setTimeout(() => {
            indicator.classList.add('hidden');
            hideTypingBubble();
        }, 3000);
    } else {
        indicator.classList.add('hidden');
        hideTypingBubble();
    }
}

// ===== Filter =====
function filterConversations(query) {
    document.querySelectorAll('.conv-item').forEach(item => {
        const name = (item.dataset.userName || '').toLowerCase();
        const email = (item.dataset.userEmail || '').toLowerCase();
        const q = query.toLowerCase().trim();
        item.style.display = (!q || name.includes(q) || email.includes(q)) ? 'flex' : 'none';
    });
}

// ===== Mark as read =====
function markAsRead(userId) {
    fetch(`/messages/${userId}/read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    }).catch(console.error);

    // Remove unread badge
    document.querySelectorAll('.conv-item').forEach(item => {
        if (parseInt(item.dataset.userId) === userId) {
            const badge = item.querySelector('.conv-unread');
            if (badge) badge.remove();
        }
    });
}

// ===== Typing indicator (sending) =====
function sendTyping(typing) {
    if (!currentUserId) return;
    fetch(`/messages/${currentUserId}/typing`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ typing }),
    }).catch(console.error);
}

// ===== Main init =====
document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('chatForm');
    loadConversations();
    startPolling(); // Polling temps réel au lieu de SSE

    if (chatForm) {
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(chatForm);
            sendTyping(false);
            clearTimeout(typingTimer);

            fetch(`/messages/${currentUserId}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData,
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    appendMessage(data.message, true);
                    chatForm.reset();
                    scrollToBottom();
                    updateConversationPreview(currentUserId, data.message.message);
                }
            })
            .catch(console.error);
        });

        const ta = chatForm.querySelector('textarea');
        ta.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 96) + 'px';

            sendTyping(true);
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => sendTyping(false), 2000);
        });
    }

    @if(isset($selectedUser) && $selectedUser)
    loadConversation({{ $selectedUser->id }}, '{{ $selectedUser->name }}', '{{ $selectedUser->email ?? '' }}');
    @endif
});

// ===== Conversations =====
function loadConversations() {
    const list = document.getElementById('conversationList');
    list.innerHTML = '<div class="text-center text-gray-400 text-xs mt-8">Chargement...</div>';

    fetch('/messages/conversations', {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    })
    .then(r => r.json())
    .then(conversations => {
        list.innerHTML = '';
        if (!conversations.length) {
            list.innerHTML = '<div class="text-center text-gray-400 text-xs mt-8">Aucune conversation</div>';
            return;
        }

        const bgColors = ['bg-emerald-100 text-emerald-700', 'bg-blue-100 text-blue-700', 'bg-amber-100 text-amber-700', 'bg-purple-100 text-purple-700', 'bg-pink-100 text-pink-700', 'bg-cyan-100 text-cyan-700'];

        conversations.forEach(conv => {
            const div = document.createElement('div');
            div.className = 'conv-item flex items-center gap-2.5 px-3 py-2.5 rounded-xl cursor-pointer transition-all mb-0.5 border border-transparent hover:bg-white hover:border-gray-100';
            div.dataset.userId = conv.user.id;
            div.dataset.userName = conv.user.name;
            div.dataset.userEmail = conv.user.email || '';

            const initial = (conv.user.name || 'U').charAt(0).toUpperCase();
            const bg = bgColors[conv.user.id % bgColors.length];

            div.innerHTML = `
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0 ${bg}">${initial}</div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-1.5">
                        <span class="text-xs font-bold text-gray-900 truncate">${escHtml(conv.user.name)}</span>
                        <span class="text-[9px] text-gray-400 flex-shrink-0">${conv.last_time || ''}</span>
                    </div>
                    <p class="text-[11px] text-gray-500 truncate mt-0.5">${escHtml(conv.last_message || 'Aucun message')}</p>
                </div>
                ${conv.unread_count > 0 ? `<span class="conv-unread flex-shrink-0 inline-flex items-center justify-center h-4 min-w-[16px] rounded-full bg-emerald-600 px-1 text-[9px] font-bold text-white">${conv.unread_count}</span>` : ''}
            `;

            div.addEventListener('click', function() {
                loadConversation(conv.user.id, conv.user.name, conv.user.email || '');
                document.querySelectorAll('.conv-item').forEach(i => i.classList.remove('!bg-white', '!border-emerald-500'));
                div.classList.add('!bg-white', '!border-emerald-500');
            });

            list.appendChild(div);
        });
    })
    .catch(() => {
        document.getElementById('conversationList').innerHTML = '<div class="text-center text-red-500 text-xs mt-8">Erreur</div>';
    });
}

function loadConversation(userId, userName, userEmail) {
    currentUserId = userId;
    document.getElementById('chatUserName').textContent = userName || 'Utilisateur';
    document.getElementById('chatUserEmail').textContent = userEmail || '';
    document.getElementById('chatAvatar').textContent = userName ? userName.charAt(0).toUpperCase() : '?';
    document.getElementById('chatTypingIndicator').classList.add('hidden');

    const chatMessages = document.getElementById('chatMessages');
    chatMessages.innerHTML = '<div class="text-center text-gray-400 text-xs mt-8">Chargement...</div>';

    fetch(`/messages/${userId}/list`, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    })
    .then(r => r.json())
    .then(messages => {
        chatMessages.innerHTML = '';
        if (!messages.length) {
            chatMessages.innerHTML = '<div class="text-center text-gray-400 text-xs mt-8">Aucun message. Envoyez le premier !</div>';
        } else {
            messages.forEach(msg => appendMessage(msg, msg.is_mine));
        }
        document.getElementById('chatInputArea').style.display = 'block';
        markAsRead(userId);
        scrollToBottom();
    })
    .catch(() => {
        chatMessages.innerHTML = '<div class="text-center text-red-500 text-xs mt-8">Erreur</div>';
    });
}

function appendMessage(msg, isMine) {
    const div = document.createElement('div');
    div.className = 'msg-row ' + (isMine ? 'justify-end' : 'justify-start');
    if (msg.id) div.dataset.msgId = msg.id;
    div.innerHTML = `
        <div class="max-w-[70%] px-3.5 py-2 rounded-[16px] text-xs leading-relaxed whitespace-pre-line ${isMine ? 'bg-emerald-600 text-white rounded-br-[4px] shadow-sm' : 'bg-white text-gray-800 border border-gray-200 rounded-bl-[4px] shadow-sm'}">
            <p>${escHtml(msg.message)}</p>
            <span class="text-[9px] mt-1 block ${isMine ? 'text-emerald-200 text-right' : 'text-gray-400 text-right'}">${msg.time || ''}</span>
        </div>
    `;
    document.getElementById('chatMessages').appendChild(div);
}

function updateConversationPreview(userId, message) {
    document.querySelectorAll('.conv-item').forEach(item => {
        if (parseInt(item.dataset.userId) === userId) {
            const preview = item.querySelector('p');
            const time = item.querySelector('.text-\\[9px\\]');
            if (preview) preview.textContent = escHtml(message);
            if (time) time.textContent = 'À l\'instant';
        }
    });
}

function scrollToBottom() {
    setTimeout(() => {
        document.getElementById('chatMessages').scrollTop = document.getElementById('chatMessages').scrollHeight;
    }, 50);
}

function escHtml(str) {
    if (!str) return '';
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}
</script>
@endpush