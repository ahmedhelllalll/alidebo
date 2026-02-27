@extends('emails.layout')

@section('content')
    <h1>أهلاً بك في عائلة alidebo، يا {{ $name }}!</h1>
    
    <p>يسعدنا جداً انضمامك إلينا. لقد قمت بالخطوة الأولى نحو بناء حضور رقمي احترافي، ونحن هنا لنضمن لك تجربة استثنائية.</p>
    
    <p>في <strong>alidebo</strong>، هدفنا هو تمكينك من إدارة أعمالك وتسويق مهاراتك بأسلوب عصري وجذاب. حسابك الآن جاهز تماماً للاستخدام.</p>
    
    <div class="btn-container">
        <a href="{{ url('/dashboard') }}" class="btn">ابدأ رحلتك الآن</a>
    </div>

    <div class="benefit-section">
        <p style="margin-bottom: 0; color: #c2410c; font-weight: 600;">كيف تبدأ؟</p>
        <ul style="font-size: 13px; margin-top: 8px; padding-right: 20px; color: #5f6368;">
            <li>قم بإكمال ملفك الشخصي ليظهر بشكل احترافي.</li>
            <li>أضف أعمالك السابقة لتبهر عملائك.</li>
            <li>تصفح الأدوات الذكية المتاحة لك في لوحة التحكم.</li>
        </ul>
    </div>

    <div class="note-section">
        فريقنا متاح دائماً لمساعدتك في أي وقت. لا تتردد في الرد على هذا الإيميل إذا كان لديك أي استفسار.
    </div>
@endsection