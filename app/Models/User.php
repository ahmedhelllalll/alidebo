<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;

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
        'email_verified_at',
        'register_ip',
        'last_login_ip',
        'last_user_agent'
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

    public function sendNewLoginNotification($ip, $deviceInfo)
    {
        $location = "";
        try {
            // بنحاول نجيب الدولة بناءً على الـ IP
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}?fields=status,country&lang=ar");
            if ($response->successful() && $response['status'] === 'success') {
                $location = $response['country'] . " ";
            }
        } catch (\Exception $e) {
            // لو حصل مشكلة في الـ API بنكمل عادي
        }

        Mail::send('emails.new-login', [
            'name'   => $this->name,
            'ip'     => $location . "(" . $ip . ")",
            'device' => $deviceInfo, // هنا بنعرض البيانات اللي جاية من مكتبة Agent مباشرة
            'time'   => now()->format('Y-m-d H:i')
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
