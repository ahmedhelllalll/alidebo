@extends('users.layout')

@section('title', __('dashboard.index.reviews') . ' | ' . __('dashboard.index.title'))
@section('page_title', __('dashboard.index.reviews'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-black/5 dark:border-white/[0.04]">
        <div>
            <h1 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">{{ __('dashboard.index.reviews') }}</h1>
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mt-1">{{ __('dashboard.index.reviews_desc') }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-black/5 dark:border-white/[0.04] overflow-hidden">
        @if($reviews->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-zinc-500 dark:text-zinc-400 bg-zinc-50 dark:bg-zinc-800/50 uppercase">
                        <tr>
                            <th class="px-6 py-4 font-semibold">{{ __('dashboard.index.rating') }}</th>
                            <th class="px-6 py-4 font-semibold">{{ __('dashboard.index.comment') }}</th>
                            <th class="px-6 py-4 font-semibold">{{ __('dashboard.index.status') }}</th>
                            <th class="px-6 py-4 font-semibold">{{ __('dashboard.index.date') }}</th>
                            <th class="px-6 py-4 font-semibold text-right">{{ __('dashboard.index.action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5 dark:divide-white/[0.04]">
                        @foreach($reviews as $review)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 transition-colors" x-data="{ openReply: false }">
                                <td class="px-6 py-4">
                                    <div class="flex items-center text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'fill-current' : 'text-zinc-300 dark:text-zinc-700' }}" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <div class="text-xs text-zinc-500 mt-1">
                                        {{ $review->reviewer_name }}
                                        @if($review->reviewer_email)
                                            <span class="text-zinc-400">&bull; {{ $review->reviewer_email }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-zinc-700 dark:text-zinc-300 line-clamp-2">{{ $review->comment }}</p>
                                    @if($review->reply)
                                        <div class="mt-2 pl-3 border-l-2 border-indigo-500 text-xs">
                                            <span class="font-semibold text-zinc-900 dark:text-white">{{ __('directory.business_reply') }}</span>
                                            <p class="text-zinc-600 dark:text-zinc-400 mt-1">{{ $review->reply }}</p>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium 
                                        @if($review->status === 'approved') bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400
                                        @elseif($review->status === 'pending') bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400
                                        @else bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400
                                        @endif
                                    ">
                                        {{ __('dashboard.index.' . $review->status) ?? ucfirst($review->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400">
                                    {{ $review->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="openReply = !openReply" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium">
                                        {{ __('dashboard.index.reply_to_review') }}
                                    </button>
                                </td>
                            </tr>
                            <tr x-show="openReply" style="display: none;">
                                <td colspan="5" class="px-6 py-4 bg-zinc-50 dark:bg-zinc-800/30">
                                    <form action="{{ route('dashboard.reviews.reply', $review) }}" method="POST" class="max-w-2xl">
                                        @csrf
                                        <div class="flex gap-4">
                                            <div class="flex-1">
                                                <label class="sr-only">{{ __('dashboard.index.your_reply') }}</label>
                                                <textarea name="reply" rows="2" class="w-full rounded-xl border-0 ring-1 ring-black/5 dark:ring-white/[0.04] bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder-zinc-400 focus:ring-2 focus:ring-indigo-500 text-sm p-3 resize-none" placeholder="{{ __('dashboard.index.your_reply') }}..." required>{{ $review->reply }}</textarea>
                                            </div>
                                            <div class="flex items-end">
                                                <button type="submit" class="px-4 py-2 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-xl text-sm font-semibold hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-colors">
                                                    {{ __('dashboard.index.submit_reply') }}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-black/5 dark:border-white/[0.04]">
                {{ $reviews->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-zinc-100 dark:bg-zinc-800 mb-4">
                    <svg class="w-6 h-6 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-zinc-900 dark:text-white">{{ __('dashboard.index.no_reviews') }}</h3>
            </div>
        @endif
    </div>
</div>
@endsection
