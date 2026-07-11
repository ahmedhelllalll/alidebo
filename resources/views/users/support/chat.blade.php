@extends('users.layout')

@section('title', __('dashboard.index.help_center') ?? 'Help Center')
@section('page_title', __('dashboard.index.help_center') ?? 'Help Center')

@section('content')
<div class="max-w-4xl mx-auto h-[75vh] flex flex-col glass-panel rounded-2xl border border-black/5 dark:border-white/[0.04] shadow-lg overflow-hidden" x-data="supportChat()">
    
    {{-- Chat Header --}}
    <div class="px-6 py-4 border-b border-black/5 dark:border-white/[0.04] bg-white/50 dark:bg-zinc-900/50 shrink-0 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="relative">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-primary to-orange-400 p-[2px] shadow-sm">
                    <div class="w-full h-full bg-white dark:bg-zinc-900 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-headset text-primary text-sm"></i>
                    </div>
                </div>
                <div class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 rounded-full border-2 border-white dark:border-zinc-900 shadow-sm"></div>
            </div>
            <div>
                <h2 class="text-[15px] font-bold text-zinc-900 dark:text-white leading-tight">{{ __('dashboard.index.priority_support') ?? 'Priority Support' }}</h2>
                <p class="text-[11px] font-medium text-emerald-600 dark:text-emerald-400">{{ __('dashboard.index.elite_team') ?? 'Elite Team' }} - Online</p>
            </div>
        </div>
    </div>

    {{-- Chat Messages Area --}}
    <div class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar" id="chat-messages-container" x-ref="messagesContainer">
        
        <template x-if="loading">
            <div class="flex justify-center py-4">
                <i class="fa-solid fa-circle-notch fa-spin text-primary text-2xl"></i>
            </div>
        </template>
        
        <template x-if="!loading && messages.length === 0">
            <div class="text-center py-10">
                <div class="w-16 h-16 mx-auto rounded-full bg-primary/10 flex items-center justify-center mb-4">
                    <i class="fa-regular fa-comments text-primary text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white">{{ __('dashboard.index.support_growth') ?? 'Support & Growth' }}</h3>
                <p class="text-sm text-zinc-500 mt-2">{{ __('dashboard.index.type_message') ?? 'Type your message to start the conversation.' }}</p>
            </div>
        </template>

        <template x-for="(msg, index) in messages" :key="msg.id">
            <div :class="['flex w-full mt-2', msg.sender_type === 'user' ? 'justify-end' : 'justify-start']" 
                 style="animation: msgSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; transform: translateY(12px);"
                 :style="`animation-delay: ${index * 0.03}s`">
                <div :class="[
                    'max-w-[75%] rounded-2xl px-5 py-3 text-[14px] shadow-sm', 
                    msg.sender_type === 'user' 
                        ? 'bg-primary text-white rounded-br-sm' 
                        : 'bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-black/5 dark:border-white/[0.04] rounded-bl-sm'
                ]">
                    <p x-text="msg.message" class="whitespace-pre-wrap leading-relaxed"></p>
                    <span :class="[
                        'text-[10px] mt-1.5 block',
                        msg.sender_type === 'user' ? 'text-white/70 text-right' : 'text-zinc-400 text-left'
                    ]" x-text="msg.time_ago"></span>
                </div>
            </div>
        </template>

        <template x-if="isAdminTyping">
            <div class="flex w-full justify-start mt-2" style="animation: msgSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; transform: translateY(12px);">
                <div class="max-w-[75%] rounded-2xl px-5 py-4 shadow-sm bg-white dark:bg-zinc-800 border border-black/5 dark:border-white/[0.04] rounded-bl-sm flex items-center gap-1.5">
                    <div class="w-1.5 h-1.5 bg-zinc-400 dark:bg-zinc-500 rounded-full" style="animation: typingWave 1.4s infinite ease-in-out; animation-delay: 0ms;"></div>
                    <div class="w-1.5 h-1.5 bg-zinc-400 dark:bg-zinc-500 rounded-full" style="animation: typingWave 1.4s infinite ease-in-out; animation-delay: 160ms;"></div>
                    <div class="w-1.5 h-1.5 bg-zinc-400 dark:bg-zinc-500 rounded-full" style="animation: typingWave 1.4s infinite ease-in-out; animation-delay: 320ms;"></div>
                </div>
            </div>
        </template>

        <style>
            @keyframes msgSlideUp {
                0% { opacity: 0; transform: translateY(12px) scale(0.98); }
                100% { opacity: 1; transform: translateY(0) scale(1); }
            }
            @keyframes typingWave {
                0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
                30% { transform: translateY(-4px); opacity: 1; }
            }
        </style>

    </div>

    {{-- Chat Input Area --}}
    <div class="p-4 bg-white dark:bg-zinc-900 border-t border-black/5 dark:border-white/[0.04] shrink-0">
        <form @submit.prevent="sendMessage" class="flex items-end gap-3 relative">
            <div class="flex-1 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl border border-zinc-200 dark:border-zinc-700/50 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 transition-all overflow-hidden relative">
                <textarea 
                    x-model="newMessage"
                    @keydown.enter.prevent="if(!event.shiftKey) sendMessage()"
                    @input="sendTyping()"
                    rows="1"
                    class="w-full bg-transparent border-none focus:ring-0 resize-none py-3.5 px-4 text-sm text-zinc-800 dark:text-zinc-200 placeholder:text-zinc-400 max-h-32 custom-scrollbar"
                    placeholder="{{ __('dashboard.index.type_message') ?? 'Type your message...' }}"
                    style="min-height: 48px;"
                    oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                ></textarea>
            </div>
            <button 
                type="submit" 
                :disabled="isSending || newMessage.trim() === ''"
                class="w-12 h-12 rounded-xl bg-primary text-white flex items-center justify-center shrink-0 hover:bg-primary-dark transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-md shadow-primary/20"
            >
                <i x-show="!isSending" class="fa-solid fa-paper-plane rtl:-scale-x-100"></i>
                <i x-show="isSending" class="fa-solid fa-circle-notch fa-spin"></i>
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('supportChat', () => ({
            messages: [],
            newMessage: '',
            loading: true,
            isSending: false,
            pollInterval: null,
            isAdminTyping: false,
            typingTimer: null,

            init() {
                this.fetchMessages();
                this.pollInterval = setInterval(() => {
                    this.fetchMessages(false);
                }, 5000); // Poll every 5 seconds
            },

            destroy() {
                if (this.pollInterval) clearInterval(this.pollInterval);
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    setTimeout(() => {
                        const container = this.$refs.messagesContainer;
                        if(container) {
                            container.scrollTo({
                                top: container.scrollHeight,
                                behavior: 'smooth'
                            });
                        }
                    }, 50);
                });
            },

            fetchMessages(showLoading = true) {
                if(showLoading) this.loading = true;
                const url = '{{ route("support.chat.fetch") }}?_t=' + new Date().getTime();
                fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    cache: 'no-store'
                })
                .then(res => res.json())
                .then(data => {
                    const oldLength = this.messages.length;
                    this.messages = data.messages;
                    this.loading = false;
                    
                    const typingChanged = this.isAdminTyping !== data.is_admin_typing;
                    this.isAdminTyping = data.is_admin_typing;
                    
                    if (this.messages.length > oldLength || typingChanged) {
                        this.scrollToBottom();
                    }
                })
                .catch(err => {
                    console.error('Error fetching messages', err);
                    this.loading = false;
                });
            },

            sendMessage() {
                if (this.newMessage.trim() === '') return;

                this.isSending = true;
                const msgText = this.newMessage;
                this.newMessage = ''; // clear input immediately for better UX
                const textarea = this.$el.querySelector('textarea');
                if(textarea) textarea.style.height = '48px';

                fetch('{{ route("support.chat.send") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ message: msgText })
                })
                .then(res => res.json())
                .then(data => {
                    this.isSending = false;
                    if (data.success) {
                        this.messages.push(data.message);
                        if (data.auto_response) {
                            this.messages.push(data.auto_response);
                        }
                        this.scrollToBottom();
                    }
                })
                .catch(err => {
                    console.error('Error sending message', err);
                    this.isSending = false;
                });
            },

            sendTyping() {
                if (!this.typingTimer) {
                    fetch('{{ route("support.chat.typing") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).catch(() => {});
                    
                    this.typingTimer = setTimeout(() => {
                        this.typingTimer = null;
                    }, 2000);
                }
            }
        }));
    });
</script>
@endpush
