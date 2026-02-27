@extends('emails.layout')

@section('content')
    <h1>تنبيه أمان: تسجيل دخول جديد</h1>
    
    <p>مرحباً {{ $name }}،</p>
    
    <p>نود إخطارك بأنه تم تسجيل الدخول إلى حسابك في <strong>alidebo</strong> من متصفح أو جهاز جديد.</p>
    
    <div class="benefit-section" style="background-color: #f8f9fa;">
        <p style="margin: 5px 0;"><strong>الوقت:</strong> {{ $time }}</p>
        <p style="margin: 5px 0;"><strong>الجهاز/المتصفح:</strong> {{ $device }}</p>
        <p style="margin: 5px 0;"><strong>الموقع:</strong> {{ $ip }}</p>
    </div>

    <p>إذا كنت أنت من قام بهذا الإجراء، يمكنك تجاهل هذا الإيميل بأمان.</p>

    <div class="note-section" style="border-right-color: #d93025; color: #d93025;">
        <strong>أما إذا لم تكن أنت:</strong> يرجى تغيير كلمة المرور الخاصة بك فوراً وتأمين حسابك من خلال إعدادات الحماية في المنصة.
    </div>

    <div class="btn-container">
        <a href="{{ url('/password/reset') }}" class="btn" style="background-color: #d93025;">تأمين الحساب الآن</a>
    </div>
@endsection