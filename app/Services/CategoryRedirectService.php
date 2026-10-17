<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class CategoryRedirectService
{
    /**
     * Map of 45 legacy category slugs to new category slugs.
     */
    public const LEGACY_SLUG_MAP = [
        'restaurants-cafes-rp0TC'                            => 'restaurants-and-cafes', // L1 8
        'real-estate-Stxbj'                                  => 'real-estate-and-development', // L1 3
        'construction-CXwXi'                                 => 'contracting-and-construction', // L1 4
        'interior-design-0BplG'                              => 'contracting-and-construction/interior-design-and-decoration', // L2 411
        'medical-services-Ra5sa'                             => 'health-and-medical', // L1 10
        'cars-showrooms-xa7lT'                               => 'cars-and-vehicles', // L1 2
        'technology-software-RgFQo'                          => 'technology-and-software', // L1 16
        'beauty-personal-care-nR1T3'                         => 'beauty-and-personal-care', // L1 11
        'fashion-clothing-kxV5S'                             => 'fashion-and-clothing', // L1 19
        'tourism-hotels-dVUPm'                               => 'tourism-travel-and-hotels', // L1 13
        'shipping-transport-4Xe4P'                           => 'transport-shipping-and-logistics', // L1 22
        'sports-gym-gtJ9c'                                   => 'sports-and-entertainment', // L1 20
        'building-materials-and-ceramics'                    => 'building-materials-and-ceramics', // L1 1
        'furniture'                                          => 'furniture-and-furnishings', // L1 5
        'furnishings'                                        => 'furniture-and-furnishings', // L1 5
        'home-appliances'                                    => 'appliances-and-electronics', // L1 7
        'vehicles'                                           => 'cars-and-vehicles', // L1 2
        'electronics'                                        => 'appliances-and-electronics', // L1 7
        'services'                                           => 'professional-and-business-services', // L1 14
        'food-companies'                                     => 'food-and-groceries', // L1 9
        'accessories'                                        => 'fashion-and-clothing/gold-jewelry-and-watches', // L2 1905
        'precious-metals'                                    => 'fashion-and-clothing/gold-jewelry-and-watches', // L2 1905
        'kitchen-supply-store-coam'                          => 'furniture-and-furnishings/kitchens', // L2 505
        'handicraft-nyre'                                    => 'furniture-and-furnishings/decor-and-home-accessories', // L2 508
        'shopping-mall-hsyu'                                 => 'furniture-and-furnishings/mattresses-and-beds', // L2 507
        'small-manual-crushing-plant-near-burhama-area-tior' => 'building-materials-and-ceramics', // L1 1
        'exhibition-and-trade-center-sqyn'                   => 'furniture-and-furnishings/home-furniture', // L2 501
        'shopping-retail-yzsw'                               => 'furniture-and-furnishings/home-furniture', // L2 501
        'manufacturer-lzwr'                                  => 'industry-and-manufacturing', // L1 17
        'mattress-store-4w3q'                                => 'furniture-and-furnishings/mattresses-and-beds', // L2 507
        'carpet-store-c2jm'                                  => 'furniture-and-furnishings/curtains-and-carpets', // L2 506
        'lumber-store-6dxr'                                  => 'building-materials-and-ceramics/wood-and-doors', // L2 105
        'woodworking-supply-store-fqtu'                      => 'building-materials-and-ceramics/wood-and-doors', // L2 105
        'business-center-xt3l'                               => 'real-estate-and-development/offices-and-commercial-spaces', // L2 306
        'curtain-store-fovk'                                 => 'furniture-and-furnishings/curtains-and-carpets', // L2 506
        'design-engineer-qzfv'                               => 'contracting-and-construction/engineering-offices-and-consultancy', // L2 403
        'woodworking-supply-shop-bnwq'                       => 'industry-and-manufacturing/furniture-factories', // L2 1708
        'home-goods-store-hqab'                              => 'furniture-and-furnishings/home-furniture', // L2 501
        'department-store-uzgt'                              => 'furniture-and-furnishings/home-furniture', // L2 501
        'carpet-and-flooring-shop-8izz'                      => 'furniture-and-furnishings/curtains-and-carpets', // L2 506
        'office-supply-store-v6yg'                           => 'furniture-and-furnishings/office-furniture', // L2 502
        'saw-mill-vvi9'                                      => 'building-materials-and-ceramics/wood-and-doors', // L2 105
        'homewares-shop-3hc6'                                => 'furniture-and-furnishings/kitchens', // L2 505
        'khrbaaa-oadaaa-2heh'                                => 'lighting-and-electrical', // L1 6
        'moad-bnaaa-osyramyk-75wd'                           => 'building-materials-and-ceramics', // L1 1
    ];

    /**
     * Map of legacy category slugs to legacy category IDs.
     */
    public const LEGACY_SLUG_TO_ID = [
        'restaurants-cafes-rp0TC'                            => 13,
        'real-estate-Stxbj'                                  => 14,
        'construction-CXwXi'                                 => 15,
        'interior-design-0BplG'                              => 16,
        'medical-services-Ra5sa'                             => 17,
        'cars-showrooms-xa7lT'                               => 18,
        'technology-software-RgFQo'                          => 19,
        'beauty-personal-care-nR1T3'                         => 20,
        'fashion-clothing-kxV5S'                             => 21,
        'tourism-hotels-dVUPm'                               => 22,
        'shipping-transport-4Xe4P'                           => 23,
        'sports-gym-gtJ9c'                                   => 24,
        'building-materials-and-ceramics'                    => 25,
        'furniture'                                          => 26,
        'furnishings'                                        => 27,
        'home-appliances'                                    => 28,
        'vehicles'                                           => 29,
        'electronics'                                        => 30,
        'services'                                           => 31,
        'food-companies'                                     => 33,
        'accessories'                                        => 34,
        'precious-metals'                                    => 35,
        'kitchen-supply-store-coam'                          => 40,
        'handicraft-nyre'                                    => 41,
        'shopping-mall-hsyu'                                 => 42,
        'small-manual-crushing-plant-near-burhama-area-tior' => 43,
        'exhibition-and-trade-center-sqyn'                   => 44,
        'shopping-retail-yzsw'                               => 45,
        'manufacturer-lzwr'                                  => 46,
        'mattress-store-4w3q'                                => 52,
        'carpet-store-c2jm'                                  => 53,
        'lumber-store-6dxr'                                  => 54,
        'woodworking-supply-store-fqtu'                      => 55,
        'business-center-xt3l'                               => 56,
        'curtain-store-fovk'                                 => 57,
        'design-engineer-qzfv'                               => 58,
        'woodworking-supply-shop-bnwq'                       => 59,
        'home-goods-store-hqab'                              => 60,
        'department-store-uzgt'                              => 61,
        'carpet-and-flooring-shop-8izz'                      => 62,
        'office-supply-store-v6yg'                           => 63,
        'saw-mill-vvi9'                                      => 64,
        'homewares-shop-3hc6'                                => 65,
        'khrbaaa-oadaaa-2heh'                                => 66,
        'moad-bnaaa-osyramyk-75wd'                           => 67,
    ];

    /**
     * Map of base slug prefixes without random hashes (e.g. "restaurants-cafes-mtUeh" -> "restaurants-cafes").
     */
    public const LEGACY_PREFIX_MAP = [
        'restaurants-cafes'                            => 'restaurants-and-cafes',
        'real-estate'                                  => 'real-estate-and-development',
        'construction'                                 => 'contracting-and-construction',
        'interior-design'                              => 'contracting-and-construction/interior-design-and-decoration',
        'medical-services'                             => 'health-and-medical',
        'cars-showrooms'                               => 'cars-and-vehicles',
        'technology-software'                          => 'technology-and-software',
        'beauty-personal-care'                         => 'beauty-and-personal-care',
        'fashion-clothing'                             => 'fashion-and-clothing',
        'tourism-hotels'                               => 'tourism-travel-and-hotels',
        'shipping-transport'                           => 'transport-shipping-and-logistics',
        'sports-gym'                                   => 'sports-and-entertainment',
        'kitchen-supply-store'                         => 'furniture-and-furnishings/kitchens',
        'handicraft'                                   => 'furniture-and-furnishings/decor-and-home-accessories',
        'shopping-mall'                                => 'furniture-and-furnishings/mattresses-and-beds',
        'small-manual-crushing-plant-near-burhama-area' => 'building-materials-and-ceramics',
        'exhibition-and-trade-center'                  => 'furniture-and-furnishings/home-furniture',
        'shopping-retail'                              => 'furniture-and-furnishings/home-furniture',
        'manufacturer'                                 => 'industry-and-manufacturing',
        'mattress-store'                               => 'furniture-and-furnishings/mattresses-and-beds',
        'carpet-store'                                 => 'furniture-and-furnishings/curtains-and-carpets',
        'lumber-store'                                 => 'building-materials-and-ceramics/wood-and-doors',
        'woodworking-supply-store'                     => 'building-materials-and-ceramics/wood-and-doors',
        'business-center'                              => 'real-estate-and-development/offices-and-commercial-spaces',
        'curtain-store'                                => 'furniture-and-furnishings/curtains-and-carpets',
        'design-engineer'                              => 'contracting-and-construction/engineering-offices-and-consultancy',
        'woodworking-supply-shop'                      => 'industry-and-manufacturing/furniture-factories',
        'home-goods-store'                             => 'furniture-and-furnishings/home-furniture',
        'department-store'                             => 'furniture-and-furnishings/home-furniture',
        'carpet-and-flooring-shop'                     => 'furniture-and-furnishings/curtains-and-carpets',
        'office-supply-store'                          => 'furniture-and-furnishings/office-furniture',
        'saw-mill'                                     => 'building-materials-and-ceramics/wood-and-doors',
        'homewares-shop'                               => 'furniture-and-furnishings/kitchens',
        'khrbaaa-oadaaa'                               => 'lighting-and-electrical',
        'moad-bnaaa-osyramyk'                          => 'building-materials-and-ceramics',
    ];

    public const LEGACY_PREFIX_TO_ID = [
        'restaurants-cafes'                            => 13,
        'real-estate'                                  => 14,
        'construction'                                 => 15,
        'interior-design'                              => 16,
        'medical-services'                             => 17,
        'cars-showrooms'                               => 18,
        'technology-software'                          => 19,
        'beauty-personal-care'                         => 20,
        'fashion-clothing'                             => 21,
        'tourism-hotels'                               => 22,
        'shipping-transport'                           => 23,
        'sports-gym'                                   => 24,
        'kitchen-supply-store'                         => 40,
        'handicraft'                                   => 41,
        'shopping-mall'                                => 42,
        'small-manual-crushing-plant-near-burhama-area' => 43,
        'exhibition-and-trade-center'                  => 44,
        'shopping-retail'                              => 45,
        'manufacturer'                                 => 46,
        'mattress-store'                               => 52,
        'carpet-store'                                 => 53,
        'lumber-store'                                 => 54,
        'woodworking-supply-store'                     => 55,
        'business-center'                              => 56,
        'curtain-store'                                => 57,
        'design-engineer'                              => 58,
        'woodworking-supply-shop'                      => 59,
        'home-goods-store'                             => 60,
        'department-store'                             => 61,
        'carpet-and-flooring-shop'                     => 62,
        'office-supply-store'                          => 63,
        'saw-mill'                                     => 64,
        'homewares-shop'                               => 65,
        'khrbaaa-oadaaa'                               => 66,
        'moad-bnaaa-osyramyk'                          => 67,
    ];

    /**
     * Self-check verifying consistency between the four constant maps.
     * Returns an array of any discrepancies found.
     */
    public static function checkMapConsistency(): array
    {
        $discrepancies = [];

        $missingInId = array_diff_key(self::LEGACY_SLUG_MAP, self::LEGACY_SLUG_TO_ID);
        $missingInSlug = array_diff_key(self::LEGACY_SLUG_TO_ID, self::LEGACY_SLUG_MAP);
        $missingInPrefixId = array_diff_key(self::LEGACY_PREFIX_MAP, self::LEGACY_PREFIX_TO_ID);
        $missingInPrefixSlug = array_diff_key(self::LEGACY_PREFIX_TO_ID, self::LEGACY_PREFIX_MAP);

        if (!empty($missingInId)) {
            $discrepancies['LEGACY_SLUG_MAP_missing_in_LEGACY_SLUG_TO_ID'] = array_keys($missingInId);
        }
        if (!empty($missingInSlug)) {
            $discrepancies['LEGACY_SLUG_TO_ID_missing_in_LEGACY_SLUG_MAP'] = array_keys($missingInSlug);
        }
        if (!empty($missingInPrefixId)) {
            $discrepancies['LEGACY_PREFIX_MAP_missing_in_LEGACY_PREFIX_TO_ID'] = array_keys($missingInPrefixId);
        }
        if (!empty($missingInPrefixSlug)) {
            $discrepancies['LEGACY_PREFIX_TO_ID_missing_in_LEGACY_PREFIX_MAP'] = array_keys($missingInPrefixSlug);
        }

        return $discrepancies;
    }

    /**
     * Cache list of valid new_categories slugs to prevent loops.
     */
    protected static ?array $validNewSlugs = null;

    /**
     * Get array of all valid slugs in new_categories.
     */
    public static function getValidNewSlugs(): array
    {
        if (self::$validNewSlugs === null) {
            try {
                self::$validNewSlugs = \App\Models\NewCategory::pluck('slug')->all();
            } catch (\Throwable $e) {
                self::$validNewSlugs = [];
            }
        }
        return self::$validNewSlugs;
    }

    /**
     * Determine if a slug candidate is already a valid slug in new_categories.
     */
    public static function isValidNewSlug(string $slug): bool
    {
        $decoded = urldecode(trim($slug));
        return in_array($decoded, self::getValidNewSlugs(), true);
    }

    /**
     * Map of legacy category ID to new category slug.
     */
    protected static ?array $legacyIdToNewSlug = null;

    /**
     * Get new category slug for a numeric legacy category ID.
     */
    public static function getNewSlugForLegacyId(int $legacyId): ?string
    {
        if (self::$legacyIdToNewSlug === null) {
            $map = [];
            foreach (self::LEGACY_SLUG_TO_ID as $slug => $id) {
                if (isset(self::LEGACY_SLUG_MAP[$slug])) {
                    $map[$id] = self::LEGACY_SLUG_MAP[$slug];
                }
            }
            self::$legacyIdToNewSlug = $map;
        }

        return self::$legacyIdToNewSlug[$legacyId] ?? null;
    }

    /**
     * Resolve a legacy category slug or numeric ID to its new equivalent slug.
     */
    public static function getNewSlug(string $slug): ?string
    {
        $slug = trim($slug);

        // 1. Valid-new-slug guard: if already in new_categories, do not redirect
        if (self::isValidNewSlug($slug)) {
            return null;
        }

        // 2. Numeric legacy category ID resolution (e.g. "16", "26", "66")
        if (ctype_digit($slug)) {
            $newSlug = self::getNewSlugForLegacyId((int)$slug);
            if ($newSlug) {
                return $newSlug;
            }
        }

        // 3. Exact match in legacy slug map
        if (isset(self::LEGACY_SLUG_MAP[$slug])) {
            return self::LEGACY_SLUG_MAP[$slug];
        }

        // 4. Exact match in prefix map (e.g. legacy slugs without hash)
        if (isset(self::LEGACY_PREFIX_MAP[$slug])) {
            return self::LEGACY_PREFIX_MAP[$slug];
        }

        // 5. Regex ^{prefix}-[A-Za-z0-9]{4,5}$ for known prefixes
        foreach (self::LEGACY_PREFIX_MAP as $prefix => $target) {
            if (preg_match('/^' . preg_quote($prefix, '/') . '-[A-Za-z0-9]{4,5}$/', $slug)) {
                return $target;
            }
        }

        return null;
    }

    /**
     * Resolve a legacy category slug to its legacy category ID.
     */
    public static function getLegacyId(string $slug): ?int
    {
        $slug = trim($slug);

        if (isset(self::LEGACY_SLUG_TO_ID[$slug])) {
            return self::LEGACY_SLUG_TO_ID[$slug];
        }

        if (isset(self::LEGACY_PREFIX_TO_ID[$slug])) {
            return self::LEGACY_PREFIX_TO_ID[$slug];
        }

        foreach (self::LEGACY_PREFIX_TO_ID as $prefix => $id) {
            if (preg_match('/^' . preg_quote($prefix, '/') . '-[A-Za-z0-9]{4,5}$/', $slug)) {
                return $id;
            }
        }

        return null;
    }

    /**
     * Determine if a string is a recognized legacy category slug.
     */
    public static function isLegacySlug(string $slug): bool
    {
        if (self::isValidNewSlug($slug)) {
            return false;
        }

        return self::getNewSlug($slug) !== null || self::getLegacyId($slug) !== null;
    }

    /**
     * Handle incoming request in middleware for old category URLs (both path and query).
     */
    public static function handleRequest(Request $request): ?RedirectResponse
    {
        // 1. Only process GET and HEAD requests
        if (!$request->isMethod('GET') && !$request->isMethod('HEAD')) {
            return null;
        }

        $v2Enabled = config('categories.v2_enabled', false);
        $redirectStatus = (int)config('categories.redirect_status', 302);
        $path = trim($request->path(), '/');
        $segments = explode('/', $path);

        // Detect locale if present as first segment
        $locales = ['en', 'ar', 'es', 'de', 'zh', 'tr'];
        $locale = 'en';
        if (!empty($segments) && in_array($segments[0], $locales, true)) {
            $locale = array_shift($segments);
        } else {
            $locale = session('locale', $request->getPreferredLanguage($locales) ?: 'en');
        }

        $remainingPath = implode('/', $segments);

        // Query string check on /directory or /{locale}/directory
        if ($remainingPath === 'directory') {
            $rawCategory = $request->query('category');

            // Guard: If category is missing, empty, or not a string (e.g. array ?category[]=a), ignore and never throw
            if ($rawCategory === null || !is_string($rawCategory) || trim($rawCategory) === '') {
                return null;
            }

            if ($v2Enabled) {
                // If the entire parameter is already a valid new slug, no redirect needed
                if (self::isValidNewSlug($rawCategory)) {
                    return null;
                }

                $items = explode(',', $rawCategory);
                $hasOld = false;
                $newItems = [];

                foreach ($items as $item) {
                    $trimmed = trim($item);
                    $newSlug = self::getNewSlug($trimmed);
                    if ($newSlug && $newSlug !== $trimmed) {
                        $hasOld = true;
                        $newItems[] = $newSlug;
                    } else {
                        $newItems[] = $trimmed;
                    }
                }

                if ($hasOld) {
                    $query = $request->query();
                    $query['category'] = implode(',', $newItems);
                    // Keep the slash raw in Location parameter (?category=contracting-and-construction/interior-design-and-decoration)
                    $queryString = str_replace('%2F', '/', http_build_query($query));
                    $url = url('/' . $locale . '/directory') . '?' . $queryString;
                    return redirect($url, $redirectStatus);
                }
            }
        }

        return null;
    }
}
