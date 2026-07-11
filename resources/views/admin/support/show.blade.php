@extends('admin.layouts.admin')
@section('title', __('admin.chat_with') . ' ' . ($support_chat->user->name ?? ''))
@section('content')

<div class="h-[calc(100vh-96px)] sm:h-[calc(100vh-112px)] lg:h-[calc(100vh-128px)] max-w-6xl mx-auto flex flex-col relative z-10">
    
    {{-- SaaS App Container --}}
    <div class="flex-1 min-h-0 bg-white dark:bg-[#121214] rounded-3xl border border-slate-200/60 dark:border-white/[0.05] shadow-[0_8px_40px_rgba(0,0,0,0.04)] dark:shadow-[0_8px_40px_rgba(0,0,0,0.2)] overflow-hidden flex flex-col relative">
        
        {{-- App Header --}}
        <div class="h-20 px-4 sm:px-8 border-b border-slate-100 dark:border-zinc-800/60 bg-white/80 dark:bg-[#121214]/80 backdrop-blur-xl flex items-center justify-between shrink-0 z-20 sticky top-0">
            <div class="flex items-center gap-4 sm:gap-6">
                {{-- Back Button --}}
                <a href="{{ route('admin.support-chats.index') }}" title="{{ __('admin.tooltip_back_to_list') }}"
                    class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-slate-700 dark:text-zinc-500 dark:hover:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800/80 rounded-xl transition-all group">
                    <svg class="w-5 h-5 rtl:rotate-180 group-hover:-translate-x-0.5 rtl:group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </a>
                
                {{-- User Info --}}
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div class="w-12 h-12 rounded-[18px] bg-slate-50 dark:bg-zinc-800/50 border border-slate-200/50 dark:border-white/5 flex items-center justify-center text-slate-700 dark:text-zinc-300 font-[900] text-lg shadow-sm">
                            {{ strtoupper(substr($support_chat->user->name ?? '?', 0, 1)) }}
                        </div>
                        @if($support_chat->status === 'open')
                        <div class="absolute bottom-[-1px] end-[-1px] w-3.5 h-3.5 rounded-full bg-emerald-500 border-[2px] border-white dark:border-[#121214] shadow-sm"></div>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-base sm:text-lg font-[900] tracking-tight text-slate-900 dark:text-white leading-none mb-1">
                            {{ $support_chat->user->name ?? __('admin.unknown_user') }}
                        </h1>
                        <p class="text-[12px] font-medium text-slate-500 dark:text-zinc-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-envelope text-[10px] opacity-70"></i>
                            {{ $support_chat->user->email ?? '' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{-- Status Pill --}}
                <div class="hidden sm:inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-[11px] font-[900] uppercase tracking-wider
                    {{ $support_chat->status === 'open'
                        ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                        : 'bg-slate-100 dark:bg-zinc-800 text-slate-500 dark:text-zinc-400' }}">
                    @if($support_chat->status === 'open')
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                    @else
                        <div class="w-1.5 h-1.5 rounded-full bg-slate-400 dark:bg-zinc-500"></div>
                    @endif
                    {{ $support_chat->status === 'open' ? __('admin.chat_open') : __('admin.chat_closed') }}
                </div>
                
                {{-- Actions --}}
                @if($support_chat->status === 'open')
                <div class="h-6 w-px bg-slate-200 dark:bg-zinc-800 hidden sm:block mx-1"></div>
                <form action="{{ route('admin.support-chats.close', $support_chat) }}" method="POST" id="closeChatForm">
                    @csrf
                    <button type="button" onclick="openCloseModal()" title="{{ __('admin.tooltip_close_chat') }}"
                        class="h-10 px-4 inline-flex items-center justify-center gap-2 bg-white dark:bg-zinc-900 text-slate-600 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 border border-slate-200 dark:border-zinc-800 hover:border-red-200 dark:hover:border-red-500/30 rounded-xl text-[13px] font-[800] transition-all active:scale-[0.96] shadow-sm">
                        <i class="fa-solid fa-lock text-[11px]"></i>
                        <span class="hidden sm:block">{{ __('admin.close_chat') }}</span>
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Chat Messages Area --}}
        <div id="chatMessagesBody" class="flex-1 overflow-y-auto p-4 sm:p-8 space-y-6 bg-slate-50/50 dark:bg-[#09090b]/40 relative scroll-smooth">
            {{-- Loading State --}}
            <div id="chat-loading" class="absolute inset-0 flex flex-col items-center justify-center bg-white/50 dark:bg-[#121214]/50 backdrop-blur-sm z-10">
                <div class="relative flex items-center justify-center">
                    <div class="w-10 h-10 rounded-full border-[3px] border-primary/20"></div>
                    <div class="w-10 h-10 rounded-full border-[3px] border-primary border-t-transparent animate-spin absolute inset-0"></div>
                </div>
                <p class="mt-3 text-[10px] font-[900] text-primary uppercase tracking-[0.2em] animate-pulse">{{ __('admin.loading_data') }}</p>
            </div>
        </div>

        {{-- Input Area (Floating Dock Style) --}}
        @if($support_chat->status === 'open')
        <div class="p-4 sm:p-6 bg-white dark:bg-[#121214] border-t border-slate-100 dark:border-zinc-800/60 z-20">
            <form onsubmit="sendChatMessage(event)" class="max-w-4xl mx-auto relative group">
                <div class="flex items-end gap-3 bg-slate-100/80 dark:bg-zinc-900/80 border border-slate-200/80 dark:border-white/5 group-focus-within:border-primary/30 group-focus-within:bg-white dark:group-focus-within:bg-[#09090b] rounded-[24px] p-2 transition-all shadow-sm group-focus-within:shadow-[0_8px_30px_rgba(244,80,24,0.08)]">
                    
                    {{-- Avatar --}}
                    <div class="hidden sm:flex w-10 h-10 rounded-full bg-primary/10 border border-primary/20 items-center justify-center text-primary ms-2 mb-1 shrink-0">
                        <i class="fa-solid fa-headset text-[13px]"></i>
                    </div>

                    {{-- Textarea (auto-growing feel) --}}
                    <input type="text" id="chatInput" placeholder="{{ __('admin.type_reply') }}" autocomplete="off"
                        class="flex-1 bg-transparent border-none focus:ring-0 text-[14px] font-medium py-3 px-3 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-zinc-500 mb-0.5">

                    {{-- Send Button --}}
                    <button type="button" id="sendBtn" onclick="sendChatMessage(event)"
                        class="w-12 h-12 flex items-center justify-center bg-primary hover:bg-primary-light text-white rounded-full font-[900] shadow-[0_4px_15px_rgba(244,80,24,0.2)] hover:shadow-[0_8px_25px_rgba(244,80,24,0.35)] transition-all active:scale-[0.92] shrink-0 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-5 h-5 rtl:-scale-x-100 translate-x-0.5 rtl:-translate-x-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>
                    </button>
                </div>
                <div class="text-center mt-3">
                    <p class="text-[11px] font-medium text-slate-400 dark:text-zinc-500">
                        <span class="inline-flex items-center gap-1"><kbd class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 font-sans text-[9px] uppercase font-bold">Enter</kbd> to send</span>
                    </p>
                </div>
            </form>
        </div>
        @else
        <div class="p-6 bg-slate-50 dark:bg-[#0e0e11] border-t border-slate-100 dark:border-zinc-800/60 z-20 flex items-center justify-center">
            <div class="inline-flex items-center gap-3 px-6 py-3 rounded-2xl bg-white dark:bg-zinc-900 border border-slate-200/60 dark:border-zinc-800 shadow-sm text-[13px] font-[800] text-slate-500 dark:text-zinc-400">
                <i class="fa-solid fa-lock opacity-50"></i>
                {{ __('admin.chat_closed_message') }}
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Premium Close Confirmation Modal --}}
@if($support_chat->status === 'open')
<x-admin.modal id="closeChatModal" :title="__('admin.warning')" class="max-w-md">
    <div class="text-center px-4 py-8">
        <div class="w-20 h-20 rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center text-red-500 mx-auto mb-6 shadow-inner ring-4 ring-red-50 dark:ring-red-500/5 relative overflow-hidden">
            <div class="absolute inset-0 bg-red-500/20 animate-ping rounded-full"></div>
            <i class="fa-solid fa-lock text-2xl relative z-10"></i>
        </div>
        <h3 class="text-2xl font-[900] text-slate-900 dark:text-white mb-3 tracking-tight">{{ __('admin.close_chat_warning_title') }}</h3>
        <p class="text-[14px] font-medium text-slate-500 dark:text-zinc-400 leading-relaxed max-w-sm mx-auto">
            {{ __('admin.close_chat_warning_desc') }}
        </p>
    </div>
    <x-slot name="footer">
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full pb-2">
            <button type="button" onclick="closeModal('closeChatModal')" class="w-full sm:flex-1 px-5 py-3.5 bg-white dark:bg-[#121214]/80 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-[900] text-[14px] hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors shadow-sm active:scale-[0.98]">
                {{ __('admin.cancel') }}
            </button>
            <button type="button" onclick="document.getElementById('closeChatForm').submit()" class="w-full sm:flex-1 px-5 py-3.5 bg-red-500 hover:bg-red-600 text-white rounded-xl font-[900] text-[14px] shadow-[0_8px_20px_rgba(239,68,68,0.25)] hover:shadow-[0_12px_25px_rgba(239,68,68,0.35)] transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                <i class="fa-solid fa-lock text-xs"></i>
                {{ __('admin.close_chat_confirm_btn') }}
            </button>
        </div>
    </x-slot>
</x-admin.modal>
@endif

@push('scripts')
<script>
    let chatPolling = null;
    let lastMessageCount = 0;
    let lastTypingStatus = false;
    let typingTimer = null;

    function fetchChatMessages() {
        const url = '{{ route("admin.support-chats.show", $support_chat) }}?_t=' + new Date().getTime();
        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-store'
        })
        .then(res => res.json())
        .then(data => {
            const chatBody = document.getElementById('chatMessagesBody');
            const loading = document.getElementById('chat-loading');
            if (loading) loading.remove();

            // Detect if user is near bottom to auto-scroll
            const shouldScroll = chatBody.scrollHeight - chatBody.scrollTop <= chatBody.clientHeight + 150;
            const isNewMessage = data.messages.length !== lastMessageCount;
            const typingChanged = data.is_user_typing !== lastTypingStatus;
            
            if (!isNewMessage && !typingChanged && chatBody.children.length > 0) return;

            // Remove existing typing indicator to avoid duplicates
            const existingTyping = document.getElementById('typingIndicatorBox');
            if (existingTyping) existingTyping.remove();

            // Clear 'no messages' placeholder if we now have messages or typing
            if (chatBody.querySelector('.fa-messages')) {
                chatBody.innerHTML = '';
            }

            if (data.messages.length === 0 && !data.is_user_typing) {
                chatBody.innerHTML = `
                    <div class="flex flex-col items-center justify-center h-full opacity-60">
                        <div class="w-20 h-20 rounded-3xl bg-white dark:bg-zinc-800 flex items-center justify-center mb-6 shadow-sm border border-slate-100 dark:border-zinc-700">
                            <i class="fa-solid fa-messages text-2xl text-slate-300 dark:text-zinc-500"></i>
                        </div>
                        <p class="text-[16px] font-[900] text-slate-700 dark:text-zinc-300 mb-1">{{ __('admin.no_messages_yet') }}</p>
                        <p class="text-[13px] font-medium text-slate-500 dark:text-zinc-500">{{ __('admin.start_conversation') }}</p>
                    </div>
                `;
                return;
            }

            if (isNewMessage) {
                let prevSender = lastMessageCount > 0 ? data.messages[lastMessageCount - 1].sender_type : null;
                
                for (let idx = lastMessageCount; idx < data.messages.length; idx++) {
                    const msg = data.messages[idx];
                    const isAdmin = msg.sender_type === 'admin' || msg.sender_type === 'bot';
                    const nextMsg = data.messages[idx + 1];
                    const isFirstInGroup = prevSender !== msg.sender_type;
                    const isLastInGroup = !nextMsg || (nextMsg.sender_type === 'admin' || nextMsg.sender_type === 'bot') !== isAdmin;

                    const senderLabel = isAdmin ? '{{ __("admin.you") }}' : '{{ $support_chat->user->name ?? "" }}';
                    
                    // Add margin top if it's a new sender block
                    const marginTop = isFirstInGroup && idx !== 0 ? 'mt-6' : 'mt-1.5';

                    // Message Bubble Layout
                    const html = `
                        <div class="flex w-full ${isAdmin ? 'justify-end' : 'justify-start'} ${marginTop}" style="animation: msgSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; transform: translateY(10px);">
                            <div class="flex max-w-[85%] sm:max-w-[70%] ${isAdmin ? 'flex-row-reverse' : 'flex-row'} gap-3">
                                
                                <!-- Avatar -->
                                ${isFirstInGroup ? `
                                    <div class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center text-[10px] font-[900] mt-auto shadow-sm
                                        ${isAdmin ? 'bg-primary/10 border border-primary/20 text-primary' : 'bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-slate-600 dark:text-zinc-300'}">
                                        ${isAdmin ? '<i class="fa-solid fa-headset"></i>' : '{{ strtoupper(substr($support_chat->user->name ?? "?", 0, 1)) }}'}
                                    </div>
                                ` : '<div class="w-8 shrink-0"></div>'}

                                <!-- Bubble -->
                                <div class="flex flex-col ${isAdmin ? 'items-end' : 'items-start'}">
                                    
                                    <div class="px-4 py-2.5 text-[14px] leading-[1.5] shadow-sm relative group/bubble
                                        ${isAdmin
                                            ? 'bg-primary text-white ' + (isLastInGroup ? 'rounded-[18px] rounded-br-[4px]' : 'rounded-[18px]')
                                            : 'bg-white dark:bg-zinc-800 text-slate-900 dark:text-white border border-slate-200/60 dark:border-zinc-700/60 ' + (isLastInGroup ? 'rounded-[18px] rounded-bl-[4px]' : 'rounded-[18px]')
                                        }">
                                        <div class="break-words whitespace-pre-wrap">${msg.message}</div>
                                        <div class="flex items-center justify-start gap-1 mt-0.5 ${isAdmin ? 'text-white/80' : 'text-slate-500 dark:text-zinc-400'}" style="font-size: 9px; line-height: 1;" dir="ltr">
                                            ${msg.time_ago}
                                            ${isAdmin ? '<i class="fa-solid fa-check-double" style="font-size: 8px;"></i>' : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    chatBody.insertAdjacentHTML('beforeend', html);
                    prevSender = msg.sender_type;
                }
            }

            if (data.is_user_typing) {
                const typingHtml = `
                    <div id="typingIndicatorBox" class="flex w-full justify-start mt-6" style="animation: msgSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;">
                        <div class="flex max-w-[85%] sm:max-w-[70%] flex-row gap-3">
                            <div class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center text-[10px] font-[900] mt-auto shadow-sm bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-slate-600 dark:text-zinc-300">
                                {{ strtoupper(substr($support_chat->user->name ?? "?", 0, 1)) }}
                            </div>
                            <div class="flex flex-col items-start">
                                <div class="px-4 py-3.5 shadow-sm relative bg-white dark:bg-zinc-800 border border-slate-200/60 dark:border-zinc-700/60 rounded-[18px] rounded-bl-[4px] flex items-center gap-1">
                                    <div class="w-1.5 h-1.5 bg-slate-400 dark:bg-zinc-500 rounded-full" style="animation: typingWave 1.4s infinite ease-in-out; animation-delay: 0ms;"></div>
                                    <div class="w-1.5 h-1.5 bg-slate-400 dark:bg-zinc-500 rounded-full" style="animation: typingWave 1.4s infinite ease-in-out; animation-delay: 160ms;"></div>
                                    <div class="w-1.5 h-1.5 bg-slate-400 dark:bg-zinc-500 rounded-full" style="animation: typingWave 1.4s infinite ease-in-out; animation-delay: 320ms;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                chatBody.insertAdjacentHTML('beforeend', typingHtml);
            }

            // Calculate stagger animation delays for the elements that just got added
            const newElements = chatBody.querySelectorAll('[style*="animation: msgSlideUp"]');
            newElements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.03}s`;
            });

            lastMessageCount = data.messages.length;
            lastTypingStatus = data.is_user_typing;

            if (shouldScroll || isNewMessage) {
                // Use setTimeout to ensure the DOM is fully painted and animations started before scrolling
                setTimeout(() => {
                    chatBody.scrollTo({
                        top: chatBody.scrollHeight,
                        behavior: 'smooth'
                    });
                }, 150);
            }
        })
        .catch(err => {
            console.error('Chat fetch error:', err);
        });
    }

    function sendChatMessage(e) {
        e.preventDefault();
        const input = document.getElementById('chatInput');
        const btn = document.getElementById('sendBtn');
        const msg = input.value.trim();
        if(!msg) return;

        input.value = '';
        input.disabled = true;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>';

        fetch('{{ route("admin.support-chats.send", $support_chat) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: msg })
        })
        .then(res => res.json())
        .then(data => {
            input.disabled = false;
            btn.disabled = false;
            btn.innerHTML = `<svg class="w-5 h-5 rtl:-scale-x-100 translate-x-0.5 rtl:-translate-x-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>`;
            input.focus();
            fetchChatMessages();
        })
        .catch(err => {
            input.disabled = false;
            btn.disabled = false;
            btn.innerHTML = `<svg class="w-5 h-5 rtl:-scale-x-100 translate-x-0.5 rtl:-translate-x-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>`;
            if(window.showToast) showToast('error', '{{ __("admin.error") }}', '{{ __("admin.send_failed") }}');
        });
    }

    function openCloseModal() {
        const modal = document.getElementById('closeChatModal');
        if (modal) {
            if (window.modals && window.modals['closeChatModal']) {
                window.modals['closeChatModal'].show();
            } else {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }
    }

    window.closeModal = (id) => {
        if (window.modals && window.modals[id]) window.modals[id].hide();
        else document.getElementById(id).classList.add('hidden');
    };

    document.addEventListener('DOMContentLoaded', () => {
        fetchChatMessages();
        @if($support_chat->status === 'open')
            chatPolling = setInterval(fetchChatMessages, 3000);
        @endif

        const chatInput = document.getElementById('chatInput');
        if (chatInput) {
            chatInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendChatMessage(e);
                }
            });
            chatInput.addEventListener('input', function() {
                if (!typingTimer) {
                    fetch('{{ route("admin.support-chats.typing", $support_chat) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    typingTimer = setTimeout(() => { typingTimer = null; }, 2000);
                }
            });
            chatInput.focus();
        }
    });
</script>
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
@endpush
@endsection
