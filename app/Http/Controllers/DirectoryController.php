<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\NewCategory;
use App\Models\City;
use App\Models\Country;
use App\Services\CategoryRedirectService;
use Illuminate\Support\Facades\Cache;

class DirectoryController extends Controller
{
    public function index(Request $request)
    {
        $v2Enabled = config('categories.v2_enabled', false);

        // When v2 is enabled, if an old category slug is passed in query string, issue 301
        if ($v2Enabled && $request->filled('category')) {
            $catInput = $request->category;
            $items = is_array($catInput) ? $catInput : explode(',', $catInput);
            $hasOld = false;
            $newItems = [];

            foreach ($items as $item) {
                $trimmed = trim($item);
                $newSlug = CategoryRedirectService::getNewSlug($trimmed);
                if ($newSlug) {
                    $hasOld = true;
                    $newItems[] = $newSlug;
                } else {
                    $newItems[] = $trimmed;
                }
            }

            if ($hasOld) {
                $queryParams = $request->query();
                $queryParams['category'] = implode(',', $newItems);
                $locale = app()->getLocale() ?: 'en';
                $targetUrl = url('/' . $locale . '/directory') . '?' . http_build_query($queryParams);
                return redirect($targetUrl, 301);
            }
        }

        $cacheKey = 'directory_index_v2_' . ($v2Enabled ? 'v2_' : 'v1_') . md5(json_encode($request->all()));

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function() use ($request, $v2Enabled) {
            $query = BusinessProfile::with(['category', 'city', 'owner'])->where('status', 'approved');

            // Apply Search Filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Apply Country Filter
            if ($request->filled('country')) {
                $countries = is_array($request->country) ? $request->country : explode(',', $request->country);
                $query->whereHas('city', function($q) use ($countries) {
                    $q->whereIn('country_id', $countries);
                });
            }

            // Apply Category Filter
            if ($request->filled('category')) {
                $categoriesInput = is_array($request->category) ? $request->category : explode(',', $request->category);

                if ($v2Enabled) {
                    // V2 Filter: by new_category_id (supports both numeric ID and new slug)
                    $targetNewCategoryIds = [];

                    foreach ($categoriesInput as $cat) {
                        $cat = trim($cat);
                        $catModel = null;

                        if (is_numeric($cat)) {
                            $mappedNewSlug = CategoryRedirectService::getNewSlugForLegacyId((int)$cat);
                            if ($mappedNewSlug) {
                                $catModel = NewCategory::where('slug', $mappedNewSlug)->first();
                            }
                            if (!$catModel) {
                                $catModel = NewCategory::find((int) $cat);
                            }
                        } else {
                            $catModel = NewCategory::where('slug', $cat)->first();
                        }

                        if ($catModel) {
                            if ($catModel->level === 1) {
                                // L1: include the L1 ID itself and all of its L2 children IDs
                                $childrenIds = NewCategory::where('parent_id', $catModel->id)->pluck('id')->toArray();
                                $targetNewCategoryIds = array_merge($targetNewCategoryIds, [$catModel->id], $childrenIds);
                            } else {
                                // L2: specific subcategory
                                $targetNewCategoryIds[] = $catModel->id;
                            }
                        }
                    }

                    $targetNewCategoryIds = array_unique($targetNewCategoryIds);
                    if (!empty($targetNewCategoryIds)) {
                        $query->whereIn('new_category_id', $targetNewCategoryIds);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                } else {
                    // Legacy Filter: by category_id (supports numeric ID and legacy slug, fixing sitemap bug)
                    $categoryIds = [];

                    foreach ($categoriesInput as $cat) {
                        $cat = trim($cat);
                        if (is_numeric($cat)) {
                            $categoryIds[] = (int) $cat;
                        } else {
                            $id = CategoryRedirectService::getLegacyId($cat)
                                ?? Category::where('slug', $cat)->value('id');
                            if ($id) {
                                $categoryIds[] = $id;
                            }
                        }
                    }

                    $categoryIds = array_unique($categoryIds);

                    if (!empty($categoryIds)) {
                        $query->whereIn('category_id', $categoryIds);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                }
            }

            // Apply City Filter
            if ($request->filled('city')) {
                $query->where('city_id', $request->city);
            }

            // Apply Sort
            if ($request->filled('sort')) {
                if ($request->sort === 'newest') {
                    $query->latest('approved_at')->orderBy('id', 'desc');
                } elseif ($request->sort === 'oldest') {
                    $query->oldest('approved_at')->orderBy('id', 'asc');
                } elseif ($request->sort === 'a-z') {
                    $query->orderBy('name', 'asc')->orderBy('id', 'desc');
                } elseif ($request->sort === 'z-a') {
                    $query->orderBy('name', 'desc')->orderBy('id', 'desc');
                }
            } else {
                // Default sort
                $query->latest('approved_at')->orderBy('id', 'desc');
            }

            $businesses = $query->paginate(12)->withQueryString();
            
            $isNoIndex = false;
            if ($v2Enabled) {
                $nonEmptyCategoryIds = NewCategory::getNonEmptyCategoryIds();

                // Sidebar: show only non-empty L1 categories, with only non-empty children
                $categories = NewCategory::active()
                    ->whereIn('id', $nonEmptyCategoryIds)
                    ->parents()
                    ->with(['children' => function($q) use ($nonEmptyCategoryIds) {
                        $q->active()
                          ->whereIn('id', $nonEmptyCategoryIds)
                          ->orderBy('sort_order');
                    }])
                    ->orderBy('sort_order')
                    ->get();

                // Part E.4: If an empty category is opened by URL, flag for noindex,follow
                if ($request->filled('category')) {
                    $catParam = $request->category;
                    $catList = is_array($catParam) ? $catParam : explode(',', $catParam);
                    $requestedIds = [];
                    foreach ($catList as $c) {
                        $c = trim($c);
                        if (is_numeric($c)) {
                            $requestedIds[] = (int)$c;
                        } else {
                            $matchedCat = NewCategory::where('slug', $c)->first();
                            if ($matchedCat) {
                                $requestedIds[] = $matchedCat->id;
                                if ($matchedCat->level === 1) {
                                    $cIds = NewCategory::where('parent_id', $matchedCat->id)->pluck('id')->toArray();
                                    $requestedIds = array_merge($requestedIds, $cIds);
                                }
                            }
                        }
                    }
                    $hasAnyNonEmpty = !empty(array_intersect($requestedIds, $nonEmptyCategoryIds));
                    if (!$hasAnyNonEmpty || $businesses->total() === 0) {
                        $isNoIndex = true;
                    }
                }
            } else {
                $categories = Category::where('status', 'active')->get();
            }
            $countries = Country::where('status', 'active')->get();

            $v2CategoryMap = [];
            $v2L1Map = [];
            $v2ChildToParentMap = [];
            $v2ChildrenByParent = [];

            if ($v2Enabled) {
                foreach ($categories as $cat) {
                    $v2CategoryMap[(string)$cat->id] = $cat->slug;
                    $v2L1Map[$cat->slug] = [
                        'id' => (string) $cat->id,
                        'name' => $cat->name,
                        'slug' => $cat->slug,
                    ];
                    $childrenList = [];
                    foreach ($cat->children as $child) {
                        $v2CategoryMap[(string)$child->id] = $child->slug;
                        $childData = [
                            'id' => (string) $child->id,
                            'name' => $child->name,
                            'slug' => $child->slug,
                            'parent_slug' => $cat->slug,
                            'parent_id' => (string) $cat->id,
                        ];
                        $childrenList[] = $childData;
                        $v2ChildToParentMap[$child->slug] = $cat->slug;
                        $v2ChildToParentMap[(string)$child->id] = $cat->slug;
                    }
                    $v2ChildrenByParent[$cat->slug] = $childrenList;
                }
            }

            return compact('businesses', 'categories', 'countries', 'v2Enabled', 'v2CategoryMap', 'v2L1Map', 'v2ChildToParentMap', 'v2ChildrenByParent', 'isNoIndex');
        });

        extract($data);
        $v2Enabled = config('categories.v2_enabled', false);
        $isNoIndex = $isNoIndex ?? ($data['isNoIndex'] ?? false);
        $v2CategoryMap = $v2CategoryMap ?? ($data['v2CategoryMap'] ?? []);
        $v2L1Map = $v2L1Map ?? ($data['v2L1Map'] ?? []);
        $v2ChildToParentMap = $v2ChildToParentMap ?? ($data['v2ChildToParentMap'] ?? []);
        $v2ChildrenByParent = $v2ChildrenByParent ?? ($data['v2ChildrenByParent'] ?? []);

        $hreflangs = [];
        $locales = ['en', 'ar', 'es', 'de', 'zh', 'tr'];
        $queryParameters = $request->query();
        foreach ($locales as $loc) {
            $hreflangs[$loc] = url('/' . $loc . '/directory' . ($queryParameters ? '?' . http_build_query($queryParameters) : ''));
        }
        view()->share('hreflangs', $hreflangs);

        if ($request->ajax() || $request->header('X-Alpine-Request')) {
            if ($request->boolean('append')) {
                return view('directory.index', compact('businesses', 'categories', 'countries', 'v2Enabled', 'v2CategoryMap', 'v2L1Map', 'v2ChildToParentMap', 'v2ChildrenByParent', 'isNoIndex'))->fragment('business-items');
            }
            return view('directory.index', compact('businesses', 'categories', 'countries', 'v2Enabled', 'v2CategoryMap', 'v2L1Map', 'v2ChildToParentMap', 'v2ChildrenByParent', 'isNoIndex'))->fragment('business-grid');
        }

        return view('directory.index', compact('businesses', 'categories', 'countries', 'v2Enabled', 'v2CategoryMap', 'v2L1Map', 'v2ChildToParentMap', 'v2ChildrenByParent', 'isNoIndex'));
    }

    public function liveSearch(Request $request)
    {
        $search = $request->query('q');

        if (empty($search)) {
            return response()->json([
                'categories' => [],
                'locations' => [],
                'companies' => []
            ]);
        }

        $v2Enabled = config('categories.v2_enabled', false);
        $cacheKey = 'live_search_' . ($v2Enabled ? 'v2_' : 'v1_') . md5($search);

        $results = Cache::remember($cacheKey, now()->addMinutes(10), function() use ($search, $v2Enabled) {
            // 1. Search Categories
            if ($v2Enabled) {
                $nonEmptyCategoryIds = NewCategory::getNonEmptyCategoryIds();
                $categories = NewCategory::active()
                    ->whereIn('id', $nonEmptyCategoryIds)
                    ->where(function($q) use ($search) {
                        $q->where('name_en', 'like', "%{$search}%")
                          ->orWhere('name_ar', 'like', "%{$search}%")
                          ->orWhere('name_de', 'like', "%{$search}%")
                          ->orWhere('name_es', 'like', "%{$search}%")
                          ->orWhere('name_tr', 'like', "%{$search}%")
                          ->orWhere('name_zh', 'like', "%{$search}%");
                    })
                    ->limit(3)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'type' => 'category',
                            'name' => $item->name,
                            'icon' => $item->icon_url,
                            'url' => route('directory.index', ['category' => $item->slug])
                        ];
                    });
            } else {
                $categories = Category::where('status', 'active')
                    ->where(function($q) use ($search) {
                        $q->where('name_en', 'like', "%{$search}%")
                          ->orWhere('name_ar', 'like', "%{$search}%")
                          ->orWhere('name_de', 'like', "%{$search}%")
                          ->orWhere('name_es', 'like', "%{$search}%")
                          ->orWhere('name_tr', 'like', "%{$search}%")
                          ->orWhere('name_zh', 'like', "%{$search}%");
                    })
                    ->limit(3)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'type' => 'category',
                            'name' => $item->name,
                            'icon' => $item->icon_url,
                            'url' => route('directory.index', ['category' => $item->id])
                        ];
                    });
            }

            // 2. Search Countries
            $countries = \App\Models\Country::where('status', 'active')
                ->where(function($q) use ($search) {
                    $q->where('name_en', 'like', "%{$search}%")
                      ->orWhere('name_ar', 'like', "%{$search}%");
                })
                ->limit(2)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'country',
                        'name' => $item->name,
                        'url' => route('directory.index', ['country' => $item->id])
                    ];
                });

            // 3. Search Cities
            $cities = \App\Models\City::with('country')->where('status', 'active')
                ->where(function($q) use ($search) {
                    $q->where('name_en', 'like', "%{$search}%")
                      ->orWhere('name_ar', 'like', "%{$search}%");
                })
                ->limit(2)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'country_id' => $item->country_id,
                        'type' => 'city',
                        'name' => $item->name . ($item->country ? ', ' . $item->country->name : ''),
                        'url' => route('directory.index', ['city' => $item->id])
                    ];
                });

            // 4. Search Businesses
            $businesses = BusinessProfile::with('category')
                ->where('status', 'approved')
                ->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                })
                ->limit(5)
                ->get()
                ->map(function ($business) {
                    return [
                        'id' => $business->id,
                        'type' => 'company',
                        'name' => $business->name,
                        'logo' => $business->logo_url,
                        'category' => $business->category ? $business->category->name : null,
                        'url' => route('business.view', $business->slug)
                    ];
                });

            return [
                'categories' => $categories,
                'locations' => $countries->concat($cities),
                'companies' => $businesses
            ];
        });

        return response()->json($results);
    }
}

