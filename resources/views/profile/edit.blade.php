@extends('users.layout')

@section('title', __('dashboard.account_label') ?? 'Account Settings')
@section('page_title', __('dashboard.account_label') ?? 'Account Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Update Profile Info --}}
    <div class="glass-panel p-6 sm:p-8 rounded-2xl border border-black/5 dark:border-white/[0.04] shadow-sm relative overflow-hidden group">
        <div class="max-w-xl relative z-10">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    {{-- Update Password --}}
    <div class="glass-panel p-6 sm:p-8 rounded-2xl border border-black/5 dark:border-white/[0.04] shadow-sm relative overflow-hidden group">
        <div class="max-w-xl relative z-10">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    {{-- Delete Account --}}
    <div class="glass-panel p-6 sm:p-8 rounded-2xl border border-rose-500/10 dark:border-rose-500/20 shadow-sm relative overflow-hidden group bg-rose-50/50 dark:bg-rose-500/5">
        <div class="max-w-xl relative z-10">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
