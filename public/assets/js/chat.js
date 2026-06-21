/**
 * TravelMate — Chat JS
 *
 * Handles:
 *  - Sending messages via AJAX (POST with CSRF)
 *  - Polling for new messages (GET every 3 seconds)
 *  - Auto-scrolling to latest message
 *  - Enter to send (Shift+Enter for newline)
 */

'use strict';

const chatMessages = document.getElementById('chatMessages');
const messageInput = document.getElementById('messageInput');
const sendBtn      = document.getElementById('sendBtn');
const csrfToken    = document.getElementById('csrfToken')?.value ?? '';
const sendUrl      = document.getElementById('sendUrl')?.value ?? '';
const pollUrl      = document.getElementById('pollUrl')?.value ?? '';

let lastId  = parseInt(chatMessages?.dataset.lastId ?? '0', 10);
const myId  = parseInt(chatMessages?.dataset.userId ?? '0', 10);
let polling = true;

/* ============================================================
   Scroll to bottom
   ============================================================ */
function scrollToBottom(smooth = true) {
    if (!chatMessages) return;
    chatMessages.scrollTo({
        top: chatMessages.scrollHeight,
        behavior: smooth ? 'smooth' : 'instant',
    });
}

/* ============================================================
   Render a single message bubble
   ============================================================ */
function renderMessage(msg) {
    const isOwn      = msg.user_id === myId;
    const bubbleSide = isOwn ? 'own' : 'other';
    const initial    = msg.full_name.charAt(0).toUpperCase();
    const time       = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    const avatarEl   = `<div class="tm-avatar-placeholder-sm flex-shrink-0" style="align-self:flex-end;">${initial}</div>`;
    const nameEl     = !isOwn ? `<div class="fw-semibold mb-1" style="font-size:0.75rem;color:var(--tm-gray-600);">${msg.full_name}</div>` : '';
    const textEl     = `<div class="tm-chat-text">${msg.message.replace(/\n/g, '<br>')}</div>`;
    const timeEl     = `<div class="text-muted mt-1" style="font-size:0.7rem;text-align:${isOwn ? 'right' : 'left'};">${time}</div>`;

    const div = document.createElement('div');
    div.className = `tm-chat-bubble ${bubbleSide}`;
    div.id = `msg-${msg.id}`;

    if (isOwn) {
        div.innerHTML = `
            <div>
                ${textEl}
                ${timeEl}
            </div>
            ${avatarEl}
        `;
    } else {
        div.innerHTML = `
            ${avatarEl}
            <div>
                ${nameEl}
                ${textEl}
                ${timeEl}
            </div>
        `;
    }

    return div;
}

/* ============================================================
   Remove "no messages" placeholder
   ============================================================ */
function removePlaceholder() {
    const placeholder = document.getElementById('noMsgPlaceholder');
    if (placeholder) placeholder.remove();
}

/* ============================================================
   Send message
   ============================================================ */
async function sendMessage() {
    const text = messageInput.value.trim();
    if (!text) return;

    sendBtn.disabled = true;

    try {
        const formData = new FormData();
        formData.append('message',     text);
        formData.append('csrf_token',  csrfToken);

        const res = await fetch(sendUrl, { method: 'POST', body: formData });
        const json = await res.json();

        if (json.success) {
            messageInput.value = '';
            messageInput.style.height = 'auto';
        } else {
            alert(json.message || 'Failed to send message.');
        }
    } catch (err) {
        console.error('Send error:', err);
        alert('Network error. Please try again.');
    } finally {
        sendBtn.disabled = false;
        messageInput.focus();
    }
}

/* ============================================================
   Poll for new messages
   ============================================================ */
async function pollMessages() {
    if (!polling) return;

    try {
        const res = await fetch(`${pollUrl}?after=${lastId}`);
        if (!res.ok) return;

        const messages = await res.json();

        if (messages.length > 0) {
            removePlaceholder();

            const atBottom = chatMessages.scrollHeight - chatMessages.clientHeight - chatMessages.scrollTop < 100;

            messages.forEach(msg => {
                if (!document.getElementById(`msg-${msg.id}`)) {
                    chatMessages.appendChild(renderMessage(msg));
                    lastId = Math.max(lastId, msg.id);
                }
            });

            if (atBottom) {
                scrollToBottom();
            }
        }
    } catch (err) {
        // Silent fail — reconnects on next interval
    }
}

/* ============================================================
   Event Listeners
   ============================================================ */
if (chatMessages) {
    // Initial scroll
    scrollToBottom(false);

    // Send on button click
    sendBtn?.addEventListener('click', sendMessage);

    // Enter to send, Shift+Enter for newline
    messageInput?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Auto-resize textarea
    messageInput?.addEventListener('input', () => {
        messageInput.style.height = 'auto';
        messageInput.style.height = Math.min(messageInput.scrollHeight, 120) + 'px';
    });

    // Poll every 3 seconds
    const pollInterval = setInterval(pollMessages, 3000);

    // Stop polling when page hides
    document.addEventListener('visibilitychange', () => {
        polling = !document.hidden;
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        clearInterval(pollInterval);
    });
}
