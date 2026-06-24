<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomVerifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;
    public $name;

    /**
     * بنستقبل الرابط واسم المستخدم هنا
     */
    public function __construct($url, $name)
    {
        $this->url = $url;
        $this->name = $name;
    }

    /**
     * إعدادات عنوان الإيميل والراسل
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'خطوة واحدة وتبدأ رحلتك مع AliDebo! 🚀',
        );
    }

    /**
     * تحديد ملف الـ Blade اللي فيه التصميم
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.verify-custom', // اتأكد إن الملف موجود في resources/views/emails/verify-custom.blade.php
        );
    }

    public function attachments(): array
    {
        return [];
    }
}