<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(Login::class, function ($event) {
            $user = $event->user;

            if ($user->wasRecentlyCreated) {
                return;
            }

            $ip = request()->ip();
            $userAgent = request()->header('User-Agent');
            $oldIp = $user->last_login_ip;
            $oldAgent = $user->last_user_agent;

            if (!empty($oldIp)) {
                if ($oldIp !== $ip || $oldAgent !== $userAgent) {
                    $user->sendNewLoginNotification($ip, $userAgent);
                }
            }

            $user->update([
                'last_login_ip' => $ip,
                'last_user_agent' => $userAgent
            ]);
        });
    }
}
