<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\City;
use App\Models\Country;

$locations = [
    [
        'name_en' => 'Egypt',
        'cities' => [
            ['en' => 'Cairo', 'ar' => 'القاهرة'],
            ['en' => 'Giza', 'ar' => 'الجيزة'],
            ['en' => 'Alexandria', 'ar' => 'الإسكندرية'],
            ['en' => 'Dakahlia', 'ar' => 'الدقهلية'],
            ['en' => 'Qalyubia', 'ar' => 'القليوبية'],
            ['en' => 'Sharqia', 'ar' => 'الشرقية'],
            ['en' => 'Monufia', 'ar' => 'المنوفية'],
            ['en' => 'Gharbia', 'ar' => 'الغربية'],
            ['en' => 'Beheira', 'ar' => 'البحيرة'],
            ['en' => 'Damietta', 'ar' => 'دمياط'],
            ['en' => 'Matrouh', 'ar' => 'مطروح'],
            ['en' => 'Port Said', 'ar' => 'بورسعيد'],
            ['en' => 'Ismailia', 'ar' => 'الإسماعيلية'],
            ['en' => 'Suez', 'ar' => 'السويس'],
            ['en' => 'North Sinai', 'ar' => 'سيناء الشمالية'],
            ['en' => 'South Sinai', 'ar' => 'سيناء الجنوبية'],
            ['en' => 'Kafr El Sheikh', 'ar' => 'كفر الشيخ'],
            ['en' => 'Fayoum', 'ar' => 'الفيوم'],
            ['en' => 'Beni Suef', 'ar' => 'بني سويف'],
            ['en' => 'Minya', 'ar' => 'المنيا'],
            ['en' => 'Asyut', 'ar' => 'أسيوط'],
            ['en' => 'Sohag', 'ar' => 'سوهاج'],
            ['en' => 'Qena', 'ar' => 'قنا'],
            ['en' => 'Luxor', 'ar' => 'الأقصر'],
            ['en' => 'Aswan', 'ar' => 'أسوان'],
            ['en' => 'Red Sea', 'ar' => 'البحر الأحمر'],
            ['en' => 'New Valley', 'ar' => 'الوادي الجديد'],
        ]
    ],
    [
        'name_en' => 'Saudi Arabia',
        'cities' => [
            ['en' => 'Riyadh', 'ar' => 'الرياض'],
            ['en' => 'Makkah', 'ar' => 'مكة المكرمة'],
            ['en' => 'Madinah', 'ar' => 'المدينة المنورة'],
            ['en' => 'Jeddah', 'ar' => 'جدة'],
            ['en' => 'Dammam', 'ar' => 'الدمام'],
            ['en' => 'Khobar', 'ar' => 'الخبر'],
            ['en' => 'Dhahran', 'ar' => 'الظهران'],
            ['en' => 'Al Ahsa', 'ar' => 'الأحساء'],
            ['en' => 'Taif', 'ar' => 'الطائف'],
            ['en' => 'Khamis Mushait', 'ar' => 'خميس مشيط'],
            ['en' => 'Tabuk', 'ar' => 'تبوك'],
            ['en' => 'Hail', 'ar' => 'حائل'],
            ['en' => 'Najran', 'ar' => 'نجران'],
            ['en' => 'Abha', 'ar' => 'أبها'],
            ['en' => 'Al Jouf', 'ar' => 'الجوف'],
            ['en' => 'Jizan', 'ar' => 'جيزان'],
            ['en' => 'Buraydah', 'ar' => 'بريدة'],
            ['en' => 'Unaizah', 'ar' => 'عنيزة'],
            ['en' => 'Sakaka', 'ar' => 'سكاكا'],
            ['en' => 'Arar', 'ar' => 'عرعر'],
        ]
    ],
    [
        'name_en' => 'United Arab Emirates',
        'cities' => [
            ['en' => 'Abu Dhabi', 'ar' => 'أبوظبي'],
            ['en' => 'Dubai', 'ar' => 'دبي'],
            ['en' => 'Sharjah', 'ar' => 'الشارقة'],
            ['en' => 'Ajman', 'ar' => 'عجمان'],
            ['en' => 'Ras Al Khaimah', 'ar' => 'رأس الخيمة'],
            ['en' => 'Fujairah', 'ar' => 'الفجيرة'],
            ['en' => 'Umm Al Quwain', 'ar' => 'أم القيوين'],
            ['en' => 'Al Ain', 'ar' => 'العين'],
        ]
    ],
    [
        'name_en' => 'Kuwait',
        'cities' => [
            ['en' => 'Kuwait City', 'ar' => 'الكويت العاصمة'],
            ['en' => 'Jahra', 'ar' => 'الجهراء'],
            ['en' => 'Hawalli', 'ar' => 'حولي'],
            ['en' => 'Farwaniya', 'ar' => 'الفروانية'],
            ['en' => 'Mubarak Al-Kabeer', 'ar' => 'مبارك الكبير'],
            ['en' => 'Ahmadi', 'ar' => 'الأحمدي'],
        ]
    ],
    [
        'name_en' => 'Qatar',
        'cities' => [
            ['en' => 'Doha', 'ar' => 'الدوحة'],
            ['en' => 'Al Rayyan', 'ar' => 'الريان'],
            ['en' => 'Al Wakrah', 'ar' => 'الوكرة'],
            ['en' => 'Al Khor', 'ar' => 'الخور'],
            ['en' => 'Madinat ash Shamal', 'ar' => 'الشمال'],
            ['en' => 'Al Daayen', 'ar' => 'الظعاين'],
            ['en' => 'Umm Salal', 'ar' => 'أم صلال'],
        ]
    ],
];

$updated = 0;
foreach ($locations as $loc) {
    $country = Country::where('name_en', $loc['name_en'])->first();
    if (!$country) continue;
    
    foreach ($loc['cities'] as $c) {
        // Try fuzzy matching city
        $city = City::where('country_id', $country->id)
                    ->where(function($q) use ($c) {
                        $q->where('name_en', 'like', "%{$c['en']}%")
                          ->orWhere('name_ar', 'like', "%{$c['en']}%");
                    })
                    ->first();
        if ($city) {
            $city->name_ar = $c['ar'];
            $city->save();
            $updated++;
            echo "Translated {$city->name_en} to {$c['ar']}\n";
        }
    }
}
echo "Finished updating $updated native city translations!\n";
