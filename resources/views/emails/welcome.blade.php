@extends('emails.layout')

@section('content')
    <h1>{{ __('emails.welcome.title', ['name' => $name]) }}</h1>
    
    <p>{{ __('emails.welcome.desc') }}</p>
    
    <p>{{ __('emails.welcome.desc_2') }}</p>
    
    <div class="btn-container">
        <a href="{{ url('/dashboard') }}" class="btn">{{ __('emails.welcome.action') }}</a>
    </div>

    <div class="benefit-section">
        <p style="margin-bottom: 0; color: #f45018; font-weight: 600;">{{ __('emails.welcome.how_to_start') }}</p>
        <ul style="font-size: 13px; margin-top: 8px; padding-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 20px; color: #5f6368;">
            <li>{{ __('emails.welcome.step_1') }}</li>
            <li>{{ __('emails.welcome.step_2') }}</li>
            <li>{{ __('emails.welcome.step_3') }}</li>
        </ul>
    </div>

    <div class="note-section">
        {{ __('emails.welcome.support_note') }}
    </div>
@endsection