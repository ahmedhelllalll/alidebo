<style>
    /* Chatbot Styles */
    #chatbot-window {
        transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.3s;
        transform-origin: bottom left;
    }
    
    [dir="rtl"] #chatbot-window {
        transform-origin: bottom right;
    }

    #chatbot-window.hidden-state {
        opacity: 0;
        visibility: hidden;
        transform: scale(0.95) translateY(10px);
    }

    .chat-bubble-user {
        background: #f45018;
        color: white;
        border-bottom-right-radius: 4px;
    }
    
    [dir="rtl"] .chat-bubble-user {
        border-bottom-right-radius: 1rem;
        border-bottom-left-radius: 4px;
    }

    .chat-bubble-ai {
        background: #f1f5f9;
        color: #0f172a;
        border-bottom-left-radius: 4px;
    }
    
    .dark .chat-bubble-ai {
        background: #1e293b;
        color: #f8fafc;
    }

    [dir="rtl"] .chat-bubble-ai {
        border-bottom-left-radius: 1rem;
        border-bottom-right-radius: 4px;
    }

    .typing-dot {
        animation: chatTyping 1.4s infinite ease-in-out;
    }
    .typing-dot:nth-child(1) { animation-delay: -0.32s; }
    .typing-dot:nth-child(2) { animation-delay: -0.16s; }
    @keyframes chatTyping {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }

    /* Strict Mobile-Only Fullscreen Override */
    @media (max-width: 639px) {
        #chatbot-window:not(.hidden-state) {
            position: fixed !important;
            inset: 0 !important;
            width: 100% !important;
            height: 100dvh !important;
            max-width: none !important;
            max-height: none !important;
            margin: 0 !important;
            border-radius: 0 !important;
            border: none !important;
            z-index: 100000 !important;
        }
    }
</style>

<div id="dibo-chatbot-container" class="fixed bottom-6 left-6 rtl:left-auto rtl:right-6 z-[90000] flex flex-col items-start pointer-events-none" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    
    {{-- Chat Window --}}
    <div id="chatbot-window" class="hidden-state pointer-events-auto mb-4 w-[350px] sm:w-[380px] h-[500px] max-h-[calc(100vh-6rem)] max-w-[calc(100vw-3rem)] bg-white dark:bg-[#09090b] rounded-3xl shadow-[0_20px_50px_-12px_rgba(0,0,0,0.15)] dark:shadow-[0_20px_50px_-12px_rgba(0,0,0,0.5)] border border-slate-200/60 dark:border-zinc-800/60 overflow-hidden flex flex-col">
        {{-- Header --}}
        <div class="bg-primary px-5 py-4 flex items-center justify-between shadow-sm relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent pointer-events-none"></div>
            <div class="flex items-center gap-3 relative z-10">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-md shadow-inner ring-1 ring-white/30">
                    <img src="{{ asset('images/logo.webp') }}" alt="Dibo" class="w-6 h-6 object-contain filter drop-shadow-md">
                </div>
                <div>
                    <h3 class="text-white font-bold text-sm leading-tight">{{ __('chatbot.title') ?? 'Chat with Dibo' }}</h3>
                    <p class="text-white/80 text-xs mt-0.5 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        Online
                    </p>
                </div>
            </div>
            <button onclick="toggleChatbot()" class="relative z-10 p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition-colors" aria-label="Close chat">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Messages Area --}}
        <div id="chatbot-messages" data-lenis-prevent class="flex-1 p-5 overflow-y-auto scroll-smooth flex flex-col gap-4 bg-slate-50/50 dark:bg-zinc-900/30">
            {{-- Initial Greeting --}}
            <div class="flex flex-col gap-1 items-start rtl:items-start max-w-[90%]">
                <div class="chat-bubble-ai prose prose-sm dark:prose-invert prose-p:leading-relaxed prose-a:text-primary prose-a:no-underline hover:prose-a:underline prose-strong:text-slate-800 dark:prose-strong:text-slate-200 prose-ul:my-1 prose-li:my-0 px-5 py-3 rounded-3xl rounded-tl-sm text-[14px] shadow-md border border-slate-100 dark:border-zinc-800/60">
                    {{ __('chatbot.greeting') ?? 'Welcome to AliDebo! How can I help you today? 😊' }}
                </div>
            </div>
            
            {{-- Suggested Prompts --}}
            <div class="flex flex-col items-center w-full max-w-[280px] mx-auto gap-2.5 mt-4" id="suggested-prompts">
                <p class="text-[13px] font-medium text-slate-500 dark:text-zinc-400 w-full text-center mb-1">
                    {{ __('chatbot.suggest_title') ?? 'You can ask me about:' }}
                </p>
                <button onclick="sendChatMessage('{{ __('chatbot.suggest_1') ?? 'What is AliDebo?' }}')" class="w-full text-xs px-4 py-2.5 rounded-2xl border border-primary/20 bg-primary/5 text-primary hover:bg-primary hover:text-white transition-colors text-center font-medium shadow-sm">
                    {{ __('chatbot.suggest_1') ?? 'What is AliDebo?' }}
                </button>
                <button onclick="sendChatMessage('{{ __('chatbot.suggest_2') ?? 'How can I add my business?' }}')" class="w-full text-xs px-4 py-2.5 rounded-2xl border border-primary/20 bg-primary/5 text-primary hover:bg-primary hover:text-white transition-colors text-center font-medium shadow-sm">
                    {{ __('chatbot.suggest_2') ?? 'How can I add my business?' }}
                </button>
                <button onclick="sendChatMessage('{{ __('chatbot.suggest_3') ?? 'Are there any subscription fees?' }}')" class="w-full text-xs px-4 py-2.5 rounded-2xl border border-primary/20 bg-primary/5 text-primary hover:bg-primary hover:text-white transition-colors text-center font-medium shadow-sm">
                    {{ __('chatbot.suggest_3') ?? 'Are there any subscription fees?' }}
                </button>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="p-4 bg-white dark:bg-[#09090b] border-t border-slate-100 dark:border-zinc-800/80">
            <form id="chatbot-form" onsubmit="handleChatSubmit(event)" class="relative flex items-end gap-2">
                @csrf
                <textarea 
                    id="chatbot-input" 
                    rows="1"
                    data-lenis-prevent
                    placeholder="{{ __('chatbot.placeholder') ?? 'Type your message...' }}" 
                    class="w-full bg-slate-100 dark:bg-zinc-800/50 border-transparent focus:border-primary/30 focus:ring-0 rounded-2xl px-4 py-3 text-sm text-slate-700 dark:text-zinc-200 resize-none max-h-[100px] scrollbar-hide"
                    onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); handleChatSubmit(event); }"
                    oninput="this.style.height = ''; this.style.height = Math.min(this.scrollHeight, 100) + 'px';"
                ></textarea>
                <button type="submit" id="chatbot-submit" class="p-3 bg-primary text-white rounded-xl hover:bg-primary-light hover:shadow-lg hover:shadow-primary/20 transition-all shrink-0 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </form>
        </div>
    </div>

    {{-- Floating Toggle Button --}}
    <button id="chatbot-toggle" onclick="toggleChatbot()" class="pointer-events-auto relative group w-14 h-14 bg-primary text-white rounded-full flex items-center justify-center shadow-lg shadow-primary/30 hover:shadow-xl hover:shadow-primary/40 hover:-translate-y-1 transition-all duration-300 z-50">
        <svg id="chatbot-icon-open" class="w-6 h-6 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
        <svg id="chatbot-icon-close" class="w-6 h-6 absolute inset-0 m-auto opacity-0 scale-50 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        
        {{-- Notification Badge --}}
        <span class="absolute top-0 right-0 flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500 border-2 border-white dark:border-[#09090b]"></span>
        </span>
    </button>
</div>

<!-- Markdown Parser & HTML Sanitizer -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.0.6/purify.min.js"></script>

<script>
    let isChatOpen = false;
    let isWaitingForReply = false;
    let conversationHistory = [];
    const chatbotWindow = document.getElementById('chatbot-window');
    const messagesContainer = document.getElementById('chatbot-messages');
    const chatInput = document.getElementById('chatbot-input');
    const chatSubmit = document.getElementById('chatbot-submit');
    const suggestedPrompts = document.getElementById('suggested-prompts');

    // DOMPurify hook to force links to open in a new tab securely
    if (typeof DOMPurify !== 'undefined') {
        DOMPurify.addHook('afterSanitizeAttributes', function(node) {
            if ('target' in node) {
                node.setAttribute('target', '_blank');
                node.setAttribute('rel', 'noopener noreferrer');
            }
        });
    }

    // Close chat on click outside
    document.addEventListener('click', function(event) {
        if (isChatOpen) {
            const toggle = document.getElementById('chatbot-toggle');
            if (chatbotWindow && !chatbotWindow.contains(event.target) && toggle && !toggle.contains(event.target)) {
                toggleChatbot(false);
            }
        }
    });

    // Load state from sessionStorage
    window.addEventListener('DOMContentLoaded', () => {
        const savedHistory = sessionStorage.getItem('chatbotHistory');
        if (savedHistory) {
            try {
                const history = JSON.parse(savedHistory);
                if (Array.isArray(history) && history.length > 0) {
                    conversationHistory = history;
                    
                    // Hide initial greeting and prompts if we have history
                    const initialGreeting = messagesContainer.querySelector('.chat-bubble-ai');
                    if (initialGreeting && initialGreeting.parentNode) {
                        initialGreeting.parentNode.style.display = 'none';
                    }
                    if (suggestedPrompts) suggestedPrompts.style.display = 'none';
                    
                    history.forEach(msg => {
                        appendMessage(msg.content, msg.role === 'user', true);
                    });
                }
            } catch (e) {
                console.error("Failed to parse chat history");
            }
        }
        
        const savedState = sessionStorage.getItem('isChatOpen');
        if (savedState === 'true') {
            toggleChatbot(true); // pass true to force open without toggling state
        }
    });

    function toggleChatbot(forceOpen = null) {
        isChatOpen = forceOpen !== null ? forceOpen : !isChatOpen;
        
        const iconOpen = document.getElementById('chatbot-icon-open');
        const iconClose = document.getElementById('chatbot-icon-close');
        const container = document.getElementById('dibo-chatbot-container');
        
        if (isChatOpen) {
            chatbotWindow.classList.remove('hidden-state');
            iconOpen.classList.add('opacity-0', 'scale-50');
            iconClose.classList.remove('opacity-0', 'scale-50');
            if(container) {
                container.classList.remove('z-[90000]');
                container.classList.add('z-[100000]');
            }
            
            // Disable body scroll on mobile
            if (window.innerWidth < 640) {
                document.body.classList.add('overflow-hidden');
            }
            
            setTimeout(() => chatInput.focus(), 300);
        } else {
            chatbotWindow.classList.add('hidden-state');
            iconOpen.classList.remove('opacity-0', 'scale-50');
            iconClose.classList.add('opacity-0', 'scale-50');
            if(container) {
                container.classList.remove('z-[100000]');
                container.classList.add('z-[90000]');
            }
            
            // Re-enable body scroll
            document.body.classList.remove('overflow-hidden');
        }
        
        sessionStorage.setItem('isChatOpen', isChatOpen);
    }

    // Handle resize events to prevent being stuck with hidden overflow
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 640) {
            document.body.classList.remove('overflow-hidden');
        } else if (isChatOpen) {
            document.body.classList.add('overflow-hidden');
        }
    });

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function appendMessage(text, isUser = false, isRestore = false) {
        if (suggestedPrompts && !isRestore) {
            suggestedPrompts.style.display = 'none';
        }

        const msgDiv = document.createElement('div');
        msgDiv.className = `flex flex-col gap-1 ${isUser ? 'max-w-[85%] self-end items-end' : 'max-w-[90%] self-start items-start'}`;
        
        const bubble = document.createElement('div');
        if (isUser) {
            bubble.className = `px-5 py-3 rounded-3xl text-[14px] leading-relaxed shadow-md chat-bubble-user rounded-tr-sm`;
            bubble.innerHTML = escapeHtml(text).replace(/\n/g, '<br>');
        } else {
            bubble.className = `chat-bubble-ai prose prose-sm dark:prose-invert prose-p:leading-relaxed prose-a:text-primary prose-a:no-underline hover:prose-a:underline prose-strong:text-slate-800 dark:prose-strong:text-slate-200 prose-ul:my-1 prose-li:my-0 px-5 py-3 rounded-3xl rounded-tl-sm text-[14px] shadow-md border border-slate-100 dark:border-zinc-800/60`;
            if (typeof marked !== 'undefined' && typeof DOMPurify !== 'undefined') {
                const rawHtml = marked.parse(text);
                bubble.innerHTML = DOMPurify.sanitize(rawHtml);
            } else {
                bubble.innerHTML = escapeHtml(text).replace(/\n/g, '<br>');
            }
        }
        
        msgDiv.appendChild(bubble);
        messagesContainer.appendChild(msgDiv);
        
        scrollToBottom();
    }

    function appendTypingIndicator() {
        const msgDiv = document.createElement('div');
        msgDiv.id = 'typing-indicator';
        msgDiv.className = `flex flex-col gap-1 max-w-[85%] self-start items-start`;
        
        const bubble = document.createElement('div');
        bubble.className = `px-4 py-3.5 rounded-2xl rounded-tl-sm chat-bubble-ai shadow-sm flex items-center gap-1.5`;
        bubble.innerHTML = `
            <div class="w-1.5 h-1.5 bg-slate-400 dark:bg-zinc-500 rounded-full typing-dot"></div>
            <div class="w-1.5 h-1.5 bg-slate-400 dark:bg-zinc-500 rounded-full typing-dot"></div>
            <div class="w-1.5 h-1.5 bg-slate-400 dark:bg-zinc-500 rounded-full typing-dot"></div>
        `;
        
        msgDiv.appendChild(bubble);
        messagesContainer.appendChild(msgDiv);
        scrollToBottom();
    }

    function removeTypingIndicator() {
        const indicator = document.getElementById('typing-indicator');
        if (indicator) {
            indicator.remove();
        }
    }

    function scrollToBottom() {
        messagesContainer.scrollTo({
            top: messagesContainer.scrollHeight,
            behavior: 'smooth'
        });
    }

    async function sendChatMessage(overrideMsg = null) {
        if (isWaitingForReply) return;
        
        const text = overrideMsg || chatInput.value.trim();
        if (!text) return;

        // Reset input
        chatInput.value = '';
        chatInput.style.height = '';
        chatSubmit.disabled = true;
        chatInput.disabled = true;
        isWaitingForReply = true;

        // Show user message
        appendMessage(text, true);
        
        // Push to history
        conversationHistory.push({ role: 'user', content: text });
        sessionStorage.setItem('chatbotHistory', JSON.stringify(conversationHistory));

        // Show typing indicator
        setTimeout(() => appendTypingIndicator(), 300);

        try {
            const tokenInput = document.querySelector('#chatbot-form input[name="_token"]');
            const token = tokenInput ? tokenInput.value : '';
            const response = await fetch("{{ route('chatbot.ask') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ messages: conversationHistory })
            });

            if (response.status === 419) {
                removeTypingIndicator();
                appendMessage("{{ __('chatbot.error') ?? 'Your session has expired. Please refresh the page.' }}", false);
                return;
            }

            const data = await response.json();
            
            removeTypingIndicator();
            
            if (data.success && data.reply) {
                appendMessage(data.reply, false);
                conversationHistory.push({ role: 'assistant', content: data.reply });
                sessionStorage.setItem('chatbotHistory', JSON.stringify(conversationHistory));
            } else {
                appendMessage("{{ __('chatbot.error') ?? 'Sorry, an error occurred.' }}", false);
            }
        } catch (error) {
            console.error('Chat error:', error);
            removeTypingIndicator();
            appendMessage("{{ __('chatbot.error') ?? 'Sorry, an error occurred.' }}", false);
        } finally {
            chatSubmit.disabled = false;
            chatInput.disabled = false;
            isWaitingForReply = false;
            chatInput.focus();
        }
    }

    function handleChatSubmit(e) {
        e.preventDefault();
        sendChatMessage();
    }
</script>
