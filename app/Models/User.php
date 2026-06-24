<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
            $message->to($this->email)->subject(__('emails.subjects.verify'));
        });
    }

    public function sendPasswordResetNotification($token)
    {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ], false));

        Mail::send('emails.password-reset', ['url' => $url, 'name' => $this->name], function ($message) {
            $message->to($this->email)->subject(__('emails.subjects.reset_password'));
        });
    }

    public function sendPasswordChangedNotification()
    {
        Mail::send('emails.password-changed', ['name' => $this->name], function ($message) {
            $message->to($this->email)->subject(__('emails.subjects.password_changed'));
        });
    }

    public function sendWelcomeNotification()
    {
        Mail::send('emails.welcome', ['name' => $this->name], function ($message) {
            $message->to($this->email)->subject(__('emails.subjects.welcome'));
        });
    }

    public function sendNewLoginNotification($ip, $deviceInfo)
    {
        $location = "";
        try {
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}?fields=status,country&lang=ar");
            if ($response->successful() && $response['status'] === 'success') {
                $location = $response['country'] . " ";
            }
        } catch (\Exception $e) {}

        Mail::send('emails.new-login', [
            'name'   => $this->name,
            'ip'     => $location . "(" . $ip . ")",
            'device' => $deviceInfo,
            'time'   => now()->format('Y-m-d H:i')
        ], function ($message) {
            $message->to($this->email)->subject(__('emails.subjects.new_login'));
        });
    }

    public function businessProfiles(): HasMany
    {
        return $this->hasMany(BusinessProfile::class);
    }

    public function ownedBusinesses(): HasMany
    {
        return $this->hasMany(BusinessProfile::class, 'owner_id');
    }

    public function businessProfile(): HasOne
    {
        return $this->hasOne(BusinessProfile::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }
}