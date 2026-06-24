<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\City;
use App\Models\Category;
use Illuminate\Support\Str;

class BasicDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Countries and Cities Data
        $locations = [
            [
                'name_en' => 'Egypt',
                'name_ar' => 'مصر',
                'code' => 'EG',
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
                'name_ar' => 'السعودية',
                'code' => 'SA',
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
                'name_ar' => 'الإمارات',
                'code' => 'AE',
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
                'name_ar' => 'الكويت',
                'code' => 'KW',
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
                'name_ar' => 'قطر',
                'code' => 'QA',
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

        foreach ($locations as $item) {
            $country = Country::create([
                'name_en' => $item['name_en'],
                'name_ar' => $item['name_ar'],
                'code' => $item['code'],
                'status' => 'active' // Added because of your new column
            ]);

            foreach ($item['cities'] as $cityData) {
                City::create([
                    'country_id' => $country->id,
                    'name_en' => $cityData['en'],
                    'name_ar' => $cityData['ar'],
                ]);
            }
        }

        // 2. Categories Data
        $categories = [
            ['en' => 'Restaurants & Cafes', 'ar' => 'مطاعم وكافيهات', 'image' => '', 'icon' => 'fa-utensils'],
            ['en' => 'Real Estate', 'ar' => 'عقارات وأراضي', 'image' => '', 'icon' => 'fa-building'],
            ['en' => 'Construction', 'ar' => 'مقاولات وبناء', 'image' => '', 'icon' => 'fa-hard-hat'],
            ['en' => 'Interior Design', 'ar' => 'ديكور وتشطيبات', 'image' => '', 'icon' => 'fa-couch'],
            ['en' => 'Medical Services', 'ar' => 'خدمات طبية ومستشفيات', 'image' => '', 'icon' => 'fa-hospital'],
            ['en' => 'Cars & Showrooms', 'ar' => 'سيارات ومعارض', 'image' => '', 'icon' => 'fa-car'],
            ['en' => 'Technology & Software', 'ar' => 'تكنولوجيا وبرمجيات', 'image' => '', 'icon' => 'fa-laptop-code'],
            ['en' => 'Beauty & Personal Care', 'ar' => 'تجميل وعناية شخصية', 'image' => '', 'icon' => 'fa-spa'],
            ['en' => 'Fashion & Clothing', 'ar' => 'أزياء وملابس', 'image' => '', 'icon' => 'fa-tshirt'],
            ['en' => 'Tourism & Hotels', 'ar' => 'سياحة وفنادق', 'image' => '', 'icon' => 'fa-plane'],
            ['en' => 'Shipping & Transport', 'ar' => 'نقل وشحن', 'image' => '', 'icon' => 'fa-truck'],
            ['en' => 'Sports & Gym', 'ar' => 'رياضة وجيم', 'image' => '', 'icon' => 'fa-dumbbell'],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name_en' => $cat['en'],
                'name_ar' => $cat['ar'],
                'slug' => Str::slug($cat['en']) . '-' . Str::random(5),
                'image' => $cat['image'],
                'icon' => $cat['icon'],
                'status' => 'active'
            ]);
        }
    }
}