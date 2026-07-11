<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use Illuminate\Support\Str;

class AddMissingCategoriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'categories:add-missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add missing categories with translations in 6 languages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $categories = [
            // Old categories that need translations
            [
                'name_en' => 'Restaurants & Cafes',
                'name_ar' => 'المطاعم والمقاهي',
                'name_de' => 'Restaurants & Cafés',
                'name_es' => 'Restaurantes y Cafeterías',
                'name_tr' => 'Restoranlar ve Kafeler',
                'name_zh' => '餐厅和咖啡馆',
            ],
            [
                'name_en' => 'Real Estate',
                'name_ar' => 'العقارات',
                'name_de' => 'Immobilien',
                'name_es' => 'Bienes Raíces',
                'name_tr' => 'Emlak',
                'name_zh' => '房地产',
            ],
            [
                'name_en' => 'Construction',
                'name_ar' => 'البناء والمقاولات',
                'name_de' => 'Bauwesen',
                'name_es' => 'Construcción',
                'name_tr' => 'İnşaat',
                'name_zh' => '建筑工程',
            ],
            [
                'name_en' => 'Interior Design',
                'name_ar' => 'التصميم الداخلي',
                'name_de' => 'Innenarchitektur',
                'name_es' => 'Diseño de Interiores',
                'name_tr' => 'İç Tasarım',
                'name_zh' => '室内设计',
            ],
            [
                'name_en' => 'Medical Services',
                'name_ar' => 'الخدمات الطبية',
                'name_de' => 'Medizinische Dienste',
                'name_es' => 'Servicios Médicos',
                'name_tr' => 'Tıbbi Hizmetler',
                'name_zh' => '医疗服务',
            ],
            [
                'name_en' => 'Cars & Showrooms',
                'name_ar' => 'السيارات والمعارض',
                'name_de' => 'Autos & Ausstellungsräume',
                'name_es' => 'Coches y Concesionarios',
                'name_tr' => 'Arabalar ve Galeriler',
                'name_zh' => '汽车和展厅',
            ],
            [
                'name_en' => 'Technology & Software',
                'name_ar' => 'التكنولوجيا والبرمجيات',
                'name_de' => 'Technologie & Software',
                'name_es' => 'Tecnología y Software',
                'name_tr' => 'Teknoloji ve Yazılım',
                'name_zh' => '技术和软件',
            ],
            [
                'name_en' => 'Beauty & Personal Care',
                'name_ar' => 'التجميل والعناية الشخصية',
                'name_de' => 'Schönheit & Körperpflege',
                'name_es' => 'Belleza y Cuidado Personal',
                'name_tr' => 'Güzellik ve Kişisel Bakım',
                'name_zh' => '美容和个人护理',
            ],
            [
                'name_en' => 'Fashion & Clothing',
                'name_ar' => 'الأزياء والملابس',
                'name_de' => 'Mode & Kleidung',
                'name_es' => 'Moda y Ropa',
                'name_tr' => 'Moda ve Giyim',
                'name_zh' => '时尚和服装',
            ],
            [
                'name_en' => 'Tourism & Hotels',
                'name_ar' => 'السياحة والفنادق',
                'name_de' => 'Tourismus & Hotels',
                'name_es' => 'Turismo y Hoteles',
                'name_tr' => 'Turizm ve Oteller',
                'name_zh' => '旅游和酒店',
            ],
            [
                'name_en' => 'Shipping & Transport',
                'name_ar' => 'الشحن والنقل',
                'name_de' => 'Versand & Transport',
                'name_es' => 'Envío y Transporte',
                'name_tr' => 'Nakliye ve Taşımacılık',
                'name_zh' => '航运和运输',
            ],
            [
                'name_en' => 'Sports & Gym',
                'name_ar' => 'الرياضة والنوادي',
                'name_de' => 'Sport & Fitnessstudio',
                'name_es' => 'Deportes y Gimnasios',
                'name_tr' => 'Spor ve Spor Salonları',
                'name_zh' => '体育和健身房',
            ],
            // Recently requested categories
            [
                'name_en' => 'Building materials and ceramics',
                'name_ar' => 'المواد البناء و السراميك',
                'name_de' => 'Baumaterialien und Keramik',
                'name_es' => 'Materiales de construcción y cerámica',
                'name_tr' => 'Yapı malzemeleri ve seramik',
                'name_zh' => '建筑材料和陶瓷',
                'icon' => 'fa-hammer',
            ],
            [
                'name_en' => 'Furniture',
                'name_ar' => 'الأثاث',
                'name_de' => 'Möbel',
                'name_es' => 'Muebles',
                'name_tr' => 'Mobilya',
                'name_zh' => '家具',
                'icon' => 'fa-couch',
            ],
            [
                'name_en' => 'Furnishings',
                'name_ar' => 'المفروشات',
                'name_de' => 'Einrichtungsgegenstände',
                'name_es' => 'Mobiliario',
                'name_tr' => 'Mefruşat',
                'name_zh' => '室内装潢',
                'icon' => 'fa-bed',
            ],
            [
                'name_en' => 'Home appliances',
                'name_ar' => 'الكهرومنزلية',
                'name_de' => 'Haushaltsgeräte',
                'name_es' => 'Electrodomésticos',
                'name_tr' => 'Ev aletleri',
                'name_zh' => '家用电器',
                'icon' => 'fa-plug',
            ],
            [
                'name_en' => 'Vehicles',
                'name_ar' => 'السيارات والمركبات',
                'name_de' => 'Fahrzeuge',
                'name_es' => 'Vehículos',
                'name_tr' => 'Araçlar',
                'name_zh' => '车辆',
                'icon' => 'fa-car',
            ],
            [
                'name_en' => 'Electronics',
                'name_ar' => 'الإلكترونيات',
                'name_de' => 'Elektronik',
                'name_es' => 'Electrónica',
                'name_tr' => 'Elektronik',
                'name_zh' => '电子产品',
                'icon' => 'fa-mobile-screen',
            ],
            [
                'name_en' => 'Services',
                'name_ar' => 'الخدمات',
                'name_de' => 'Dienstleistungen',
                'name_es' => 'Servicios',
                'name_tr' => 'Hizmetler',
                'name_zh' => '服务',
                'icon' => 'fa-handshake',
            ],
            [
                'name_en' => 'Fashion',
                'name_ar' => 'الأزياء',
                'name_de' => 'Mode',
                'name_es' => 'Moda',
                'name_tr' => 'Moda',
                'name_zh' => '时尚',
                'icon' => 'fa-shirt',
            ]
        ];

        $added = 0;

        foreach ($categories as $catData) {
            $existing = Category::where('name_en', $catData['name_en'])->orWhere('name_ar', $catData['name_ar'])->first();
            
            if (!$existing) {
                $catData['slug'] = Str::slug($catData['name_en']);
                $catData['status'] = 'active';
                Category::create($catData);
                $this->info("Created: {$catData['name_en']}");
                $added++;
            } else {
                // Optionally update existing categories with missing languages
                $existing->update([
                    'name_de' => $existing->name_de ?? $catData['name_de'],
                    'name_es' => $existing->name_es ?? $catData['name_es'],
                    'name_tr' => $existing->name_tr ?? $catData['name_tr'],
                    'name_zh' => $existing->name_zh ?? $catData['name_zh'],
                    'icon' => $existing->icon ?? $catData['icon'],
                ]);
                $this->line("Updated/Skipped: {$catData['name_en']}");
            }
        }

        $this->info("Successfully added $added new categories.");
    }
}
