<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\NewCategory;
use App\Models\ProductCategory;

class SeedFinal2CategoriesCommand extends Command
{
    protected $signature = 'categories:seed-final2 
                            {--target=both : Target tables: "new_categories", "product_categories", or "both"}
                            {--parent-mode=l1-17 : For new_categories: "l1-17" (under L1 ID 17 Industry & Manufacturing) or "l1-new" (create L1 ID 26 Industrial Machinery)}
                            {--dry-run : Run in dry-run mode without committing changes}';

    protected $description = 'Seed missing Industrial Machinery subcategories extracted from Final_2.xlsx with 6 language translations';

    public const CATEGORIES = [
        [
            'code' => 'hvac_refrigeration_elevators',
            'slug_part' => 'hvac-refrigeration-elevators',
            'icon' => 'wind',
            'name_ar' => 'التكييف والتبريد والمصاعد',
            'name_en' => 'HVAC, Refrigeration & Elevators',
            'name_de' => 'HLK, Kältetechnik & Aufzüge',
            'name_es' => 'Climatización, Refrigeración y Ascensores',
            'name_tr' => 'İklimlendirme, Soğutma ve Asansörler',
            'name_zh' => '暖通空调、制冷与电梯',
            'seo_title_ar' => 'التكييف والتبريد والمصاعد في مصر | دليل الشركات والموردين - alidebo',
            'seo_title_en' => 'HVAC, Refrigeration & Elevators Suppliers & Companies | Alidebo',
            'meta_desc_ar' => 'دليل شركات وتوريدات التكييف والتبريد وأنظمة المصاعد في مصر: ابحث وقارن وتواصل مباشرة عبر alidebo.com',
            'meta_desc_en' => 'Find verified HVAC, industrial refrigeration, and elevator suppliers, contractors, and manufacturers on Alidebo B2B Directory.',
        ],
        [
            'code' => 'spare_parts_machining',
            'slug_part' => 'spare-parts-machine-fabrication-machining',
            'icon' => 'cpu',
            'name_ar' => 'قطع غيار وتصنيع وتشغيل الماكينات',
            'name_en' => 'Spare Parts, Machine Fabrication & Machining',
            'name_de' => 'Ersatzteile, Maschinenbau & Zerspanung',
            'name_es' => 'Repuestos, Fabricación y Mecanizado de Maquinaria',
            'name_tr' => 'Yedek Parça, Makine İmalatı ve Talaşlı İmalat',
            'name_zh' => '备件、机械制造与机加工',
            'seo_title_ar' => 'قطع غيار وتصنيع وتشغيل الماكينات في مصر | دليل المصانع والورش - alidebo',
            'seo_title_en' => 'Spare Parts, Machine Fabrication & Machining Companies | Alidebo',
            'meta_desc_ar' => 'دليل مصانع وورش تشغيل المعادن والماكينات وتصنيع وتوريد قطع الغيار الصناعية في مصر عبر alidebo.com',
            'meta_desc_en' => 'Find verified machine fabrication, CNC machining workshops, and industrial spare parts manufacturers on Alidebo B2B Directory.',
        ],
        [
            'code' => 'pumps_compressors',
            'slug_part' => 'pumps-and-compressors',
            'icon' => 'gauge',
            'name_ar' => 'مضخات وضواغط',
            'name_en' => 'Pumps & Compressors',
            'name_de' => 'Pumpen & Kompressoren',
            'name_es' => 'Bombas y Compresores',
            'name_tr' => 'Pompalar ve Kompresörler',
            'name_zh' => '泵与压缩机',
            'seo_title_ar' => 'مضخات وضواغط في مصر | دليل الموردين والمصنعين - alidebo',
            'seo_title_en' => 'Industrial Pumps & Compressors Suppliers & Manufacturers | Alidebo',
            'meta_desc_ar' => 'دليل شركات وموردي المضخات الصناعية وضواغط الهواء وأنظمة الضغط في مصر عبر alidebo.com',
            'meta_desc_en' => 'Browse verified manufacturers and suppliers of industrial pumps, air compressors, and vacuum systems on Alidebo B2B Directory.',
        ],
        [
            'code' => 'food_restaurant_equipment',
            'slug_part' => 'food-processing-restaurant-equipment',
            'icon' => 'utensils',
            'name_ar' => 'معدات الأغذية والمطاعم',
            'name_en' => 'Food Processing & Commercial Kitchen Equipment',
            'name_de' => 'Lebensmittelverarbeitungs- & Gastronomieausstattung',
            'name_es' => 'Equipos para Procesamiento de Alimentos y Restaurantes',
            'name_tr' => 'Gıda İşleme ve Restoran Ekipmanları',
            'name_zh' => '食品加工与餐饮设备',
            'seo_title_ar' => 'معدات الأغذية والمطاعم في مصر | تجهيزات المطابخ والمصانع - alidebo',
            'seo_title_en' => 'Food Processing & Commercial Kitchen Equipment | Alidebo',
            'meta_desc_ar' => 'دليل موردي ومصنعي معدات تصنيع الأغذية وتجهيزات المطابخ والمطاعم والكافيهات في مصر عبر alidebo.com',
            'meta_desc_en' => 'Connect with certified food processing machinery manufacturers and commercial kitchen equipment suppliers on Alidebo B2B Directory.',
        ],
        [
            'code' => 'construction_equipment_cranes',
            'slug_part' => 'construction-equipment-cranes-rental',
            'icon' => 'truck',
            'name_ar' => 'معدات البناء والرافعات والتأجير',
            'name_en' => 'Construction Equipment, Cranes & Rental',
            'name_de' => 'Baumaschinen, Kräne & Vermietung',
            'name_es' => 'Maquinaria de Construcción, Grúas y Alquiler',
            'name_tr' => 'İnşaat Makineleri, Vinçler ve Kiralama',
            'name_zh' => '建筑工程机械、起重机与租赁',
            'seo_title_ar' => 'معدات البناء والرافعات والتأجير في مصر | شركات ومعدات ثقيلة - alidebo',
            'seo_title_en' => 'Construction Heavy Equipment, Cranes & Rental Companies | Alidebo',
            'meta_desc_ar' => 'دليل شركات وموردي معدات البناء الثقيلة والأوناش والرافعات وخدمات التأجير في مصر عبر alidebo.com',
            'meta_desc_en' => 'Find heavy construction equipment, tower cranes, mobile lifting equipment, and machinery rental providers on Alidebo B2B Directory.',
        ],
        [
            'code' => 'agricultural_power_equipment',
            'slug_part' => 'agricultural-power-specialized-equipment',
            'icon' => 'zap',
            'name_ar' => 'معدات الزراعة والطاقة والمعدات المتخصصة',
            'name_en' => 'Agricultural, Power & Specialized Equipment',
            'name_de' => 'Landwirtschafts-, Energie- & Spezialausrüstung',
            'name_es' => 'Equipos Agrícolas, de Energía y Especializados',
            'name_tr' => 'Tarım, Enerji ve Özel Ekipmanlar',
            'name_zh' => '农业、动力与专业专用设备',
            'seo_title_ar' => 'معدات الزراعة والطاقة والمعدات المتخصصة في مصر | alidebo',
            'seo_title_en' => 'Agricultural, Power & Specialized Industrial Equipment | Alidebo',
            'meta_desc_ar' => 'دليل موردي المعدات الزراعية والمولدات الكهربائية وأنظمة الطاقة والمعدات الصناعية المتخصصة في مصر عبر alidebo.com',
            'meta_desc_en' => 'Discover verified suppliers of agricultural machinery, power generators, energy equipment, and specialized industrial tools on Alidebo B2B Directory.',
        ],
        [
            'code' => 'general_industrial_equipment',
            'slug_part' => 'general-industrial-equipment',
            'icon' => 'cog',
            'name_ar' => 'معدات صناعية عامة',
            'name_en' => 'General Industrial Equipment',
            'name_de' => 'Allgemeine Industrieausrüstung',
            'name_es' => 'Equipos Industriales Generales',
            'name_tr' => 'Genel Endüstriyel Ekipmanlar',
            'name_zh' => '通用工业设备',
            'seo_title_ar' => 'معدات صناعية عامة في مصر | خطوط إنتاج وآلات ومستلزمات - alidebo',
            'seo_title_en' => 'General Industrial Equipment & Machinery Suppliers | Alidebo',
            'meta_desc_ar' => 'دليل شامل لشركات وموردي المعدات والآلات الصناعية العامة وخطوط الإنتاج في مصر عبر alidebo.com',
            'meta_desc_en' => 'Explore manufacturers and suppliers of general industrial machinery, production line systems, and plant equipment on Alidebo B2B Directory.',
        ],
    ];

    public function handle(): int
    {
        $target = $this->option('target');
        $parentMode = $this->option('parent-mode');
        $isDryRun = (bool)$this->option('dry-run');

        $this->info("==================================================================");
        $this->info("  Seed Industrial Machinery Subcategories from Final_2.xlsx");
        $this->info("==================================================================");
        $this->line("Target:       <comment>{$target}</comment>");
        $this->line("Parent Mode:  <comment>{$parentMode}</comment>");
        $this->line("Dry Run:      <comment>" . ($isDryRun ? 'YES (No DB Writes)' : 'NO (Applying Changes)') . "</comment>");
        $this->newLine();

        if ($target === 'both' || $target === 'new_categories') {
            $this->seedNewCategories($parentMode, $isDryRun);
        }

        if ($target === 'both' || $target === 'product_categories') {
            $this->seedProductCategories($isDryRun);
        }

        $this->newLine();
        $this->info("Operation completed successfully!");
        return Command::SUCCESS;
    }

    private function seedNewCategories(string $parentMode, bool $isDryRun): void
    {
        $this->info(">>> Processing [new_categories] table...");

        $now = now();
        $parentCategory = null;

        if ($parentMode === 'l1-new') {
            // Check if L1 category exists or create it
            $parentCategory = NewCategory::where('name_ar', 'الآلات والمعدات الصناعية')
                ->orWhere('slug', 'industrial-machinery-and-equipment')
                ->first();

            if (!$parentCategory) {
                $maxL1Id = NewCategory::where('level', 1)->max('id') ?? 25;
                $newL1Id = max(26, $maxL1Id + 1);

                $parentData = [
                    'id' => $newL1Id,
                    'parent_id' => null,
                    'level' => 1,
                    'name_ar' => 'الآلات والمعدات الصناعية',
                    'name_en' => 'Industrial Machinery & Equipment',
                    'name_de' => 'Industriemaschinen & Ausrüstung',
                    'name_es' => 'Maquinaria y Equipos Industriales',
                    'name_tr' => 'Endüstriyel Makineler ve Ekipmanlar',
                    'name_zh' => '工业机械与设备',
                    'slug' => 'industrial-machinery-and-equipment',
                    'sort_order' => $newL1Id,
                    'status' => 'active',
                    'icon' => 'cog',
                    'disk' => 'public',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if ($isDryRun) {
                    $this->line("  [DRY RUN] Would create L1 Category ID {$newL1Id}: 'الآلات والمعدات الصناعية'");
                } else {
                    DB::table('new_categories')->insert($parentData);
                    $this->line("  [CREATED] L1 Category ID {$newL1Id}: 'الآلات والمعدات الصناعية'");

                    // SEO metadata for L1
                    $this->upsertSeoMetadata(
                        $newL1Id,
                        'الآلات والمعدات الصناعية في مصر | دليل الشركات والموردين - alidebo',
                        'Industrial Machinery & Equipment in Egypt | Alidebo B2B',
                        'تصفح دليل الآلات والمعدات الصناعية في مصر: خطوط إنتاج، مضخات، ضواغط، تكييف وتبريد عبر alidebo.com',
                        'Explore verified industrial machinery and equipment manufacturers and suppliers in Egypt on Alidebo.',
                        $now
                    );
                }
                $parentCategory = (object)$parentData;
            } else {
                $this->line("  [FOUND] Existing L1 Category ID {$parentCategory->id}: '{$parentCategory->name_ar}'");
            }

            $parentId = $parentCategory->id;
            $parentSlug = $parentCategory->slug;
            $baseChildId = $parentId * 100;
        } else {
            // Default: Attach under existing L1 ID 17 ('الصناعة والتصنيع' / Industry & Manufacturing)
            $parentId = 17;
            $parentCategory = NewCategory::find($parentId);
            $parentSlug = $parentCategory ? $parentCategory->slug : 'industry-and-manufacturing';
            $this->line("  [ATTACHING] Subcategories to canonical L1 ID {$parentId}: '{$parentCategory->name_ar}' ({$parentCategory->name_en})");

            // Base child ID: existing max ID under 17
            $maxExistingChildId = NewCategory::where('parent_id', $parentId)->max('id') ?? 1708;
            $baseChildId = $maxExistingChildId;
        }

        $idx = 0;
        foreach (self::CATEGORIES as $cat) {
            $idx++;
            $childId = ($parentMode === 'l1-new') ? ($baseChildId + $idx) : ($baseChildId + $idx);
            $fullSlug = "{$parentSlug}/{$cat['slug_part']}";

            // Check if already exists by name_ar under this parent or anywhere
            $existing = NewCategory::where('name_ar', $cat['name_ar'])->first();

            $categoryData = [
                'parent_id'  => $parentId,
                'level'      => 2,
                'name_ar'    => $cat['name_ar'],
                'name_en'    => $cat['name_en'],
                'name_de'    => $cat['name_de'],
                'name_es'    => $cat['name_es'],
                'name_tr'    => $cat['name_tr'],
                'name_zh'    => $cat['name_zh'],
                'slug'       => $fullSlug,
                'sort_order' => $idx + ($parentMode === 'l1-17' ? 8 : 0),
                'status'     => 'active',
                'icon'       => $cat['icon'],
                'disk'       => 'public',
                'updated_at' => $now,
            ];

            if ($existing) {
                if ($isDryRun) {
                    $this->line("  [DRY RUN] Would update existing Category ID {$existing->id}: '{$cat['name_ar']}'");
                } else {
                    DB::table('new_categories')->where('id', $existing->id)->update($categoryData);
                    $this->line("  [UPDATED] Existing Category ID {$existing->id}: '{$cat['name_ar']}'");
                    $this->upsertSeoMetadata($existing->id, $cat['seo_title_ar'], $cat['seo_title_en'], $cat['meta_desc_ar'], $cat['meta_desc_en'], $now);
                }
            } else {
                if ($isDryRun) {
                    $this->line("  [DRY RUN] Would insert Category ID {$childId}: '{$cat['name_ar']}' ({$cat['name_en']})");
                } else {
                    $categoryData['id'] = $childId;
                    $categoryData['created_at'] = $now;
                    DB::table('new_categories')->insert($categoryData);
                    $this->line("  [INSERTED] Category ID {$childId}: '{$cat['name_ar']}' ({$cat['name_en']})");
                    $this->upsertSeoMetadata($childId, $cat['seo_title_ar'], $cat['seo_title_en'], $cat['meta_desc_ar'], $cat['meta_desc_en'], $now);
                }
            }
        }
    }

    private function seedProductCategories(bool $isDryRun): void
    {
        $this->info(">>> Processing [product_categories] table...");

        $now = now();
        // Main industrial category in product_categories is ID 2: 'آلات ومعدات وقطع غيار صناعية'
        $parentId = 2;
        $parent = ProductCategory::find($parentId);

        if (!$parent) {
            $this->warn("  ProductCategory ID {$parentId} not found. Skipping product_categories.");
            return;
        }

        $this->line("  [PARENT] ProductCategory ID {$parentId}: '{$parent->name_ar}' ({$parent->name_en})");

        $idx = 0;
        foreach (self::CATEGORIES as $cat) {
            $idx++;
            $childSlug = "{$parent->slug}/{$cat['slug_part']}";

            $existing = ProductCategory::where('name_ar', $cat['name_ar'])
                ->orWhere('slug', $childSlug)
                ->first();

            $data = [
                'name_ar'    => $cat['name_ar'],
                'name_en'    => $cat['name_en'],
                'name_de'    => $cat['name_de'],
                'name_es'    => $cat['name_es'],
                'name_tr'    => $cat['name_tr'],
                'name_zh'    => $cat['name_zh'],
                'slug'       => $childSlug,
                'parent_id'  => $parentId,
                'icon'       => $cat['icon'],
                'order'      => $idx,
                'status'     => 'active',
                'disk'       => 'public',
                'updated_at' => $now,
            ];

            if ($existing) {
                if ($isDryRun) {
                    $this->line("  [DRY RUN] Would update existing ProductCategory ID {$existing->id}: '{$cat['name_ar']}'");
                } else {
                    $existing->update($data);
                    $this->line("  [UPDATED] Existing ProductCategory ID {$existing->id}: '{$cat['name_ar']}'");
                }
            } else {
                if ($isDryRun) {
                    $this->line("  [DRY RUN] Would insert ProductCategory child: '{$cat['name_ar']}' ({$cat['name_en']})");
                } else {
                    $data['created_at'] = $now;
                    $created = ProductCategory::create($data);
                    $this->line("  [INSERTED] ProductCategory ID {$created->id}: '{$cat['name_ar']}' ({$cat['name_en']})");
                }
            }
        }
    }

    private function upsertSeoMetadata(int $id, string $titleAr, string $titleEn, string $descAr, string $descEn, $now): void
    {
        $metaTitle = [
            'ar' => $titleAr,
            'en' => $titleEn,
        ];
        $metaDesc = [
            'ar' => $descAr,
            'en' => $descEn,
        ];

        DB::table('seo_metadata')->updateOrInsert(
            [
                'seoable_type' => 'category_v2',
                'seoable_id'   => $id,
            ],
            [
                'meta_title'       => json_encode($metaTitle, JSON_UNESCAPED_UNICODE),
                'meta_description' => json_encode($metaDesc, JSON_UNESCAPED_UNICODE),
                'updated_at'       => $now,
                'created_at'       => $now,
            ]
        );
    }
}
