@extends('emails.layout')

@section('content')
    <h1>نسيت كلمة المرور؟ لا تقلق..</h1>
    
    <p>شريكنا العزيز، لقد تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك على <strong>alidebo</strong>. نحن هنا لنعيدك إلى مسار نجاحك في أسرع وقت.</p>
    
    <p>ببساطة، قم بالنقر على الزر أدناه لتعيين كلمة مرور جديدة:</p>
    
    <div class="btn-container">
        <a href="{{ $url }}" class="btn">إعادة تعيين كلمة المرور</a>
    </div>

    <div class="benefit-section">
        <p style="margin-bottom: 0; color: #3c4043; font-weight: 600;">ملاحظة أمنية هامة:</p>
        <p style="font-size: 13px; margin-top: 8px;">هذا الرابط صالح لمدة <strong>60 دقيقة</strong> فقط. إذا لم تكن أنت من طلب هذا التغيير، فيمكنك تجاهل هذا الإيميل بأمان؛ لن يتم إجراء أي تعديل على حسابك دون موافقتك.</p>
    </div>

    <div class="note-section">
        حماية بياناتك هي أولويتنا القصوى. إذا واجهت أي صعوبة، فريق الدعم الفني جاهز دائماً لمساعدتك.
    </div>
@endsection