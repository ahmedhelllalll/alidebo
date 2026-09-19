<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Seed 12 Canonical B2B Product Categories
        $now = now();
        $categoriesData = [
            [
                'slug' => 'electronics-appliances',
                'name_en' => 'Consumer Electronics & Appliances',
                'name_ar' => 'إلكترونيات وأجهزة منزلية',
                'name_de' => 'Elektronik & Haushaltsgeräte',
                'name_es' => 'Electrónica y Electrodomésticos',
                'name_tr' => 'Tüketici Elektroniği ve Aletleri',
                'name_zh' => '消费电子与家用电器',
                'icon' => 'cpu',
                'order' => 1,
                'status' => 'active',
                'disk' => 'public',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'industrial-machinery-parts',
                'name_en' => 'Industrial Machinery & Equipment',
                'name_ar' => 'آلات ومعدات وقطع غيار صناعية',
                'name_de' => 'Industriemaschinen & Ausrüstung',
                'name_es' => 'Maquinaria y Equipos Industriales',
                'name_tr' => 'Endüstriyel Makineler ve Ekipmanlar',
                'name_zh' => '工业机械与设备',
                'icon' => 'cog',
                'order' => 2,
                'status' => 'active',
                'disk' => 'public',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'apparel-textiles-fabrics',
                'name_en' => 'Apparel, Textiles & Fabrics',
                'name_ar' => 'أزياء ومنسوجات وأقمشة',
                'name_de' => 'Bekleidung, Textilien & Stoffe',
                'name_es' => 'Ropa, Textiles y Telas',
                'name_tr' => 'Giyim, Tekstil ve Kumaşlar',
                'name_zh' => '服装、纺织品与面料',
                'icon' => 'shirt',
                'order' => 3,
                'status' => 'active',
                'disk' => 'public',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'food-beverage-agriculture',
                'name_en' => 'Food, Beverage & Agricultural Products',
                'name_ar' => 'مواد غذائية ومحاصيل زراعية',
                'name_de' => 'Lebensmittel & Landwirtschaft',
                'name_es' => 'Alimentos, Bebidas y Agricultura',
                'name_tr' => 'Gıda, İçecek ve Tarım Ürünleri',
                'name_zh' => '食品、饮料与农产品',
                'icon' => 'apple',
                'order' => 4,
                'status' => 'active',
                'disk' => 'public',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'packaging-printing-supplies',
                'name_en' => 'Packaging & Printing Supplies',
                'name_ar' => 'مواد تعبئة وتغليف وطباعة',
                'name_de' => 'Verpackung & Druck',
                'name_es' => 'Embalaje e Impresión',
                'name_tr' => 'Ambalaj ve Baskı Malzemeleri',
                'name_zh' => '包装与印刷材料',
                'icon' => 'package',
                'order' => 5,
                'status' => 'active',
                'disk' => 'public',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'chemicals-plastics-raw',
                'name_en' => 'Chemicals, Plastics & Raw Materials',
                'name_ar' => 'كيماويات وبلاستيك ومواد خام',
                'name_de' => 'Chemikalien, Kunststoffe & Rohstoffe',
                'name_es' => 'Químicos, Plásticos y Materias Primas',
                'name_tr' => 'Kimyasallar, Plastikler ve Hammaddeler',
                'name_zh' => '化工、塑料与原材料',
                'icon' => 'flask-conical',
                'order' => 6,
                'status' => 'active',
                'disk' => 'public',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'building-construction-materials',
                'name_en' => 'Building & Construction Materials',
                'name_ar' => 'مواد بناء وإنشاءات',
                'name_de' => 'Bau- & Konstruktionsmaterialien',
                'name_es' => 'Materiales de Construcción',
                'name_tr' => 'Yapı ve İnşaat Malzemeleri',
                'name_zh' => '建筑与施工材料',
                'icon' => 'brick-wall',
                'order' => 7,
                'status' => 'active',
                'disk' => 'public',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'hardware-tools-safety',
                'name_en' => 'Hardware, Tools & Security',
                'name_ar' => 'عدد وأدوات يدوية ومعدات أمان',
                'name_de' => 'Werkzeuge & Sicherheit',
                'name_es' => 'Ferretería, Herramientas y Seguridad',
                'name_tr' => 'Hırdavat, Aletler ve Güvenlik',
                'name_zh' => '五金、工具与安防',
                'icon' => 'wrench',
                'order' => 8,
                'status' => 'active',
                'disk' => 'public',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'automotive-parts-accessories',
                'name_en' => 'Automotive & Transportation Parts',
                'name_ar' => 'قطع غيار سيارات ومركبات',
                'name_de' => 'Fahrzeugteile & Zubehör',
                'name_es' => 'Repuestos y Accesorios Automotrices',
                'name_tr' => 'Otomotiv Parçaları ve Aksesuarları',
                'name_zh' => '汽车零部件与配件',
                'icon' => 'car',
                'order' => 9,
                'status' => 'active',
                'disk' => 'public',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'health-medical-supplies',
                'name_en' => 'Medical Supplies & Healthcare Equipment',
                'name_ar' => 'مستلزمات طبية ورعاية صحية',
                'name_de' => 'Medizinbedarf & Gesundheitsausrüstung',
                'name_es' => 'Suministros Médicos y de Salud',
                'name_tr' => 'Tıbbi Malzemeler ve Sağlık Ekipmanları',
                'name_zh' => '医疗用品与保健设备',
                'icon' => 'heart-pulse',
                'order' => 10,
                'status' => 'active',
                'disk' => 'public',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'beauty-personal-care',
                'name_en' => 'Beauty & Personal Care Products',
                'name_ar' => 'مستحضرات تجميل وعناية شخصية',
                'name_de' => 'Schönheit & Körperpflege',
                'name_es' => 'Belleza y Cuidado Personal',
                'name_tr' => 'Güzellik ve Kişisel Bakım',
                'name_zh' => '美容与个人护理',
                'icon' => 'sparkles',
                'order' => 11,
                'status' => 'active',
                'disk' => 'public',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'home-office-furniture',
                'name_en' => 'Office & Commercial Furniture',
                'name_ar' => 'أثاث ومستلزمات مكتبية وتجارية',
                'name_de' => 'Büro- & Gewerbemöbel',
                'name_es' => 'Mobiliario Comercial y de Oficina',
                'name_tr' => 'Ofis ve Ticari Mobilyalar',
                'name_zh' => '办公与商业家具',
                'icon' => 'armchair',
                'order' => 12,
                'status' => 'active',
                'disk' => 'public',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($categoriesData as $cat) {
            DB::table('product_categories')->updateOrInsert(
                ['slug' => $cat['slug']],
                $cat
            );
        }

        // 2. Safely Re-point Foreign Key on products.category_id
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_category_id_foreign');
        });

        // 3. Remap existing products according to approved plan
        $foodCatId = DB::table('product_categories')->where('slug', 'food-beverage-agriculture')->value('id');
        $elecCatId = DB::table('product_categories')->where('slug', 'electronics-appliances')->value('id');
        $beautyCatId = DB::table('product_categories')->where('slug', 'beauty-personal-care')->value('id');

        if ($foodCatId) {
            DB::table('products')->whereIn('id', [1, 16])->update(['category_id' => $foodCatId]);
        }
        if ($elecCatId) {
            DB::table('products')->whereIn('id', [3, 12])->update(['category_id' => $elecCatId]);
        }
        if ($beautyCatId) {
            DB::table('products')->where('id', 10)->update(['category_id' => $beautyCatId]);
        }

        // Any other product with an unmapped category_id set to null
        $validProductCatIds = DB::table('product_categories')->pluck('id')->all();
        DB::table('products')
            ->whereNotNull('category_id')
            ->whereNotIn('category_id', $validProductCatIds)
            ->update(['category_id' => null]);

        // 4. Add new foreign key constraint to product_categories(id)
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('category_id')
                ->references('id')
                ->on('product_categories')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        // Re-attach to categories table
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->nullOnDelete();
        });
    }
};
