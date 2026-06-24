<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = \Illuminate\Support\Facades\Cache::remember('dashboard.stats', 3600, function () {
            $now = \Carbon\Carbon::now();
            $lastWeek = $now->copy()->subDays(7);
            $twoWeeksAgo = $now->copy()->subDays(14);

            $currentWeekUsers = User::where('created_at', '>=', $lastWeek)->count();
            $previousWeekUsers = User::whereBetween('created_at', [$twoWeeksAgo, $lastWeek])->count();
            $userGrowth = $previousWeekUsers > 0 ? (($currentWeekUsers - $previousWeekUsers) / $previousWeekUsers) * 100 : ($currentWeekUsers > 0 ? 100 : 0);

            $currentWeekBusinesses = BusinessProfile::where('created_at', '>=', $lastWeek)->count();
            $previousWeekBusinesses = BusinessProfile::whereBetween('created_at', [$twoWeeksAgo, $lastWeek])->count();
            $businessGrowth = $previousWeekBusinesses > 0 ? (($currentWeekBusinesses - $previousWeekBusinesses) / $previousWeekBusinesses) * 100 : ($currentWeekBusinesses > 0 ? 100 : 0);
            
            $currentWeekViews = \App\Models\BusinessView::where('created_at', '>=', $lastWeek)->count();
            $previousWeekViews = \App\Models\BusinessView::whereBetween('created_at', [$twoWeeksAgo, $lastWeek])->count();
            $viewsGrowth = $previousWeekViews > 0 ? (($currentWeekViews - $previousWeekViews) / $previousWeekViews) * 100 : ($currentWeekViews > 0 ? 100 : 0);

            return [
                'categories' => Category::count(),
                'businesses' => BusinessProfile::count(),
                'users' => User::count(),
                'pending' => BusinessProfile::where('status', 'pending')->count(),
                'active_businesses' => BusinessProfile::where('status', 'approved')->count(),
                'total_views' => \App\Models\BusinessView::count(),
                'user_growth' => round($userGrowth, 1),
                'business_growth' => round($businessGrowth, 1),
                'views_growth' => round($viewsGrowth, 1),
                'claimed_businesses' => BusinessProfile::where('is_claimed', true)->count(),
            ];
        });

        // Eager load related data
        $recent_users = User::latest()->take(5)->get();
        $recent_businesses = BusinessProfile::with(['user', 'city.country'])->latest()->take(5)->get();

        // Data for Business Status Pie Chart
        $businessStatuses = \Illuminate\Support\Facades\Cache::remember('dashboard.business_statuses', 3600, function () {
            return BusinessProfile::select('status', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        });

        // Registration trends for the past 7 days
        $registrations = \Illuminate\Support\Facades\Cache::remember('dashboard.registrations', 3600, function () {
            $dates = collect();
            for ($i = 6; $i >= 0; $i--) {
                $dates->push(\Carbon\Carbon::now()->subDays($i)->format('Y-m-d'));
            }

            $users = User::select(\Illuminate\Support\Facades\DB::raw('DATE(created_at) as date'), \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(6)->startOfDay())
                ->groupBy('date')
                ->pluck('count', 'date');

            $businesses = BusinessProfile::select(\Illuminate\Support\Facades\DB::raw('DATE(created_at) as date'), \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(6)->startOfDay())
                ->groupBy('date')
                ->pluck('count', 'date');

            return [
                'categories' => $dates->map(function ($date) { return \Carbon\Carbon::parse($date)->format('M d'); })->toArray(),
                'users' => $dates->map(function ($date) use ($users) { return $users->get($date, 0); })->toArray(),
                'businesses' => $dates->map(function ($date) use ($businesses) { return $businesses->get($date, 0); })->toArray(),
            ];
        });

        return view('admin.dashboard', compact('stats', 'recent_users', 'recent_businesses', 'businessStatuses', 'registrations'));
    }
}
