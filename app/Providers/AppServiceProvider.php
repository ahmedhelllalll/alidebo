<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Jenssegers\Agent\Agent;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(Login::class, function ($event) {
            $user = $event->user;

            if ($user->wasRecentlyCreated) {
                return;
            }

            $agent = new Agent();
            $ip = request()->ip();
            $userAgentRaw = request()->header('User-Agent');

            // تحويل الكود لاسم جهاز ومتصفح مفهوم
            $browser = $agent->browser(); // مثال: Chrome
            $platform = $agent->platform(); // مثال: Windows
            $device = $agent->device(); // مثال: iPhone أو Macintosh

            $readableAgent = "$platform - $browser ($device)";

            $oldIp = $user->last_login_ip;
            $oldAgent = $user->last_user_agent;

            if (!empty($oldIp)) {
                if ($oldIp !== $ip || $oldAgent !== $userAgentRaw) {
                    // نبعت الاسم المفهوم للإيميل
                    $user->sendNewLoginNotification($ip, $readableAgent);
                }
            }

            $user->update([
                'last_login_ip' => $ip,
                'last_user_agent' => $userAgentRaw
            ]);
        });
    }
}

// 