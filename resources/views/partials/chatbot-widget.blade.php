<!-- Chatbot Floating Widget -->
<div id="chatbot-container" class="fixed bottom-6 right-6 z-50 font-sans">
    <!-- Toggle Button -->
    <button id="chatbot-toggle" class="flex items-center justify-center w-14 h-14 bg-emerald-500 hover:bg-emerald-600 text-white rounded-full shadow-2xl transition-all duration-300 transform hover:scale-110 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7" id="icon-chat">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
        </svg>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 hidden" id="icon-close">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <!-- Chat Box -->
    <div id="chatbot-box" class="hidden absolute bottom-20 right-0 w-80 sm:w-96 h-[550px] bg-white dark:bg-slate-800 rounded-2xl shadow-2xl flex flex-col overflow-hidden border border-slate-100 dark:border-slate-700 transition-all duration-300 transform scale-95 opacity-0 origin-bottom-right">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 p-3 text-white flex items-center justify-between shadow-sm z-10 relative">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center font-bold text-lg border-2 border-emerald-300">
                        🤖
                    </div>
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-emerald-600 rounded-full"></span>
                </div>
                <div>
                    <h3 class="font-bold text-sm leading-none flex items-center gap-1">KikiBot — Asisten Toko</h3>
                    <span class="text-xs text-emerald-100 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-green-400"></span> Online • Powered by AI</span>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <!-- Clear Button -->
                <button id="chatbot-clear-btn" title="Hapus Riwayat" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 hover:bg-white/20 transition-colors focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </button>
                <!-- Close Button -->
                <button id="chatbot-close" title="Tutup" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 hover:bg-white/20 transition-colors focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Disclaimer Banner -->
        <div class="bg-amber-100 dark:bg-amber-900/40 border-b border-amber-200 dark:border-amber-700/50 p-2.5 px-3 flex items-start gap-2 text-xs text-amber-800 dark:text-amber-200 relative shadow-sm z-0 transition-colors">
            <span class="text-amber-500 dark:text-amber-400 shrink-0">⚠️</span>
            <p class="leading-relaxed"><strong>Disclaimer:</strong> Jawaban AI mungkin tidak selalu 100% akurat. Untuk keluhan pesanan, hubungi Admin.</p>
        </div>

        <!-- Message Body -->
        <div id="chatbot-messages" class="flex-1 p-4 overflow-y-auto space-y-4 bg-slate-50/50 dark:bg-slate-900/50 scrollbar-thin relative pb-16 transition-colors">
            <!-- Greeting Message -->
            <div class="flex items-start gap-2.5">
                <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/50 rounded-full flex items-center justify-center text-sm shadow-sm shrink-0 border border-emerald-200 dark:border-emerald-700/50 transition-colors">
                    🤖
                </div>
                <div class="flex flex-col w-full max-w-[270px] leading-relaxed p-3.5 bg-white dark:bg-slate-700 rounded-r-xl rounded-bl-xl shadow-sm border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 transition-colors">
                    <p class="text-sm font-normal">
                        Halo <strong>{{ Auth::check() ? Auth::user()->name : 'Pengunjung' }}</strong>! 👋<br><br>
                        Silakan tanyakan tentang rekomendasi produk, stok barang, atau cara checkout pesanan Anda. Saya siap membantu! 💚
                    </p>
                    <span class="text-[10px] font-normal text-slate-400 dark:text-slate-400 text-right mt-1.5">Sekarang</span>
                </div>
            </div>
        </div>

        <!-- Dynamic Quick Prompts (from View Composer) -->
        <div id="quick-prompts" class="absolute bottom-[65px] left-0 right-0 px-4 py-2 bg-gradient-to-t from-slate-50 dark:from-slate-800 via-slate-50/90 dark:via-slate-800/90 to-transparent flex flex-nowrap overflow-x-auto whitespace-nowrap gap-2 pointer-events-auto transition-colors scrollbar-thin">
            @if(isset($globalChatbotPrompts) && $globalChatbotPrompts->count() > 0)
                @foreach($globalChatbotPrompts as $prompt)
                    <button class="quick-prompt px-3 py-1.5 bg-white dark:bg-slate-700 border border-emerald-500 dark:border-emerald-600 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-slate-600 rounded-full text-xs font-semibold transition-colors flex items-center gap-1 shadow-sm" data-prompt="{{ $prompt->prompt_text }}">
                        {{ $prompt->title }}
                    </button>
                @endforeach
            @else
                <!-- Fallback defaults if DB is empty -->
                <button class="quick-prompt px-3 py-1.5 bg-white dark:bg-slate-700 border border-emerald-500 dark:border-emerald-600 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-slate-600 rounded-full text-xs font-semibold transition-colors flex items-center gap-1 shadow-sm" data-prompt="Berikan saya rekomendasi baju terbaik.">
                    👕 Rekomendasi Baju
                </button>
                <button class="quick-prompt px-3 py-1.5 bg-white dark:bg-slate-700 border border-emerald-500 dark:border-emerald-600 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-slate-600 rounded-full text-xs font-semibold transition-colors flex items-center gap-1 shadow-sm" data-prompt="Berapa ongkos kirim standar ke daerah saya?">
                    📦 Cek Ongkos Kirim
                </button>
            @endif
        </div>

        <!-- Loading Indicator -->
        <div id="chatbot-loading" class="hidden px-4 py-2 flex items-center gap-2 bg-slate-50/50 dark:bg-slate-900/50 transition-colors">
            <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/50 rounded-full flex items-center justify-center text-sm border border-emerald-200 dark:border-emerald-700/50 transition-colors">
                🤖
            </div>
            <div class="bg-white dark:bg-slate-700 px-3 py-2 rounded-xl shadow-sm border border-slate-200 dark:border-slate-600 flex items-center gap-1 transition-colors">
                <span class="w-1.5 h-1.5 bg-emerald-500 dark:bg-emerald-400 rounded-full animate-bounce"></span>
                <span class="w-1.5 h-1.5 bg-emerald-500 dark:bg-emerald-400 rounded-full animate-bounce [animation-delay:0.2s]"></span>
                <span class="w-1.5 h-1.5 bg-emerald-500 dark:bg-emerald-400 rounded-full animate-bounce [animation-delay:0.4s]"></span>
            </div>
        </div>

        <!-- Input Area -->
        <form id="chatbot-form" class="p-3 border-t border-slate-200 dark:border-slate-700 flex gap-2 items-center bg-white dark:bg-slate-800 z-10 transition-colors">
            <input type="text" id="chatbot-input" placeholder="Tanya KikiBot..." autocomplete="off" class="flex-1 px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-full focus:outline-none focus:border-emerald-500 dark:focus:border-emerald-400 text-slate-700 dark:text-slate-200 bg-slate-50 dark:bg-slate-700 focus:bg-white dark:focus:bg-slate-600 transition-all shadow-inner placeholder-slate-400 dark:placeholder-slate-500">
            <button type="submit" class="w-11 h-11 shrink-0 flex items-center justify-center bg-emerald-500 text-white rounded-full hover:bg-emerald-600 transition-colors focus:outline-none shadow-md transform hover:scale-105 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 ml-0.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                </svg>
            </button>
        </form>

        <!-- Clear Chat Overlay Modal -->
        <div id="clear-modal" class="hidden absolute inset-0 bg-slate-900/40 dark:bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center opacity-0 transition-opacity duration-300">
            <div class="bg-white dark:bg-slate-800 rounded-2xl w-64 p-5 text-center shadow-2xl transform scale-95 transition-transform duration-300 border border-slate-100 dark:border-slate-700" id="clear-modal-content">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 text-red-500 dark:text-red-400 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </div>
                <h4 class="font-bold text-slate-800 dark:text-slate-100 text-sm mb-2">Hapus Riwayat Chat?</h4>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 mb-5 leading-relaxed">Riwayat chat akan dihapus permanen dari server dan tidak dapat dikembalikan.</p>
                <div class="flex gap-2">
                    <button id="btn-confirm-clear" class="flex-1 py-2 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-bold transition-colors">Hapus</button>
                    <button id="btn-cancel-clear" class="flex-1 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-bold transition-colors">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('chatbot-toggle');
    const closeBtn = document.getElementById('chatbot-close');
    const chatbotBox = document.getElementById('chatbot-box');
    const iconChat = document.getElementById('icon-chat');
    const iconClose = document.getElementById('icon-close');
    const chatbotForm = document.getElementById('chatbot-form');
    const chatbotInput = document.getElementById('chatbot-input');
    const messagesContainer = document.getElementById('chatbot-messages');
    const loadingIndicator = document.getElementById('chatbot-loading');
    const quickPromptsDiv = document.getElementById('quick-prompts');
    
    const clearBtn = document.getElementById('chatbot-clear-btn');
    const clearModal = document.getElementById('clear-modal');
    const clearModalContent = document.getElementById('clear-modal-content');
    const btnConfirmClear = document.getElementById('btn-confirm-clear');
    const btnCancelClear = document.getElementById('btn-cancel-clear');

    let isOpen = false;

    toggleBtn.addEventListener('click', toggleChatbot);
    closeBtn.addEventListener('click', toggleChatbot);

    function toggleChatbot() {
        isOpen = !isOpen;
        if (isOpen) {
            chatbotBox.classList.remove('hidden');
            setTimeout(() => {
                chatbotBox.classList.remove('scale-95', 'opacity-0');
                chatbotBox.classList.add('scale-100', 'opacity-100');
            }, 10);
            iconChat.classList.add('hidden');
            iconClose.classList.remove('hidden');
            chatbotInput.focus();
            scrollToBottom();
        } else {
            chatbotBox.classList.remove('scale-100', 'opacity-100');
            chatbotBox.classList.add('scale-95', 'opacity-0');
            iconChat.classList.remove('hidden');
            iconClose.classList.add('hidden');
            setTimeout(() => {
                chatbotBox.classList.add('hidden');
            }, 300);
        }
    }

    // Dynamic Quick Prompts Logic
    document.querySelectorAll('.quick-prompt').forEach(btn => {
        btn.addEventListener('click', function() {
            const text = this.getAttribute('data-prompt');
            chatbotInput.value = text;
            chatbotForm.dispatchEvent(new Event('submit'));
        });
    });

    clearBtn.addEventListener('click', () => {
        clearModal.classList.remove('hidden');
        setTimeout(() => {
            clearModal.classList.remove('opacity-0');
            clearModalContent.classList.remove('scale-95');
        }, 10);
    });

    btnCancelClear.addEventListener('click', closeClearModal);
    
    function closeClearModal() {
        clearModal.classList.add('opacity-0');
        clearModalContent.classList.add('scale-95');
        setTimeout(() => {
            clearModal.classList.add('hidden');
        }, 300);
    }

    btnConfirmClear.addEventListener('click', async () => {
        try {
            await fetch('/api/chatbot/clear', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            while (messagesContainer.children.length > 1) {
                messagesContainer.removeChild(messagesContainer.lastChild);
            }
            if(quickPromptsDiv) quickPromptsDiv.classList.remove('hidden');
            closeClearModal();
        } catch (e) {
            console.error("Failed to clear chat", e);
            closeClearModal();
        }
    });

    chatbotForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const text = chatbotInput.value.trim();
        if (!text) return;

        if(quickPromptsDiv) quickPromptsDiv.classList.add('hidden');
        chatbotInput.value = '';

        appendMessage('user', text);
        scrollToBottom();

        loadingIndicator.classList.remove('hidden');
        scrollToBottom();

        try {
            const response = await fetch('/api/chatbot/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ message: text })
            });

            loadingIndicator.classList.add('hidden');

            if (!response.ok) {
                appendMessage('assistant', 'Maaf, saya sedang mengalami kendala. Silakan coba kembali nanti.');
                return;
            }

            const reader = response.body.getReader();
            const decoder = new TextDecoder('utf-8');
            let assistantMessage = '';
            
            const messageEl = appendMessage('assistant', '');
            const pElement = messageEl.querySelector('p.chat-content');

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                const chunk = decoder.decode(value, { stream: true });
                const lines = chunk.split('\n\n');

                for (const line of lines) {
                    if (line.startsWith('data: ')) {
                        const dataStr = line.substring(6);
                        if (dataStr === '[DONE]') continue;
                        
                        try {
                            const data = JSON.parse(dataStr);
                            if (data.chunk) {
                                assistantMessage += data.chunk;
                                pElement.innerHTML = formatText(assistantMessage);
                                scrollToBottom();
                            }
                        } catch (e) {
                            console.error("Error parsing stream chunk", e);
                        }
                    }
                }
            }
        } catch (err) {
            console.error(err);
            loadingIndicator.classList.add('hidden');
            appendMessage('assistant', 'Koneksi terputus. Silakan cek koneksi internet Anda.');
        }

        scrollToBottom();
    });

    window.scoreAIResponse = async function(btn, score) {
        const container = btn.closest('.scoring-container');
        container.querySelectorAll('button').forEach(b => b.disabled = true);
        
        btn.classList.add(score > 0 ? 'text-emerald-600' : 'text-red-500');
        btn.classList.add(score > 0 ? 'bg-emerald-100' : 'bg-red-100');
        btn.classList.add('dark:bg-opacity-20');
        
        try {
            await fetch('/api/chatbot/score', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ score: score })
            });
        } catch(e) {
            console.error("Score failed to send", e);
        }
    };

    function appendMessage(sender, text) {
        const timestamp = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const bubble = document.createElement('div');
        
        if (sender === 'user') {
            bubble.className = "flex items-start justify-end gap-2.5";
            bubble.innerHTML = `
                <div class="flex flex-col w-full max-w-[270px] leading-relaxed p-3.5 bg-emerald-500 rounded-l-xl rounded-br-xl shadow-sm text-white">
                    <p class="text-sm font-normal chat-content">${formatText(text)}</p>
                    <span class="text-[10px] font-normal text-emerald-100 text-right mt-1.5">${timestamp}</span>
                </div>
                <div class="w-8 h-8 bg-emerald-600 rounded-full flex items-center justify-center text-sm font-bold text-white shadow-sm shrink-0">
                    👤
                </div>
            `;
        } else {
            bubble.className = "flex items-start gap-2.5";
            bubble.innerHTML = `
                <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/50 rounded-full flex items-center justify-center text-sm shadow-sm shrink-0 border border-emerald-200 dark:border-emerald-700/50 transition-colors">
                    🤖
                </div>
                <div class="flex flex-col w-full max-w-[270px]">
                    <div class="leading-relaxed p-3.5 bg-white dark:bg-slate-700 rounded-r-xl rounded-bl-xl shadow-sm border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 transition-colors">
                        <p class="text-sm font-normal chat-content">${formatText(text)}</p>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                        <div class="flex gap-1 scoring-container">
                            <button onclick="scoreAIResponse(this, 0.988796)" class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-emerald-500 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-slate-700 rounded-lg transition-colors focus:outline-none" title="Respons Baik">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9v2.25h3.382a.75.75 0 01.698 1.018l-2.614 7.218A1.75 1.75 0 0114.07 21H7.5a1.75 1.75 0 01-1.75-1.75v-9A1.75 1.75 0 017.5 8.5h6.75V3.75a1.5 1.5 0 013 0v5.25z" /></svg>
                            </button>
                            <button onclick="scoreAIResponse(this, -0.34551)" class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-slate-700 rounded-lg transition-colors focus:outline-none" title="Respons Buruk">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 15v-2.25H6.368a.75.75 0 01-.698-1.018l2.614-7.218A1.75 1.75 0 019.93 3h6.57a1.75 1.75 0 011.75 1.75v9a1.75 1.75 0 01-1.75 1.75H9.75v4.5a1.5 1.5 0 01-3 0V15z" /></svg>
                            </button>
                        </div>
                        <span class="text-[10px] font-normal text-slate-400 dark:text-slate-500">${timestamp}</span>
                    </div>
                </div>
            `;
        }
        
        messagesContainer.appendChild(bubble);
        return bubble;
    }

    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function formatText(raw) {
        return raw
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/\n/g, '<br>');
    }
});
</script>
