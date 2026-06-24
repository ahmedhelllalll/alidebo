<?php

namespace App\Services;

use App\Models\BusinessView;
use Illuminate\Support\Facades\DB;

class BusinessAnalyticsService
{
    /**
     * Get all analytics data for a given business profile.
     *
     * @param int|null $businessId
     * @return array
     */
    public function getAnalyticsData(?int $businessId): array
    {
        $totalViews = 0;
        $viewsChange = 0;
        $countryStats = collect();

        if ($businessId) {
            $totalViews = BusinessView::where('business_profile_id', $businessId)->count();

            $currentMonthViews = BusinessView::where('business_profile_id', $businessId)
                ->whereMonth('created_at', now()->month)
                ->count();

            $lastMonthViews = BusinessView::where('business_profile_id', $businessId)
                ->whereMonth('created_at', now()->subMonth()->month)
                ->count();

            if ($lastMonthViews > 0) {
                $viewsChange = round((($currentMonthViews - $lastMonthViews) / $lastMonthViews) * 100);
            } elseif ($currentMonthViews > 0) {
                $viewsChange = 100;
            }

            $countryStats = BusinessView::where('business_profile_id', $businessId)
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
                        'flag' => $this->getCountryFlag($item->country_code),
                        'count' => $item->total
                    ];
                });
        }

        return compact('totalViews', 'viewsChange', 'countryStats');
    }

    private function getCountryName($code)
    {
        $countries = [
            'EG' => 'مصر', 'SA' => 'السعودية', 'AE' => 'الإمارات', 'KW' => 'الكويت',
            'QA' => 'قطر', 'BH' => 'البحرين', 'OM' => 'عمان', 'JO' => 'الأردن',
            'LB' => 'لبنان', 'PS' => 'فلسطين', 'IQ' => 'العراق', 'SY' => 'سوريا',
            'YE' => 'اليمن', 'LY' => 'ليبيا', 'SD' => 'السودان', 'MA' => 'المغرب',
            'DZ' => 'الجزائر', 'TN' => 'تونس', 'US' => 'الولايات المتحدة',
            'GB' => 'المملكة المتحدة', 'FR' => 'فرنسا', 'DE' => 'ألمانيا',
            'CA' => 'كندا', 'AU' => 'أستراليا', 'TR' => 'تركيا',
        ];
        return $countries[$code] ?? $code;
    }

    private function getCountryFlag($code)
    {
        $flags = [
            'EG' => '🇪🇬', 'SA' => '🇸🇦', 'AE' => '🇦🇪', 'KW' => '🇰🇼',
            'QA' => '🇶🇦', 'BH' => '🇧🇭', 'OM' => '🇴🇲', 'JO' => '🇯🇴',
            'LB' => '🇱🇧', 'PS' => '🇵🇸', 'IQ' => '🇮🇶', 'SY' => '🇸🇾',
            'YE' => '🇾🇪', 'LY' => '🇱🇾', 'SD' => '🇸🇩', 'MA' => '🇲🇦',
            'DZ' => '🇩🇿', 'TN' => '🇹🇳', 'US' => '🇺🇸', 'GB' => '🇬🇧',
            'FR' => '🇫🇷', 'DE' => '🇩🇪', 'CA' => '🇨🇦', 'AU' => '🇦🇺',
            'TR' => '🇹🇷',
        ];
        return $flags[$code] ?? '🌍';
    }
}
