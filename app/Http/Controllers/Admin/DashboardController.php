<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\User;
use App\Models\BusinessView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Real-time stats that shouldn't be cached
        $stats['unread_leads'] = \App\Models\ContactMessage::where('status', '!=', 'read')->orWhereNull('status')->count();
        $stats['total_leads'] = \App\Models\ContactMessage::count();

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

        // Registration trends are now loaded dynamically via AJAX
        return view('admin.dashboard', compact('stats', 'recent_users', 'recent_businesses', 'businessStatuses'));
    }

    public function chartData(Request $request)
    {
        try {
            $period = $request->get('period', 'week');
            $cacheKey = "dashboard.chart_data.{$period}";
            
            $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($period) {
                $days = ($period === 'month') ? 30 : 7;
                
                $dates = collect();
                for ($i = $days - 1; $i >= 0; $i--) {
                    $dates->push(now()->subDays($i)->format('Y-m-d'));
                }
                
                $startDate = now()->subDays($days - 1)->startOfDay();
                $endDate = now()->endOfDay();
                
                $users = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('date')
                    ->pluck('count', 'date');
                    
                $businesses = BusinessProfile::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('date')
                    ->pluck('count', 'date');
                    
                $views = BusinessView::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('date')
                    ->pluck('count', 'date');
                    
                $labels = $dates->map(function ($date) use ($period) { 
                    return $period === 'week' ? \Carbon\Carbon::parse($date)->format('D') : \Carbon\Carbon::parse($date)->format('d M'); 
                })->toArray();
                
                return [
                    'labels' => $labels,
                    'users' => $dates->map(fn($date) => $users->get($date, 0))->toArray(),
                    'businesses' => $dates->map(fn($date) => $businesses->get($date, 0))->toArray(),
                    'views' => $dates->map(fn($date) => $views->get($date, 0))->toArray(),
                ];
            });
            
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
