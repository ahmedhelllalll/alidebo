<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\BusinessView;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $business = $user->businessProfile;
        
        $totalViews = 0;
        $viewsChange = 0;
        $countryStats = [];
        $dailyViews = [];
        
        if ($business) {
            $totalViews = BusinessView::where('business_profile_id', $business->id)->count();
            
            $currentMonthViews = BusinessView::where('business_profile_id', $business->id)
                ->whereMonth('created_at', now()->month)
                ->count();
            
            $lastMonthViews = BusinessView::where('business_profile_id', $business->id)
                ->whereMonth('created_at', now()->subMonth()->month)
                ->count();
            
            if ($lastMonthViews > 0) {
                $viewsChange = round((($currentMonthViews - $lastMonthViews) / $lastMonthViews) * 100);
            } elseif ($currentMonthViews > 0) {
                $viewsChange = 100;
            }
            
            $countryStats = BusinessView::where('business_profile_id', $business->id)
                ->whereNotNull('country_code')
                ->select('country_code', DB::raw('count(*) as total'))
                ->groupBy('country_code')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'country' => $this->getCountryName($item->country_code),
                        'code' => $item->country_code,
                        'count' => $item->total
                    ];
                });
            
            $dailyViews = BusinessView::where('business_profile_id', $business->id)
                ->whereMonth('created_at', now()->month)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->date => $item->total];
                })
                ->toArray();
        }
        
        return view('users.index', [
            'business' => $business,
            'hasProfile' => (bool) $business,
            'totalViews' => $totalViews,
            'viewsChange' => $viewsChange,
            'countryStats' => $countryStats,
            'dailyViews' => $dailyViews
        ]);
    }
    
    public function viewsChart(Request $request)
    {
        try {
            $business = Auth::user()->businessProfile;
            
            if (!$business) {
                return response()->json(['labels' => [], 'values' => []]);
            }
            
            $period = $request->get('period', 'month');
            $labels = [];
            $values = [];
            
            $daysToLookup = ($period === 'week') ? 7 : 30; // Trailing 30 days logic standard
            $startDate = now()->subDays($daysToLookup - 1)->startOfDay();
            $endDate = now()->endOfDay();
            
            // Single optimized query using native Laravel collection manipulation
            $viewsData = BusinessView::where('business_profile_id', $business->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->get()
                ->keyBy('date');
                
            for ($i = $daysToLookup - 1; $i >= 0; $i--) {
                $dateKey = now()->subDays($i)->format('Y-m-d');
                $labels[] = ($period === 'week') ? now()->subDays($i)->format('D') : now()->subDays($i)->format('d M');
                $values[] = isset($viewsData[$dateKey]) ? $viewsData[$dateKey]->count : 0;
            }
            
            return response()->json(['labels' => $labels, 'values' => $values]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    private function getCountryName($code)
    {
        $locale = app()->getLocale();
        $isAr = $locale === 'ar';
        
        $countries = [
            'EG' => $isAr ? 'مصر' : 'Egypt',
            'SA' => $isAr ? 'السعودية' : 'Saudi Arabia',
            'AE' => $isAr ? 'الإمارات' : 'UAE',
            'KW' => $isAr ? 'الكويت' : 'Kuwait',
            'QA' => $isAr ? 'قطر' : 'Qatar',
            'BH' => $isAr ? 'البحرين' : 'Bahrain',
            'OM' => $isAr ? 'عمان' : 'Oman',
            'JO' => $isAr ? 'الأردن' : 'Jordan',
            'LB' => $isAr ? 'لبنان' : 'Lebanon',
            'PS' => $isAr ? 'فلسطين' : 'Palestine',
            'IQ' => $isAr ? 'العراق' : 'Iraq',
            'SY' => $isAr ? 'سوريا' : 'Syria',
            'YE' => $isAr ? 'اليمن' : 'Yemen',
            'LY' => $isAr ? 'ليبيا' : 'Libya',
            'SD' => $isAr ? 'السودان' : 'Sudan',
            'MA' => $isAr ? 'المغرب' : 'Morocco',
            'DZ' => $isAr ? 'الجزائر' : 'Algeria',
            'TN' => $isAr ? 'تونس' : 'Tunisia',
            'US' => $isAr ? 'الولايات المتحدة' : 'United States',
            'GB' => $isAr ? 'المملكة المتحدة' : 'United Kingdom',
            'FR' => $isAr ? 'فرنسا' : 'France',
            'DE' => $isAr ? 'ألمانيا' : 'Germany',
            'CA' => $isAr ? 'كندا' : 'Canada',
            'AU' => $isAr ? 'أستراليا' : 'Australia',
            'TR' => $isAr ? 'تركيا' : 'Turkey',
            'IL' => $isAr ? 'اسرائيل' : 'Israel',
            'CN' => $isAr ? 'الصين' : 'China',
            'IN' => $isAr ? 'الهند' : 'India',
        ];
        
        return $countries[strtoupper($code)] ?? $code;
    }
}