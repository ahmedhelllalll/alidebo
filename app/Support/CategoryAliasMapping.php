<?php

namespace App\Support;

class CategoryAliasMapping
{
    /**
     * Map of normalized search terms to target [new_category_id, level, needs_review].
     */
    public static array $aliases = [
        // Google Maps place types & production pending categories
        'mattress store'                                   => [507, 2, false],
        'mattress shop'                                    => [507, 2, false],
        'home goods store'                                 => [501, 2, false],
        'carpet store'                                     => [506, 2, false],
        'carpet and flooring shop'                         => [506, 2, false],
        'curtain store'                                    => [506, 2, false],
        'kitchen supply store'                             => [505, 2, false],
        'homewares shop'                                   => [505, 2, false], // Approved for 505 (In prod: Cairo Capital Kitchens Factory ID 545)
        'home accessories'                                 => [508, 2, false],
        'office supply store'                              => [502, 2, false],
        'department store'                                 => [501, 2, false],
        'exhibition and trade center'                      => [501, 2, false],
        'shopping & retail'                                => [501, 2, false],
        'shopping mall'                                    => [507, 2, false], // In prod data, the entry was Yanssen/Englander mattress store
        'business center'                                  => [306, 2, false],
        'lumber store'                                     => [105, 2, false],
        'woodworking supply store'                         => [105, 2, false],
        'woodworking supply shop'                          => [1708, 2, false],
        'manufacturer'                                     => [17, 1, true],
        'design engineer'                                  => [403, 2, false],
        'interior_designer'                                => [411, 2, false],
        'interior designer'                                => [411, 2, false],
        'handicraft'                                       => [508, 2, false],
        'saw mill'                                         => [105, 2, false],
        'small manual crushing plant near burhama area.'   => [1, 1, true], // Haji Hassan Group -> L1 1 + needs_review
        'haji hassan group'                                => [1, 1, true], // Haji Hassan Group -> L1 1 + needs_review
        'لراحتك'                                           => [21, 1, true], // Company لراحتك -> L1 21 + needs_review
        'liaoning borui machinery co., ltd'                => [1707, 2, false], // ID 25 approved for 1707
        'rd automation'                                    => [1602, 2, false], // ID 176 approved for 1602
        'مصنع القاهرة كابيتل للمطابخ و الاثاث'              => [505, 2, false], // ID 545 approved for 505

        // Production legacy active categories
        'furniture'                                        => [5, 1, true],
        'الأثاث'                                           => [5, 1, true],
        'كهرباء وإضاءة'                                    => [6, 1, true],
        'lighting & electrical'                            => [6, 1, true],
        'مواد بناء وسيراميك'                               => [1, 1, true],
        'building materials and ceramics'                  => [1, 1, true],
        'المواد البناء و السراميك'                         => [1, 1, true],
        'construction'                                     => [4, 1, true],
        'مقاولات وبناء'                                    => [4, 1, true],
        'interior design'                                  => [4, 1, true],
        'ديكور وتشطيبات'                                   => [4, 1, true],
        'electronics'                                      => [7, 1, true],
        'الإلكترونيات'                                     => [7, 1, true],
        'technology & software'                            => [16, 1, true],
        'تكنولوجيا وبرمجيات'                               => [16, 1, true],
        'restaurants & cafes'                              => [8, 1, true],
        'مطاعم وكافيهات'                                   => [8, 1, true],
        'real estate'                                      => [3, 1, true],
        'عقارات وأراضي'                                    => [3, 1, true],
        'medical services'                                 => [10, 1, true],
        'خدمات طبية ومستشفيات'                             => [10, 1, true],
        'cars & showrooms'                                 => [2, 1, true],
        'سيارات ومعارض'                                    => [2, 1, true],
        'beauty & personal care'                           => [11, 1, true],
        'تجميل وعناية شخصية'                               => [11, 1, true],
        'fashion & clothing'                               => [19, 1, true],
        'أزياء وملابس'                                     => [19, 1, true],
        'tourism & hotels'                                 => [13, 1, true],
        'سياحة وفنادق'                                     => [13, 1, true],
        'shipping & transport'                             => [22, 1, true],
        'نقل وشحن'                                         => [22, 1, true],
        'sports & gym'                                     => [20, 1, true],
        'رياضة وجيم'                                       => [20, 1, true],
        'furnishings'                                      => [5, 1, true],
        'المفروشات'                                        => [5, 1, true],
        'home appliances'                                  => [7, 1, true],
        'الكهرومنزلية'                                     => [7, 1, true],
        'vehicles'                                         => [2, 1, true],
        'السيارات والمركبات'                               => [2, 1, true],
        'services'                                         => [14, 1, true],
        'الخدمات'                                          => [14, 1, true],
        'food companies'                                   => [9, 1, true],
        'شركات الأغذية'                                    => [9, 1, true],
        'accessories'                                      => [1905, 2, false],
        'إكسسوارات'                                        => [1905, 2, false],
        'precious metals'                                  => [1905, 2, false],
        'معادن ثمينة'                                      => [1905, 2, false],
        'asa'                                              => [null, 0, true], // flagged for manual review, never created
    ];

    /**
     * Reverse map from New L1 Category ID to legacy categories.id for dual-write.
     */
    public static array $newToLegacyL1 = [
        1  => 67, // Building Materials -> مواد بناء وسيراميك
        2  => 18, // Cars & Vehicles -> Cars & Showrooms
        3  => 14, // Real Estate -> Real Estate
        4  => 15, // Contracting -> Construction
        5  => 26, // Furniture -> Furniture
        6  => 66, // Lighting -> كهرباء وإضاءة
        7  => 28, // Appliances & Electronics -> Home appliances
        8  => 13, // Restaurants & Cafes -> Restaurants & Cafes
        9  => 33, // Food -> Food Companies
        10 => 17, // Health & Medical -> Medical Services
        11 => 20, // Beauty -> Beauty & Personal Care
        13 => 22, // Tourism -> Tourism & Hotels
        14 => 31, // Business Services -> Services
        16 => 19, // Technology -> Technology & Software
        19 => 21, // Fashion -> Fashion & Clothing
        20 => 24, // Sports -> Sports & Gym
        22 => 23, // Shipping -> Shipping & Transport
    ];

    /**
     * Resolve a raw category string to [new_category_id, level, needs_review].
     */
    public static function resolve(?string $rawName): ?array
    {
        if (empty($rawName)) {
            return null;
        }

        $key = mb_strtolower(trim($rawName), 'UTF-8');

        if (isset(self::$aliases[$key])) {
            $mapped = self::$aliases[$key];
            if ($mapped[0] === null) {
                return null;
            }
            return [
                'new_category_id'       => $mapped[0],
                'level'                 => $mapped[1],
                'category_needs_review' => $mapped[2],
            ];
        }

        return null;
    }

    /**
     * For dual-write: get legacy category_id given a new_category_id.
     */
    public static function getLegacyCategoryId(?int $newCategoryId): ?int
    {
        if (!$newCategoryId) {
            return null;
        }

        // If it's L2, get the L1 parent ID (e.g. 507 -> 5, 105 -> 1)
        $l1Id = $newCategoryId > 100 ? (int) floor($newCategoryId / 100) : $newCategoryId;

        return self::$newToLegacyL1[$l1Id] ?? null;
    }
}
