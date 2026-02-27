<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'facebook_id',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function sendEmailVerificationNotification()
    {
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->getKey(), 'hash' => sha1($this->getEmailForVerification())]
        );

        Mail::send('emails.verify-custom', ['url' => $url, 'name' => $this->name], function ($message) {
            $message->to($this->email)
                ->subject('تفعيل حسابك في alidebo');
        });
    }

    public function sendPasswordResetNotification($token)
    {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ], false));

        Mail::send('emails.password-reset', ['url' => $url, 'name' => $this->name], function ($message) {
            $message->to($this->email)
                ->subject('إعادة تعيين كلمة المرور - alidebo');
        });
    }

    public function sendPasswordChangedNotification()
    {
        Mail::send('emails.password-changed', ['name' => $this->name], function ($message) {
            $message->to($this->email)
                ->subject('تنبيه أمان: تم تغيير كلمة مرورك - alidebo');
        });
    }

    public function sendWelcomeNotification()
    {
        Mail::send('emails.welcome', ['name' => $this->name], function ($message) {
            $message->to($this->email)
                ->subject('مرحباً بك في alidebo - لنبدأ رحلة النجاح!');
        });
    }

    public function sendNewLoginNotification($ip, $userAgent)
    {
        $device = 'جهاز غير معروف';
        if (str_contains($userAgent, 'Windows')) $device = 'Windows PC';
        elseif (str_contains($userAgent, 'iPhone')) $device = 'iPhone';
        elseif (str_contains($userAgent, 'Android')) $device = 'Android Device';
        elseif (str_contains($userAgent, 'Macintosh')) $device = 'MacBook/iMac';

        $browser = 'متصفح غير معروف';
        if (str_contains($userAgent, 'Chrome')) $browser = 'Google Chrome';
        elseif (str_contains($userAgent, 'Firefox')) $browser = 'Mozilla Firefox';
        elseif (str_contains($userAgent, 'Safari') && !str_contains($userAgent, 'Chrome')) $browser = 'Apple Safari';
        elseif (str_contains($userAgent, 'Edge')) $browser = 'Microsoft Edge';

        Mail::send('emails.new-login', [
            'name' => $this->name,
            'ip' => $ip,
            'device' => $browser . ' على ' . $device,
            'time' => now()->format('Y-m-d H:i')
        ], function ($message) {
            $message->to($this->email)
                ->subject('تنبيه أمان: تسجيل دخول جديد لـ alidebo');
        });
    }

    public function businessProfiles(): HasMany
    {
        return $this->hasMany(BusinessProfile::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}