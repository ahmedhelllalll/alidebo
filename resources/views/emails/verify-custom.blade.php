@extends('emails.layout')

@section('content')
    <h1>{{ __('emails.verify.title') }}</h1>
    <p>{{ __('emails.verify.desc') }}</p>
    
    <p>{{ __('emails.verify.desc_2') }}</p>
    
    <div class="btn-container">
        <a href="{{ $url }}" class="btn">{{ __('emails.verify.action') }}</a>
    </div>

    <div class="benefit-section">
        <p style="margin-bottom: 0; color: #f45018; font-weight: 600;">{{ __('emails.verify.what_awaits') }}</p>
        <p style="font-size: 13px; margin-top: 8px;">{{ __('emails.verify.awaits_desc') }}</p>
    </div>

    <div class="note-section">
        {{ __('emails.verify.vision_note') }}
    </div>

    <p style="margin-top: 30px; font-size: 13px; color: #70757a;">
        {{ __('emails.common.trouble_clicking') }}<br>
        <a href="{{ $url }}" style="color: #f45018; word-break: break-all;">{{ $url }}</a>
    </p>
@endsection