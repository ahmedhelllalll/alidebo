<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Jenssegers\Agent\Agent;
use App\Models\BusinessProfile;
use App\Models\User;
use App\Models\BusinessView;
use App\Policies\BusinessProfilePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Paginator::useTailwind();
        Gate::policy(BusinessProfile::class, BusinessProfilePolicy::class);

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

        View::composer('admin.layouts.admin', function ($view) {
            if (auth()->check()) {
                $view->with('unreadNotificationsCount', auth()->user()->unreadNotifications->count());
                $view->with('latestNotifications', auth()->user()->notifications()->take(5)->get());
            }
        });

        // Invalidate dashboard caches on database changes for real-time consistency
        User::saved(function () {
            Cache::forget('dashboard.stats');
            Cache::forget('dashboard.chart_data.week');
            Cache::forget('dashboard.chart_data.month');
        });
        User::deleted(function () {
            Cache::forget('dashboard.stats');
            Cache::forget('dashboard.chart_data.week');
            Cache::forget('dashboard.chart_data.month');
        });

        BusinessProfile::saved(function () {
            Cache::forget('dashboard.stats');
            Cache::forget('dashboard.business_statuses');
            Cache::forget('dashboard.chart_data.week');
            Cache::forget('dashboard.chart_data.month');
        });
        BusinessProfile::deleted(function () {
            Cache::forget('dashboard.stats');
            Cache::forget('dashboard.business_statuses');
            Cache::forget('dashboard.chart_data.week');
            Cache::forget('dashboard.chart_data.month');
        });

        BusinessView::created(function () {
            Cache::forget('dashboard.stats');
            Cache::forget('dashboard.chart_data.week');
            Cache::forget('dashboard.chart_data.month');
        });
    }
}

// 