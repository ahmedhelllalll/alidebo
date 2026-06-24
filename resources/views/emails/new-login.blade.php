@extends('emails.layout')

@section('content')
    <h1>{{ __('emails.new_login.title') }}</h1>
    
    <p>{{ __('emails.new_login.desc', ['name' => $name]) }}</p>
    
    <div class="benefit-section" style="background-color: #f8f9fa;">
        <p style="margin: 5px 0;"><strong>{{ __('emails.new_login.time') }}</strong> {{ $time }}</p>
        <p style="margin: 5px 0;"><strong>{{ __('emails.new_login.device') }}</strong> {{ $device }}</p>
        <p style="margin: 5px 0;"><strong>{{ __('emails.new_login.ip') }}</strong> {{ $ip }}</p>
    </div>

    <p>{{ __('emails.new_login.ignore_note') }}</p>

    <div class="note-section" style="border-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}-color: #d93025; color: #d93025;">
        <strong>{{ __('emails.new_login.not_you') }}</strong> {{ __('emails.new_login.warning') }}
    </div>

    <div class="btn-container">
        <a href="{{ url('/password/reset') }}" class="btn" style="background-color: #d93025;">{{ __('emails.new_login.action') }}</a>
    </div>
@endsection