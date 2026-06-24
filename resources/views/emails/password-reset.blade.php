@extends('emails.layout')

@section('content')
    <h1>{{ __('emails.reset.title') }}</h1>
    
    <p>{{ __('emails.reset.desc') }}</p>
    
    <p>{{ __('emails.reset.desc_2') }}</p>
    
    <div class="btn-container">
        <a href="{{ $url }}" class="btn">{{ __('emails.reset.action') }}</a>
    </div>

    <div class="benefit-section">
        <p style="margin-bottom: 0; color: #3c4043; font-weight: 600;">{{ __('emails.reset.security_note') }}</p>
        <p style="font-size: 13px; margin-top: 8px;">{{ __('emails.reset.expiry') }}</p>
    </div>

    <div class="note-section">
        {{ __('emails.reset.support_note') }}
    </div>
@endsection