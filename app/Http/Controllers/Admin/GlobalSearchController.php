<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\Country;
use App\Models\City;
use Illuminate\Support\Facades\DB;

class GlobalSearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q');
        
        // 1. Intelligence Pattern Detection
        $isCommand = str_starts_with($query, '/');
        $cleanQuery = ltrim($query, '/');
        
        if (empty($query) || (strlen($query) < 2 && !$isCommand)) {
            return response()->json([
                'users' => [],
                'businesses' => [],
                'categories' => [],
                'locations' => [],
                'commands' => $this->getEmptyCommands(),
            ]);
        }

        $results = [
            'users' => [],
            'businesses' => [],
            'categories' => [],
            'locations' => [],
            'commands' => [],
        ];

        $isEmail = str_contains($cleanQuery, '@');
        $isNumeric = is_numeric($cleanQuery);

        // 2. Commands Search (Prioritize if starts with /)
        $results['commands'] = $this->getMatchingCommands($cleanQuery);

        if ($isCommand && empty($cleanQuery)) {
             return response()->json($results);
        }

        // 3. Users Search
        if (!$isCommand || !empty($cleanQuery)) {
            $results['users'] = User::query()
                ->when($isEmail, function($q) use ($cleanQuery) {
                    return $q->where('email', 'LIKE', "%{$cleanQuery}%");
                })
                ->when($isNumeric, function($q) use ($cleanQuery) {
                    return $q->where('id', $cleanQuery);
                })
                ->unless($isEmail || $isNumeric, function($q) use ($cleanQuery) {
                    return $q->where('name', 'LIKE', "%{$cleanQuery}%")
                             ->orWhere('email', 'LIKE', "%{$cleanQuery}%")
                             ->orWhere('role', 'LIKE', "%{$cleanQuery}%");
                })
                ->limit(5)->get(['id', 'name', 'email'])
                ->map(function($user) {
                    return [
                        'id' => "user-{$user->id}",
                        'title' => $user->name,
                        'subtitle' => $user->email,
                        'url' => route('admin.users.index', ['search' => $user->email]),
                        'icon' => 'fa-user',
                        'type' => 'user'
                    ];
                });

            // 4. Businesses Search
            $results['businesses'] = BusinessProfile::query()
                ->where('name', 'LIKE', "%{$cleanQuery}%")
                ->orWhere('slug', 'LIKE', "%{$cleanQuery}%")
                ->orWhere('description', 'LIKE', "%{$cleanQuery}%")
                ->orWhere('address', 'LIKE', "%{$cleanQuery}%")
                ->when($isNumeric, function($q) use ($cleanQuery) {
                    return $q->orWhere('id', $cleanQuery);
                })
                ->limit(5)->get(['id', 'name', 'slug'])
                ->map(function($biz) {
                    return [
                        'id' => "biz-{$biz->id}",
                        'title' => $biz->name,
                        'subtitle' => $biz->slug,
                        'url' => route('admin.businesses.index', ['search' => $biz->name]),
                        'icon' => 'fa-briefcase',
                        'type' => 'business'
                    ];
                });

            // 5. Categories Search
            $locale = app()->getLocale();
            $nameField = "name_{$locale}";

            $results['categories'] = Category::query()
                ->where($nameField, 'LIKE', "%{$cleanQuery}%")
                ->when($isNumeric, function($q) use ($cleanQuery) {
                    return $q->orWhere('id', $cleanQuery);
                })
                ->limit(5)->get(['id', $nameField])
                ->map(function($cat) use ($nameField) {
                    return [
                        'id' => "cat-{$cat->id}",
                        'title' => $cat->$nameField,
                        'url' => route('admin.categories.index', ['search' => $cat->$nameField]),
                        'icon' => 'fa-layer-group',
                        'type' => 'category'
                    ];
                });

            // 6. Locations
            $countries = Country::where($nameField, 'LIKE', "%{$cleanQuery}%")->limit(3)->get();
            $cities = City::where($nameField, 'LIKE', "%{$cleanQuery}%")->limit(3)->get();

            foreach($countries as $country) {
                $results['locations'][] = [
                    'id' => "country-{$country->id}",
                    'title' => $country->$nameField,
                    'subtitle' => __('admin.country'),
                    'url' => route('admin.countries.index', ['search' => $country->$nameField]),
                    'icon' => 'fa-globe',
                    'type' => 'location'
                ];
            }
            foreach($cities as $city) {
                $results['locations'][] = [
                    'id' => "city-{$city->id}",
                    'title' => $city->$nameField,
                    'subtitle' => __('admin.city'),
                    'url' => route('admin.cities.index', ['search' => $city->$nameField]),
                    'icon' => 'fa-location-dot',
                    'type' => 'location'
                ];
            }
        }

        return response()->json($results);
    }

    private function getEmptyCommands()
    {
        $recents = [];
        
        // Fetch 2 most recent businesses
        $latestBiz = BusinessProfile::latest()->take(2)->get();
        foreach($latestBiz as $biz) {
            $recents[] = [
                'id' => "recent-biz-{$biz->id}",
                'title' => $biz->name,
                'subtitle' => __('admin.recent_businesses'),
                'url' => route('admin.businesses.index', ['search' => $biz->name]),
                'icon' => 'fa-clock-rotate-left',
                'type' => 'business'
            ];
        }

        $commands = [
            ['id' => 'cmd-biz', 'title' => __('admin.add_business'), 'url' => route('admin.businesses.create'), 'icon' => 'fa-plus-circle', 'type' => 'command'],
            ['id' => 'cmd-user', 'title' => __('admin.add_new_user'), 'url' => route('admin.users.index'), 'icon' => 'fa-user-plus', 'type' => 'command'],
            ['id' => 'cmd-theme', 'title' => __('admin.theme_light_dark') ?? __('admin.theme_toggle'), 'url' => '#', 'onclick' => 'toggleTheme()', 'icon' => 'fa-circle-half-stroke', 'type' => 'command'],
        ];

        return array_merge($recents, $commands);
    }

    private function getMatchingCommands($query)
    {
        if (empty($query)) return [];

        $allCommands = [
            ['id' => 'cmd-biz-new', 'title' => __('admin.add_business'), 'keywords' => ['create', 'add', 'business', 'new', 'إضافة', 'عمل', 'إنشاء'], 'url' => route('admin.businesses.create'), 'icon' => 'fa-plus-circle', 'type' => 'command'],
            ['id' => 'cmd-cat', 'title' => __('admin.add_category'), 'keywords' => ['create', 'add', 'category', 'new', 'إضافة', 'فئة', 'إنشاء'], 'url' => route('admin.categories.index'), 'icon' => 'fa-layer-group', 'type' => 'command'],
            ['id' => 'cmd-user', 'title' => __('admin.add_new_user'), 'keywords' => ['add', 'user', 'create', 'new', 'إضافة', 'مستخدم', 'جديد'], 'url' => route('admin.users.index'), 'icon' => 'fa-user-plus', 'type' => 'command'],
            ['id' => 'cmd-theme', 'title' => __('admin.theme_light_dark') ?? __('admin.theme_toggle'), 'keywords' => ['theme', 'dark', 'light', 'mode', 'نمط', 'مظلم', 'فاتح'], 'url' => '#', 'onclick' => 'toggleTheme()', 'icon' => 'fa-circle-half-stroke', 'type' => 'command'],
            ['id' => 'cmd-logout', 'title' => __('admin.logout'), 'keywords' => ['logout', 'exit', 'خروج', 'تسجيل'], 'url' => route('logout'), 'icon' => 'fa-right-from-bracket', 'type' => 'command'],
            ['id' => 'nav-biz', 'title' => __('admin.businesses'), 'keywords' => ['go to', 'view', 'business', 'أعمال', 'مشاريع'], 'url' => route('admin.businesses.index'), 'icon' => 'fa-briefcase', 'type' => 'command'],
            ['id' => 'nav-loc', 'title' => __('admin.locations'), 'keywords' => ['go to', 'view', 'location', 'country', 'city', 'موقع', 'دولة', 'مدينة'], 'url' => route('admin.countries.index'), 'icon' => 'fa-map-location-dot', 'type' => 'command'],
        ];

        return array_values(array_filter($allCommands, function($cmd) use ($query) {
            foreach($cmd['keywords'] as $kw) {
                if (str_contains(strtolower($kw), strtolower($query))) return true;
            }
            return false;
        }));
    }
}
