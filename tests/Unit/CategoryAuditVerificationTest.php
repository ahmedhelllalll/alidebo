<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\NewCategory;
use App\Support\CategoryAliasMapping;
use App\Services\DataNormalizationService;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CategoryAuditVerificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.mysql.database' => 'alidebo_staging']);
        DB::purge('mysql');
        DB::reconnect('mysql');
    }

    /**
     * Test all 20 newly seeded categories exist in the database with parents and translations.
     */
    public function test_newly_seeded_categories_exist_in_database()
    {
        $expectedCategoryIds = [
            // Food & Food Exports
            908, 909, 910, 911, 912, 913, 914, 915, 916, 917, 918, 919,
            // Fashion & Clothing
            1909, 1910, 1911, 1912, 1913, 1914, 1915, 1916,
            // Final_35 Additions
            212, 213, 214, 215, 1733, 1734, 1917, 1918, 2008,
        ];

        foreach ($expectedCategoryIds as $id) {
            $cat = DB::table('new_categories')->where('id', $id)->first();
            $this->assertNotNull($cat, "Category ID {$id} should exist in new_categories table.");
            $this->assertEquals(2, $cat->level, "Category ID {$id} should be Level 2.");
            $this->assertNotEmpty($cat->name_ar, "Category ID {$id} should have Arabic name.");
            $this->assertNotEmpty($cat->name_en, "Category ID {$id} should have English name.");
            $this->assertNotEmpty($cat->name_de, "Category ID {$id} should have German name.");
            $this->assertNotEmpty($cat->name_es, "Category ID {$id} should have Spanish name.");
            $this->assertNotEmpty($cat->name_tr, "Category ID {$id} should have Turkish name.");
            $this->assertNotEmpty($cat->name_zh, "Category ID {$id} should have Chinese name.");
            $this->assertNotEmpty($cat->slug, "Category ID {$id} should have slug.");

            // Check SEO metadata
            $seo = DB::table('seo_metadata')
                ->where('seoable_type', 'category_v2')
                ->where('seoable_id', $id)
                ->first();
            $this->assertNotNull($seo, "SEO metadata for category ID {$id} should exist.");
        }
    }

    /**
     * Test that all 28 files in data/ directory achieve 100% category match rate.
     */
    public function test_data_sheets_achieve_100_percent_category_match()
    {
        $dataDir = base_path('data');
        $files = scandir($dataDir);
        $normalizer = new DataNormalizationService();
        $newCats = NewCategory::all()->keyBy('id');

        $totalRows = 0;
        $matchedRows = 0;

        foreach ($files as $file) {
            if (!preg_match('/\.(xlsx|csv)$/i', $file)) continue;

            $filePath = $dataDir . '/' . $file;
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (empty($rows)) continue;

            $headers = array_map(function($h) { return trim(strtolower((string)$h)); }, $rows[0]);
            $headerMap = array_flip($headers);

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (!array_filter($row, function($c) { return $c !== null && trim((string)$c) !== ''; })) {
                    continue;
                }

                $totalRows++;
                $mainCat = isset($headerMap['main_category']) ? trim((string)$row[$headerMap['main_category']]) : '';
                $subCat = isset($headerMap['sub_category']) ? trim((string)$row[$headerMap['sub_category']]) : '';

                $resolved = null;
                if ($subCat !== '') {
                    $alias = CategoryAliasMapping::resolve($subCat);
                    if ($alias && !empty($alias['new_category_id'])) {
                        $resolved = $newCats->get($alias['new_category_id']);
                    }
                }
                if (!$resolved && $mainCat !== '') {
                    $alias = CategoryAliasMapping::resolve($mainCat);
                    if ($alias && !empty($alias['new_category_id'])) {
                        $resolved = $newCats->get($alias['new_category_id']);
                    }
                }

                if ($resolved) {
                    $matchedRows++;
                }
            }
        }

        $this->assertEquals(344, $totalRows, "Total rows across all 28 files should be 344.");
        $this->assertEquals($totalRows, $matchedRows, "All rows must match to categories (100% match rate).");
    }
    /**
     * Test that Final_35 sheet achieves 100% category match rate.
     */
    public function test_final_35_sheet_achieves_100_percent_category_match()
    {
        $filePath = base_path('Final_35 إضافة التعديلات.xlsx');
        $this->assertFileExists($filePath);

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $this->assertNotEmpty($rows);

        $headers = array_map(function($h) { return trim((string)$h); }, $rows[0]);
        $mainCol = array_search('الفئة الرئيسية', $headers);
        $subCol = array_search('الفئة الفرعية', $headers);

        $this->assertNotFalse($mainCol, 'Main category column must exist');
        $this->assertNotFalse($subCol, 'Sub category column must exist');

        $totalRows = 0;
        $matchedRows = 0;

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $mainCat = trim((string)($row[$mainCol] ?? ''));
            $subCat = trim((string)($row[$subCol] ?? ''));

            if ($mainCat === '' && $subCat === '') continue;

            $totalRows++;
            $resolved = null;

            if ($subCat !== '') {
                $alias = CategoryAliasMapping::resolve($subCat);
                if ($alias && !empty($alias['new_category_id'])) {
                    $resolved = NewCategory::find($alias['new_category_id']);
                }
            }

            if (!$resolved && $mainCat !== '') {
                $alias = CategoryAliasMapping::resolve($mainCat);
                if ($alias && !empty($alias['new_category_id'])) {
                    $resolved = NewCategory::find($alias['new_category_id']);
                }
            }

            if ($resolved) {
                $matchedRows++;
            }
        }

        $this->assertEquals(9, $totalRows, "Total rows in Final_35 sheet should be 9.");
        $this->assertEquals($totalRows, $matchedRows, "All rows in Final_35 must achieve a 100% match rate.");
    }
}
