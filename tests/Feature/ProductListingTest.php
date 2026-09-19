<?php

namespace Tests\Feature;

use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProductListingTest extends TestCase
{
    protected function setupVendorWithProducts(): array
    {
        Queue::fake();

        $user = User::factory()->create(['role' => 'user']);
        $user->markEmailAsVerified();

        $country = Country::first() ?? Country::create(['code' => 'SA', 'phone_code' => '+966', 'name_en' => 'Saudi Arabia', 'name_ar' => 'المملكة العربية السعودية']);
        $city = City::first() ?? City::create(['country_id' => $country->id, 'name_en' => 'Riyadh', 'name_ar' => 'الرياض']);

        $categoryA = Category::first() ?? Category::create([
            'name_en' => 'Electronics Business',
            'name_ar' => 'إلكترونيات أعمال',
            'slug' => 'electronics-biz-' . uniqid(),
        ]);

        $productCategoryA = ProductCategory::first() ?? ProductCategory::create([
            'name_en' => 'Electronics',
            'name_ar' => 'إلكترونيات',
            'slug' => 'electronics-' . uniqid(),
        ]);

        $productCategoryB = ProductCategory::skip(1)->first() ?? ProductCategory::create([
            'name_en' => 'Apparel',
            'name_ar' => 'أزياء',
            'slug' => 'apparel-' . uniqid(),
        ]);

        $business = BusinessProfile::create([
            'user_id' => $user->id,
            'owner_id' => $user->id,
            'category_id' => $categoryA->id,
            'city_id' => $city->id,
            'name' => 'Alidebo Test Vendor ' . uniqid(),
            'slug' => 'alidebo-test-vendor-' . uniqid(),
            'status' => 'approved',
        ]);

        $productApproved = Product::create([
            'business_profile_id' => $business->id,
            'category_id' => $productCategoryA->id,
            'sku' => 'SKU-APP-' . uniqid(),
            'unit' => 'piece',
            'moq' => 10,
            'base_price' => 99.50,
            'status' => 'approved',
        ]);
        $productApproved->translations()->create(['locale' => 'en', 'name' => 'Approved Smart Watch', 'short_description' => 'A great watch']);
        $productApproved->translations()->create(['locale' => 'ar', 'name' => 'ساعة ذكية معتمدة', 'short_description' => 'ساعة ممتازة']);

        $productPending = Product::create([
            'business_profile_id' => $business->id,
            'category_id' => $productCategoryB->id,
            'sku' => 'SKU-PEN-' . uniqid(),
            'unit' => 'carton',
            'moq' => 20,
            'base_price' => 45.00,
            'status' => 'pending',
        ]);
        $productPending->translations()->create(['locale' => 'en', 'name' => 'Pending Casual T-Shirt', 'short_description' => 'Casual shirt']);
        $productPending->translations()->create(['locale' => 'ar', 'name' => 'قميص غير رسمي قيد المراجعة', 'short_description' => 'قميص رائع']);

        $productRejected = Product::create([
            'business_profile_id' => $business->id,
            'category_id' => $productCategoryA->id,
            'sku' => 'SKU-REJ-' . uniqid(),
            'unit' => 'piece',
            'moq' => 5,
            'base_price' => 15.00,
            'status' => 'rejected',
            'rejection_reason' => 'Image resolution too low and invalid specifications.',
        ]);
        $productRejected->translations()->create(['locale' => 'en', 'name' => 'Rejected Wireless Earbuds', 'short_description' => 'Earbuds']);
        $productRejected->translations()->create(['locale' => 'ar', 'name' => 'سماعات أذن مرفوضة', 'short_description' => 'سماعات']);

        return [$user, $business, $productCategoryA, $productCategoryB, $productApproved, $productPending, $productRejected];
    }

    public function test_vendor_can_view_my_products_page_in_arabic_with_all_elements()
    {
        [$user, $business, $categoryA, $categoryB, $approved, $pending, $rejected] = $this->setupVendorWithProducts();

        $response = $this->actingAs($user)
            ->withSession(['locale' => 'ar'])
            ->get(route('products.index'));

        $response->assertStatus(200);
        $content = $response->getContent();

        // 1. Header & Localized Title
        $this->assertStringContainsString('منتجاتي', $content);
        $this->assertStringContainsString('إدارة وتتبع قائمة منتجاتك', $content);
        $this->assertStringContainsString(route('products.create'), $content);

        // 2. View Mode Toggle Controls
        $this->assertStringContainsString("localStorage.getItem('alidebo_product_view')", $content);
        $this->assertStringContainsString('عرض شبكي', $content);
        $this->assertStringContainsString('عرض القائمة', $content);

        // 3. Grid & Row Views Containers
        $this->assertStringContainsString("x-show=\"viewMode === 'grid'\"", $content);
        $this->assertStringContainsString("x-show=\"viewMode === 'list'\"", $content);

        // 4. Status Badges & Rejection Reason
        $this->assertStringContainsString('معتمد', $content);
        $this->assertStringContainsString('قيد المراجعة', $content);
        $this->assertStringContainsString('مرفوض', $content);
        $this->assertStringContainsString('سبب الرفض:', $content);
        $this->assertStringContainsString($rejected->rejection_reason, $content);

        // 5. Actions
        $this->assertStringContainsString(route('products.edit', $approved->id), $content);
        $this->assertStringContainsString('تعديل', $content);
        $this->assertStringContainsString('حذف', $content);
        $this->assertStringContainsString('حذف المنتج؟', $content);

        // 6. Filter Sidebar
        $this->assertStringContainsString('تصفية المنتجات', $content);
        $this->assertStringContainsString('البحث بالاسم أو رمز SKU...', $content);
        $this->assertStringContainsString('جميع التصنيفات', $content);
        $this->assertStringContainsString('تطبيق الفلترة', $content);
    }

    public function test_vendor_can_view_my_products_page_in_english()
    {
        [$user, $business, $categoryA, $categoryB, $approved, $pending, $rejected] = $this->setupVendorWithProducts();

        $response = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get(route('products.index'));

        $response->assertStatus(200);
        $content = $response->getContent();

        $this->assertStringContainsString('My Products', $content);
        $this->assertStringContainsString('Grid View', $content);
        $this->assertStringContainsString('List View', $content);
        $this->assertStringContainsString('Approved', $content);
        $this->assertStringContainsString('Pending Review', $content);
        $this->assertStringContainsString('Rejected', $content);
        $this->assertStringContainsString('Filter Products', $content);
        $this->assertStringContainsString('Delete Product?', $content);
    }

    public function test_filter_by_status_approved()
    {
        [$user, $business, $categoryA, $categoryB, $approved, $pending, $rejected] = $this->setupVendorWithProducts();

        $response = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get(route('products.index', ['status' => 'approved']));

        $response->assertStatus(200);
        $products = $response->viewData('products');
        
        $this->assertGreaterThan(0, $products->count());
        foreach ($products as $p) {
            $this->assertEquals('approved', $p->status);
        }
    }

    public function test_filter_by_search_query()
    {
        [$user, $business, $categoryA, $categoryB, $approved, $pending, $rejected] = $this->setupVendorWithProducts();

        $response = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get(route('products.index', ['search' => $approved->sku]));

        $response->assertStatus(200);
        $products = $response->viewData('products');
        
        $this->assertGreaterThanOrEqual(1, $products->count());
        $this->assertEquals($approved->id, $products->first()->id);
    }

    public function test_filter_by_category()
    {
        [$user, $business, $categoryA, $categoryB, $approved, $pending, $rejected] = $this->setupVendorWithProducts();

        $response = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get(route('products.index', ['category_id' => $categoryA->id]));

        $response->assertStatus(200);
        $products = $response->viewData('products');

        foreach ($products as $p) {
            $this->assertEquals($categoryA->id, $p->category_id);
        }
    }

    public function test_empty_state_when_filters_yield_no_results()
    {
        [$user, $business] = $this->setupVendorWithProducts();

        $response = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get(route('products.index', ['search' => 'NON_EXISTENT_SKU_xyz_99999']));

        $response->assertStatus(200);
        $content = $response->getContent();

        $this->assertStringContainsString('No products match your filter criteria', $content);
        $this->assertStringContainsString('Reset Filters', $content);
    }

    public function test_api_json_response_preserved_for_products_index()
    {
        [$user, $business] = $this->setupVendorWithProducts();

        $response = $this->actingAs($user)
            ->getJson(route('products.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'products' => [
                'data',
                'current_page',
                'total'
            ]
        ]);
    }

    public function test_vendor_can_delete_own_product()
    {
        [$user, $business, $categoryA] = $this->setupVendorWithProducts();

        $tempProduct = Product::create([
            'business_profile_id' => $business->id,
            'category_id' => $categoryA->id,
            'sku' => 'SKU-DEL-' . uniqid(),
            'unit' => 'piece',
            'moq' => 1,
            'base_price' => 10.0,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)
            ->deleteJson(route('products.destroy', $tempProduct->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        $this->assertSoftDeleted('products', ['id' => $tempProduct->id]);
    }

    public function test_card_view_badges_support_long_category_names_without_overlap_in_both_locales()
    {
        [$user, $business, $categoryA, $categoryB, $approved] = $this->setupVendorWithProducts();

        $longCategory = ProductCategory::create([
            'name_en' => 'Advanced Enterprise Software & Cloud Infrastructure',
            'name_ar' => 'تكنولوجيا وبرمجيات وحلول سحابية متقدمة',
            'slug' => 'tech-soft-' . uniqid(),
        ]);

        $productWithLongCat = Product::create([
            'business_profile_id' => $business->id,
            'category_id' => $longCategory->id,
            'sku' => 'SKU-LONG-CAT',
            'unit' => 'piece',
            'moq' => 1,
            'base_price' => 500.00,
            'status' => 'approved',
        ]);
        $productWithLongCat->translations()->create(['locale' => 'en', 'name' => 'Enterprise Suite', 'short_description' => 'Desc']);
        $productWithLongCat->translations()->create(['locale' => 'ar', 'name' => 'حزمة المؤسسات', 'short_description' => 'وصف']);

        // 1. Test Arabic (RTL)
        $responseAr = $this->actingAs($user)
            ->withSession(['locale' => 'ar'])
            ->get(route('products.index', ['category_id' => $longCategory->id]));

        $responseAr->assertStatus(200);
        $contentAr = $responseAr->getContent();

        // Confirm category name is present in Arabic
        $this->assertStringContainsString('تكنولوجيا وبرمجيات', $contentAr);
        // Confirm truncation and layout classes are in the card view top badges bar
        $this->assertStringContainsString('absolute top-3 inset-x-3 z-10 flex items-center justify-between gap-2 pointer-events-none', $contentAr);
        $this->assertStringContainsString('pointer-events-auto min-w-0 max-w-[55%]', $contentAr);
        $this->assertStringContainsString('truncate max-w-full', $contentAr);
        $this->assertStringContainsString('pointer-events-auto shrink-0', $contentAr);

        // 2. Test English (LTR)
        $responseEn = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get(route('products.index', ['category_id' => $longCategory->id]));

        $responseEn->assertStatus(200);
        $contentEn = $responseEn->getContent();

        // Confirm category name is present in English
        $this->assertStringContainsString('Advanced Enterprise Software', $contentEn);
        // Confirm same non-overlapping structural classes are present in English
        $this->assertStringContainsString('pointer-events-auto min-w-0 max-w-[55%]', $contentEn);
        $this->assertStringContainsString('truncate max-w-full', $contentEn);
        $this->assertStringContainsString('pointer-events-auto shrink-0', $contentEn);
    }

    public function test_action_buttons_and_moq_unit_localization_in_english_and_arabic()
    {
        [$user, $business, $categoryA] = $this->setupVendorWithProducts();

        // Product with Arabic unit saved in DB
        $arabicUnitProduct = Product::create([
            'business_profile_id' => $business->id,
            'category_id' => $categoryA->id,
            'sku' => 'SKU-UNIT-AR-' . uniqid(),
            'unit' => 'قطعة',
            'moq' => 50,
            'base_price' => 120.00,
            'status' => 'approved',
        ]);
        $arabicUnitProduct->translations()->create(['locale' => 'en', 'name' => 'Arabic Unit Product', 'short_description' => 'Test']);
        $arabicUnitProduct->translations()->create(['locale' => 'ar', 'name' => 'منتج بوحدة عربية', 'short_description' => 'تجربة']);

        // 1. In English view: Unit must be translated to "piece", not leak "قطعة"
        $responseEn = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get(route('products.index', ['search' => 'SKU-UNIT-AR-']));

        $responseEn->assertStatus(200);
        $contentEn = $responseEn->getContent();

        // Check English unit next to price and MOQ
        $this->assertStringContainsString('/ piece', $contentEn);
        $this->assertStringContainsString('Min. Order: 50', $contentEn);
        $this->assertStringContainsString('>piece</span>', $contentEn);
        $this->assertStringNotContainsString('>قطعة</span>', $contentEn);
        $this->assertStringNotContainsString('>قطعه</span>', $contentEn);

        // Check action button group polish in English view (h-9 height, consistent radius, self-center)
        $this->assertStringContainsString('h-9 px-3.5 rounded-xl bg-primary', $contentEn);
        $this->assertStringContainsString('w-9 h-9 rounded-xl', $contentEn);
        $this->assertStringContainsString('self-center', $contentEn);

        // 2. In Arabic view: Unit must be "قطعة"
        $responseAr = $this->actingAs($user)
            ->withSession(['locale' => 'ar'])
            ->get(route('products.index', ['search' => 'SKU-UNIT-AR-']));

        $responseAr->assertStatus(200);
        $contentAr = $responseAr->getContent();

        $this->assertStringContainsString('/ قطعة', $contentAr);
        $this->assertStringContainsString('>قطعة</span>', $contentAr);
    }

    public function test_sidebar_filter_dropdowns_use_custom_alpine_component_and_hidden_inputs()
    {
        [$user, $business, $categoryA, $categoryB] = $this->setupVendorWithProducts();

        $response = $this->actingAs($user)
            ->withSession(['locale' => 'ar'])
            ->get(route('products.index', ['category_id' => $categoryA->id, 'sort' => 'price_asc']));

        $response->assertStatus(200);
        $content = $response->getContent();

        // 1. Zero native selects should remain on the page
        $this->assertStringNotContainsString('<select', $content);

        // 2. Hidden inputs must exist for form submission with matching values
        $this->assertStringContainsString('name="category_id"', $content);
        $this->assertStringContainsString('name="sort"', $content);
        $this->assertStringContainsString('<input type="hidden" name="category_id"', $content);
        $this->assertStringContainsString('<input type="hidden" name="sort"', $content);

        // 3. Custom Alpine components and smart dropUp positioning must be present
        $this->assertStringContainsString('x-ref="catDropdownBtn"', $content);
        $this->assertStringContainsString('x-ref="sortDropdownBtn"', $content);
        $this->assertStringContainsString('dropUp ? \'bottom-full mb-2\' : \'top-full mt-2\'', $content);

        // 4. Products correctly filtered by category
        $products = $response->viewData('products');
        foreach ($products as $p) {
            $this->assertEquals($categoryA->id, $p->category_id);
        }
    }

    public function test_products_index_renders_onboarding_tour_and_retrigger_button()
    {
        [$user, $business] = $this->setupVendorWithProducts();

        // 1. Arabic View
        $responseAr = $this->actingAs($user)
            ->withSession(['locale' => 'ar'])
            ->get(route('products.index'));

        $responseAr->assertStatus(200);
        $contentAr = $responseAr->getContent();

        // Validate JSON and JS syntax in Arabic
        $this->assertTourConfigIsValid($contentAr, 'index');

        // Re-trigger button
        $this->assertStringContainsString('دليل البدء', $contentAr);
        $this->assertStringContainsString('$dispatch(\'open-products-tour\')', $contentAr);

        // Target IDs present in DOM (both card view and row view quick actions)
        $this->assertStringContainsString('id="tour-add-product-btn"', $contentAr);
        $this->assertStringContainsString('id="tour-view-switcher"', $contentAr);
        $this->assertStringContainsString('id="tour-filters-sidebar"', $contentAr);
        $this->assertStringContainsString('id="tour-status-badges"', $contentAr);
        $this->assertStringContainsString('id="tour-quick-actions"', $contentAr);
        $this->assertStringContainsString('id="tour-quick-actions-row"', $contentAr);

        // 2. English View
        $responseEn = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get(route('products.index'));

        $responseEn->assertStatus(200);
        $contentEn = $responseEn->getContent();

        // Validate JSON and JS syntax in English
        $this->assertTourConfigIsValid($contentEn, 'index');
        $this->assertStringContainsString('Product Guide', $contentEn);

        // 3. Spotlight Cutout, Solid Badge & Smooth Animation Assertions
        $this->assertStringContainsString('id="tour-spotlight-mask"', $contentEn);
        $this->assertStringContainsString('mask="url(#tour-spotlight-mask)"', $contentEn);
        $this->assertStringContainsString('.tour-spotlight-active', $contentEn);
        $this->assertStringContainsString('w-7 h-7 rounded-full bg-primary text-white', $contentEn);
        $this->assertStringContainsString('tour-popover-card', $contentEn);
        $this->assertStringContainsString('tour-spotlight-frame', $contentEn);
        $this->assertStringContainsString('tour-spotlight-mask-rect', $contentEn);

        // 4. Sticky Header Offset & Scroll Alignment Assertions
        $this->assertStringContainsString('scroll-margin-top: 100px', $contentEn);
        $this->assertStringContainsString('scrollToTarget(', $contentEn);
        $this->assertStringContainsString('getHeaderHeight()', $contentEn);
        $this->assertStringContainsString('getScrollContainer()', $contentEn);
    }

    public function test_products_tour_seen_state_persists_to_database_and_reflects_in_tour_config(): void
    {
        [$user, $business] = $this->setupVendorWithProducts();
        $this->assertNull($user->products_tour_seen_at);

        // 1. When products_tour_seen_at is null, tourConfig hasSeenServer is false
        $response = $this->actingAs($user)->get(route('products.index'));
        $response->assertStatus(200);
        $content = $response->getContent();
        preg_match('/x-data="productsOnboardingTour\(JSON\.parse\(\'(.*?)\'\)\)"/s', $content, $matches);
        $this->assertNotEmpty($matches[1]);
        $rawJson = str_replace('\u0022', '"', $matches[1]);
        $decoded = json_decode($rawJson, true);
        $this->assertFalse($decoded['hasSeenServer']);
        $this->assertSame(route('products.tour.seen', [], false), str_replace('\\/', '/', $decoded['seenUrl']));

        // 2. Call API endpoint to mark tour as seen
        $seenResponse = $this->actingAs($user)->postJson(route('products.tour.seen'));
        $seenResponse->assertStatus(200)->assertJson(['success' => true]);

        // 3. User in database has products_tour_seen_at set
        $user->refresh();
        $this->assertNotNull($user->products_tour_seen_at);

        // 4. On next page load, tourConfig hasSeenServer is true
        $responseSeen = $this->actingAs($user)->get(route('products.index'));
        $responseSeen->assertStatus(200);
        $contentSeen = $responseSeen->getContent();
        preg_match('/x-data="productsOnboardingTour\(JSON\.parse\(\'(.*?)\'\)\)"/s', $contentSeen, $matchesSeen);
        $rawJsonSeen = str_replace('\u0022', '"', $matchesSeen[1]);
        $decodedSeen = json_decode($rawJsonSeen, true);
        $this->assertTrue($decodedSeen['hasSeenServer']);
    }

    /**
     * Validates that the tour x-data attribute is syntactically valid and parses correctly.
     */
    protected function assertTourConfigIsValid(string $html, string $expectedPage): void
    {
        $this->assertMatchesRegularExpression(
            '/x-data="productsOnboardingTour\(JSON\.parse\(\'(.*?)\'\)\)"/s',
            $html,
            'The tour x-data attribute must use safely-encoded JSON.parse()'
        );

        preg_match('/x-data="productsOnboardingTour\(JSON\.parse\(\'(.*?)\'\)\)"/s', $html, $matches);
        $this->assertNotEmpty($matches[1], 'Tour JSON payload cannot be empty');

        $rawJson = str_replace('\u0022', '"', $matches[1]);
        $decoded = json_decode($rawJson, true);

        $this->assertSame(
            JSON_ERROR_NONE,
            json_last_error(),
            'Tour JSON must parse without syntax error: ' . json_last_error_msg()
        );

        $this->assertIsArray($decoded);
        $this->assertSame($expectedPage, $decoded['page']);
        $this->assertIsInt($decoded['userId']);
        $this->assertIsBool($decoded['isRtl']);
        $this->assertIsArray($decoded['labels']);
        $this->assertNotEmpty($decoded['labels']['finish']);
        $this->assertNotEmpty($decoded['labels']['next']);
        $this->assertNotEmpty($decoded['labels']['back']);
        $this->assertNotEmpty($decoded['labels']['skip']);

        $this->assertIsArray($decoded['steps']);
        $this->assertGreaterThanOrEqual(4, count($decoded['steps']));

        foreach ($decoded['steps'] as $index => $step) {
            $this->assertArrayHasKey('targetId', $step, "Step #$index must have 'targetId' property (not lowercase 'targetid')");
            $this->assertArrayNotHasKey('targetid', $step, "Step #$index must NOT have lowercase 'targetid'");
            $this->assertArrayHasKey('title', $step, "Step #$index must have 'title'");
            $this->assertArrayHasKey('desc', $step, "Step #$index must have 'desc'");
            $this->assertArrayHasKey('icon', $step, "Step #$index must have 'icon'");
            $this->assertArrayHasKey('placement', $step, "Step #$index must have 'placement'");

            $this->assertStringStartsWith('#tour-', $step['targetId']);
            $this->assertNotEmpty($step['title']);
            $this->assertNotEmpty($step['desc']);
            $this->assertNotEmpty($step['icon']);
            $this->assertContains($step['placement'], ['top', 'bottom', 'side', 'auto']);
        }
    }

    public function test_products_listing_renders_package_details_for_bulk_units()
    {
        [$user, $business, $category] = $this->setupVendorWithProducts();

        $cartonProduct = Product::create([
            'business_profile_id' => $business->id,
            'category_id' => $category->id,
            'sku' => 'SKU-CARTON-TEST',
            'unit' => 'carton',
            'units_per_package' => 48,
            'moq' => 2,
            'base_price' => 120.0,
            'currency' => 'EGP',
            'status' => 'pending',
        ]);
        $cartonProduct->translations()->create([
            'locale' => 'ar',
            'name' => 'منتج كرتونة تجريبي'
        ]);
        $cartonProduct->translations()->create([
            'locale' => 'en',
            'name' => 'Carton Test Product'
        ]);

        $responseAr = $this->actingAs($user)
            ->withSession(['locale' => 'ar'])
            ->get(route('products.index'));
        $responseAr->assertStatus(200);
        $responseAr->assertSee('(48 قطعة / كرتونة)');

        $responseEn = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get(route('products.index'));
        $responseEn->assertStatus(200);
        $responseEn->assertSee('(48 pcs / carton)');
    }

    public function test_product_detail_modal_and_airbnb_gallery_lightbox_rendered_on_index()
    {
        [$user, $business, $category] = $this->setupVendorWithProducts();

        // 1. English Locale
        $responseEn = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get(route('products.index'));
        $responseEn->assertStatus(200);
        $contentEn = $responseEn->getContent();

        // Modal and Lightbox component presence
        $this->assertStringContainsString('id="product-detail-title"', $contentEn);
        $this->assertStringContainsString('id="product-gallery-lightbox"', $contentEn);
        $this->assertStringContainsString('photoGalleryLightbox()', $contentEn);
        $this->assertStringContainsString('@open-photo-gallery.window="openGallery($event.detail)"', $contentEn);

        // Airbnb Grid button overlay presence & translations
        $this->assertStringContainsString('Show all', $contentEn);
        $this->assertStringContainsString('photos', $contentEn);
        $this->assertStringContainsString('launchGalleryFromModal', $contentEn);
        $this->assertStringContainsString('openProductDetail(', $contentEn);

        // 2. Arabic Locale
        $responseAr = $this->actingAs($user)
            ->withSession(['locale' => 'ar'])
            ->get(route('products.index'));
        $responseAr->assertStatus(200);
        $contentAr = $responseAr->getContent();

        $this->assertStringContainsString('عرض جميع الصور', $contentAr);
        $this->assertStringContainsString('تفاصيل المنتج', $contentAr);
        $this->assertStringContainsString('معرض الصور', $contentAr);
    }

    public function test_gallery_lightbox_supports_masonry_grid_and_carousel_layouts()
    {
        [$user, $business, $category] = $this->setupVendorWithProducts();

        $response = $this->actingAs($user)->get(route('products.index'));
        $response->assertStatus(200);
        $content = $response->getContent();

        // 4+ Masonry Grid conditional markup
        $this->assertStringContainsString('images.length >= 4 && currentView === \'grid\'', $content);
        $this->assertStringContainsString('grid grid-cols-1 sm:grid-cols-2', $content);

        // < 4 Centered Carousel conditional markup
        $this->assertStringContainsString('images.length < 4 || currentView === \'focus\'', $content);
        $this->assertStringContainsString('prevImage()', $content);
        $this->assertStringContainsString('nextImage()', $content);

        // Shared-element morph layer
        $this->assertStringContainsString('isMorphing && morphStyles.width', $content);

        // Dismissal & keyboard navigation
        $this->assertStringContainsString('@keydown.escape.window="handleEscape()"', $content);
        $this->assertStringContainsString('closeGallery()', $content);
        $this->assertStringContainsString('handleArrowRight()', $content);
        $this->assertStringContainsString('handleArrowLeft()', $content);
    }

    public function test_product_specifications_and_multi_images_serialized_in_payload()
    {
        [$user, $business, $category] = $this->setupVendorWithProducts();

        $galleryProduct = Product::create([
            'business_profile_id' => $business->id,
            'category_id' => $category->id,
            'sku' => 'SKU-GALLERY-TEST',
            'unit' => 'piece',
            'base_price' => 250.00,
            'currency' => 'USD',
            'status' => 'approved',
        ]);
        $galleryProduct->translations()->create([
            'locale' => 'en',
            'name' => 'Multi Image Premium Device',
            'short_description' => 'Short desc for gallery product',
            'full_description' => 'Comprehensive detailed specifications for this product.'
        ]);

        // Add 5 images
        for ($i = 1; $i <= 5; $i++) {
            $galleryProduct->images()->create([
                'file_path' => "products/test_photo_{$i}.jpg",
                'is_primary' => ($i === 1),
                'order' => $i
            ]);
        }

        // Add specifications
        $galleryProduct->specifications()->create(['name' => 'Material', 'value' => 'Titanium']);
        $galleryProduct->specifications()->create(['name' => 'Battery', 'value' => '5000 mAh']);

        $response = $this->actingAs($user)->get(route('products.index'));
        $response->assertStatus(200);
        $content = $response->getContent();

        // Verify specifications & 5 images are serialized in Alpine JSON
        $this->assertStringContainsString('Titanium', $content);
        $this->assertStringContainsString('5000 mAh', $content);
        $this->assertStringContainsString('test_photo_1.jpg', $content);
        $this->assertStringContainsString('test_photo_5.jpg', $content);
    }

    public function test_anonymous_visitor_can_view_approved_products_and_airbnb_gallery_on_public_business_profile()
    {
        [$user, $business, $category] = $this->setupVendorWithProducts();
        $slug = 'pub-vendor-' . uniqid();
        $business->update(['status' => 'approved', 'slug' => $slug]);

        // Create approved product
        $approvedProd = Product::create([
            'business_profile_id' => $business->id,
            'category_id' => $category->id,
            'sku' => 'SKU-PUB-' . uniqid(),
            'unit' => 'piece',
            'base_price' => 120.00,
            'currency' => 'USD',
            'status' => 'approved',
        ]);
        $approvedProd->translations()->create([
            'locale' => 'en',
            'name' => 'Public Approved Solar Inverter',
            'short_description' => 'Industrial grade solar inverter system',
        ]);
        $approvedProd->images()->create(['file_path' => 'products/solar1.jpg', 'is_primary' => true, 'order' => 1]);
        $approvedProd->images()->create(['file_path' => 'products/solar2.jpg', 'is_primary' => false, 'order' => 2]);

        // Create pending product (should NOT be visible to public)
        $pendingProd = Product::create([
            'business_profile_id' => $business->id,
            'category_id' => $category->id,
            'sku' => 'SKU-HIDDEN-' . uniqid(),
            'unit' => 'piece',
            'base_price' => 99.00,
            'currency' => 'USD',
            'status' => 'pending',
        ]);
        $pendingProd->translations()->create([
            'locale' => 'en',
            'name' => 'Secret Unapproved Prototype',
            'short_description' => 'Should not leak to public buyers',
        ]);

        // 1. Visit as unauthenticated anonymous visitor in English
        $response = $this->get('/en/directory/' . $slug);
        $response->assertStatus(200);
        $content = $response->getContent();

        // Must show approved product
        $this->assertStringContainsString('Public Approved Solar Inverter', $content);
        $this->assertStringContainsString('id="products"', $content);
        $this->assertStringContainsString('id="product-' . $approvedProd->id . '"', $content);

        // Must NOT show pending product to anonymous visitors
        $this->assertStringNotContainsString('Secret Unapproved Prototype', $content);

        // Public page must include detail modal and reusable gallery lightbox
        $this->assertStringContainsString('id="public-product-gallery"', $content);
        $this->assertStringContainsString('photoGalleryLightbox()', $content);
        $this->assertStringContainsString('id="product-detail-title"', $content);
        $this->assertStringContainsString('Show all photos', $content);

        // Modal on public page must render Coming Soon quote CTA, NOT edit action
        $this->assertStringContainsString('Coming Soon', $content);

        // 2. Arabic visit as anonymous visitor
        $responseAr = $this->get('/ar/directory/' . $slug);
        $responseAr->assertStatus(200);
        $contentAr = $responseAr->getContent();
        $this->assertStringContainsString('المنتجات والكتالوج', $contentAr);
        $this->assertStringContainsString('عرض جميع الصور', $contentAr);
    }

    public function test_unapproved_business_profile_hidden_from_anonymous_visitors()
    {
        [$user, $business] = $this->setupVendorWithProducts();
        $pendingSlug = 'pending-vendor-' . uniqid();
        $business->update(['status' => 'pending', 'slug' => $pendingSlug]);

        // Anonymous visitor receives 404
        $response = $this->get('/en/directory/' . $pendingSlug);
        $response->assertStatus(404);
    }

    public function test_vendor_dashboard_eye_button_links_to_public_business_listing()
    {
        [$user, $business] = $this->setupVendorWithProducts();
        $activeSlug = 'active-vendor-' . uniqid();
        $business->update(['status' => 'approved', 'slug' => $activeSlug]);

        $approvedProduct = Product::create([
            'business_profile_id' => $business->id,
            'category_id' => 1,
            'sku' => 'SKU-LINK-' . uniqid(),
            'unit' => 'piece',
            'base_price' => 50.00,
            'currency' => 'USD',
            'status' => 'approved',
        ]);
        $approvedProduct->translations()->create([
            'locale' => 'en',
            'name' => 'Eye Button Link Test Product',
        ]);

        $response = $this->actingAs($user)->get(route('products.index'));
        $response->assertStatus(200);
        $content = $response->getContent();

        // Eye button has link to public profile URL pattern
        $expectedUrl = url('/en/directory/' . $activeSlug . '#product-' . $approvedProduct->id);
        $this->assertStringContainsString($expectedUrl, $content);
    }
}


