@extends('emails.layout')

@section('content')
    <h1>{{ __('emails.password_changed.title') }}</h1>
    
    <p>{{ __('emails.password_changed.desc') }}</p>
    
    <p>{{ __('emails.password_changed.desc_2') }}</p>

    <div class="benefit-section">
        <p style="margin-bottom: 0; color: #3c4043; font-weight: 600;">{{ __('emails.password_changed.problem_question') }}</p>
        <p style="font-size: 13px; margin-top: 8px;">{{ __('emails.password_changed.warning') }}</p>
    </div>

    <div class="note-section">
        {{ __('emails.password_changed.security_note') }}
    </div>
@endsection