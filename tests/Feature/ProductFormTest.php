<?php

namespace Tests\Feature;

use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProductFormTest extends TestCase
{
    public function test_vendor_can_view_product_create_form_with_all_fixes()
    {
        $user = User::first() ?? User::factory()->create();
        $user->markEmailAsVerified();
        
        if (Category::count() < 15) {
            for ($i = Category::count() + 1; $i <= 20; $i++) {
                Category::create([
                    'name_en' => "Category {$i}",
                    'name_ar' => "تصنيف {$i}",
                    'slug' => "cat-{$i}-" . uniqid(),
                ]);
            }
        }
        $category = Category::first();

        $business = BusinessProfile::where('user_id', $user->id)->first();
        if (!$business) {
            $country = \App\Models\Country::first() ?? \App\Models\Country::create(['code' => 'SA', 'phone_code' => '+966', 'name_en' => 'Saudi Arabia', 'name_ar' => 'المملكة العربية السعودية']);
            $city = \App\Models\City::first() ?? \App\Models\City::create(['country_id' => $country->id, 'name_en' => 'Riyadh', 'name_ar' => 'الرياض']);

            $business = BusinessProfile::create([
                'user_id' => $user->id,
                'owner_id' => $user->id,
                'category_id' => $category->id,
                'city_id' => $city->id,
                'name' => 'Test Business Co',
                'slug' => 'test-business-co-' . uniqid(),
                'status' => 'approved',
            ]);
        } else if (!$business->owner_id) {
            $business->update(['owner_id' => $user->id]);
        }

        // Test in Arabic
        app()->setLocale('ar');
        $responseAr = $this->actingAs($user)->get(route('products.create'));
        $responseAr->assertStatus(200);

        $contentAr = $responseAr->getContent();

        // 1. Category Dropdown max-height & pinned search
        $this->assertStringContainsString('max-height: 280px;', $contentAr, 'Dropdown list must have max-height: 280px');
        $this->assertStringContainsString('catSearchInput', $contentAr, 'Pinned search input must exist');
        $this->assertStringContainsString('shrink-0', $contentAr, 'Search input container must not shrink');

        // 2. Category trigger button matches unit and name inputs with solid primary icon box
        $this->assertStringContainsString('x-ref="catDropdownBtn"', $contentAr);
        $this->assertStringContainsString('px-4 bg-primary text-white shrink-0', $contentAr);

        // 3. 15+ Categories loaded
        $categoriesCount = Category::count();
        $this->assertGreaterThanOrEqual(15, $categoriesCount, 'Must have at least 15 categories for scroll test');

        // 4. Live preview reactivity: localized placeholders
        $this->assertStringContainsString('preview_untitled', $contentAr);
        $this->assertStringContainsString('preview_no_desc', $contentAr);

        // 5. Informative Preview Header (Active Locale Badge)
        $this->assertStringContainsString('getLocaleFlag(activeLocale)', $contentAr);
        $this->assertStringContainsString('getLocaleName(activeLocale)', $contentAr);

        // 6. Dark mode support
        $this->assertStringContainsString('dark:bg-zinc-900', $contentAr);
        $this->assertStringContainsString('dark:border-zinc-800', $contentAr);

        // Test in English
        app()->setLocale('en');
        $responseEn = $this->actingAs($user)->get(route('products.create'));
        $responseEn->assertStatus(200);
        $contentEn = $responseEn->getContent();
        $this->assertStringContainsString('Live Preview', $contentEn);

        // 7. Item 1: Sticky Live Preview & Independent Scroll
        $this->assertStringContainsString('lg:sticky lg:top-6', $contentEn);
        $this->assertStringContainsString('position: sticky', $contentEn);
        $this->assertStringContainsString('overflow-y-auto custom-scrollbar', $contentEn);

        // 8. Item 4: Full Live Preview Localization (no hardcoded Arabic in preview)
        $this->assertStringContainsString('previewI18n', $contentEn);
        $this->assertStringContainsString('preview_request_quote', $contentEn);
        $this->assertStringContainsString('preview_contact_supplier', $contentEn);
        $this->assertStringContainsString('no_images', $contentEn);
        $this->assertStringContainsString('status_pending_notice', $contentEn);
        $this->assertStringContainsString('preview_price_on_request', $contentEn);

        // 9. Item 5: Proper SVG Info Icon instead of emoji ℹ️
        $this->assertStringNotContainsString('ℹ️', $contentEn);
        $this->assertStringContainsString('d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"', $contentEn);

        // 10. Item 6: Untitled Product placeholder has no italic styling
        $this->assertStringContainsString('class="text-lg font-[900] tracking-tight leading-snug text-start transition-colors"', $contentEn);

        // 11. Item 7: Resolved duplicate Request Quote (Price on Request in price area)
        $this->assertStringContainsString('preview_price_on_request', $contentEn);

        // 12. Item 8: Responsive Pricing & MOQ Row (1 col mobile, 2 cols tablet, 3 cols desktop)
        $this->assertStringContainsString('grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6', $contentEn);

        // 13. Item 9: Redesigned Success/Error alert
        $this->assertStringContainsString('toast.visible', $contentEn);
        $this->assertStringContainsString('border-emerald-500/30', $contentEn);
        $this->assertStringContainsString('border-rose-500/30', $contentEn);

        // 14. Item 10: Fullscreen Image Gallery Lightbox (solid theme background, counter, navigation)
        $this->assertStringContainsString('x-show="lightbox.open"', $contentEn);
        $this->assertStringContainsString('bg-white dark:bg-zinc-950', $contentEn); // Solid background, NOT transparent
        $this->assertStringContainsString('lightbox.currentIndex + 1', $contentEn); // Counter
        $this->assertStringContainsString('nextLightboxImage()', $contentEn);
        $this->assertStringContainsString('prevLightboxImage()', $contentEn);
        $this->assertStringContainsString('closeLightbox()', $contentEn);

        // Test in Turkish
        $responseTr = $this->actingAs($user)->withSession(['locale' => 'tr'])->get(route('products.create'));
        $responseTr->assertStatus(200);
        $contentTr = $responseTr->getContent();
        $this->assertStringContainsString('Canlı Önizleme', $contentTr);
        $this->assertStringContainsString('Teklif İste', $contentTr); // Turkish preview_request_quote
        $this->assertStringContainsString('Tedarikçiyle İletişime Geç', $contentTr); // Turkish preview_contact_supplier
    }

    public function test_create_product_redirects_to_products_index_with_success_flash_message()
    {
        Queue::fake();
        $user = User::first() ?? User::factory()->create();
        $user->markEmailAsVerified();
        $category = Category::first();
        $business = BusinessProfile::where('user_id', $user->id)->first();

        $payload = [
            'name' => ['en' => 'Newly Created Product', 'ar' => 'منتج جديد تم إنشاؤه'],
            'short_description' => ['en' => 'Short desc of product', 'ar' => 'وصف مختصر'],
            'category_id' => $category->id,
            'unit' => 'piece',
            'moq' => 10,
            'base_price' => 55.00,
        ];

        // 1. Traditional POST submission
        $response = $this->actingAs($user)
            ->withSession(['locale' => 'ar'])
            ->post(route('products.store'), $payload);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success', __('products.success_created'));

        // 2. AJAX / Fetch submission
        $ajaxPayload = [
            'name' => ['en' => 'AJAX Created Product', 'ar' => 'منتج جديد عبر AJAX'],
            'short_description' => ['en' => 'Short desc of AJAX product', 'ar' => 'وصف مختصر'],
            'category_id' => $category->id,
            'unit' => 'carton',
            'moq' => 5,
            'base_price' => 120.00,
        ];

        $ajaxResponse = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->postJson(route('products.store'), $ajaxPayload);

        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJson([
            'success' => true,
            'redirect_url' => route('products.index'),
            'message' => __('products.success_created', [], 'en'),
        ]);
        $this->assertEquals(session('success'), __('products.success_created', [], 'en'));
    }

    public function test_update_product_redirects_to_products_index_with_success_flash_message()
    {
        Queue::fake();
        $user = User::first() ?? User::factory()->create();
        $user->markEmailAsVerified();
        $category = Category::first();
        $business = BusinessProfile::where('user_id', $user->id)->first();

        $product = Product::create([
            'business_profile_id' => $business->id,
            'category_id' => $category->id,
            'sku' => 'SKU-UPDATE-TEST-' . uniqid(),
            'unit' => 'piece',
            'moq' => 10,
            'base_price' => 100.00,
            'status' => 'pending',
        ]);
        $product->translations()->create(['locale' => 'en', 'name' => 'Original Name', 'short_description' => 'Original desc']);

        $updatePayload = [
            'name' => ['en' => 'Updated Product Title', 'ar' => 'عنوان المنتج المحدث'],
            'short_description' => ['en' => 'Updated short description', 'ar' => 'وصف مختصر محدث'],
            'category_id' => $category->id,
            'unit' => 'piece',
            'moq' => 20,
            'base_price' => 110.00,
        ];

        // 1. Traditional PUT submission
        $response = $this->actingAs($user)
            ->withSession(['locale' => 'ar'])
            ->put(route('products.update', $product->id), $updatePayload);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success', __('products.success_updated'));

        // 2. AJAX / Fetch submission
        $ajaxResponse = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->putJson(route('products.update', $product->id), $updatePayload);

        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJson([
            'success' => true,
            'redirect_url' => route('products.index'),
            'message' => __('products.success_updated', [], 'en'),
        ]);
        $this->assertEquals(session('success'), __('products.success_updated', [], 'en'));
    }

    public function test_create_validation_failure_stays_on_form_without_redirect()
    {
        $user = User::first() ?? User::factory()->create();
        $user->markEmailAsVerified();

        // Missing required unit and English name
        $response = $this->actingAs($user)
            ->postJson(route('products.store'), [
                'name' => ['ar' => 'بدون اسم إنجليزي'],
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name.en', 'unit']);
    }

    public function test_products_index_renders_toast_from_session_flash()
    {
        $user = User::first() ?? User::factory()->create();
        $user->markEmailAsVerified();

        $flashMessage = 'تم تحديث المنتج بنجاح.';

        $response = $this->actingAs($user)
            ->withSession(['locale' => 'ar', 'success' => $flashMessage])
            ->get(route('products.index'));

        $response->assertStatus(200);
        $content = $response->getContent();

        // 1. Root element uses clean x-data="productsIndex()" without inline quoting collision
        $this->assertStringContainsString('x-data="productsIndex()"', $content);
        $this->assertStringNotContainsString('catch { { ;this.deleteModal.isDeleting = false;', $content);
        $this->assertStringNotContainsString('dir="rtl" class="max-w-7xl mx--', $content);

        // 2. Flash message is correctly embedded in productsIndex script block
        $this->assertStringContainsString(json_encode($flashMessage), $content);
        $this->assertStringContainsString('this.showToast(', $content);

        // 3. Delete modal markup is intact
        $this->assertStringContainsString('deleteModal.open', $content);
        $this->assertStringContainsString('confirmDelete()', $content);
    }

    public function test_products_index_handles_products_with_quotes_in_title_safely()
    {
        $user = User::first() ?? User::factory()->create();
        $user->markEmailAsVerified();
        $business = BusinessProfile::where('user_id', $user->id)->first();
        $category = Category::first();

        $productWithQuotes = Product::create([
            'business_profile_id' => $business->id,
            'category_id' => $category->id,
            'sku' => 'SKU-QUOTE-' . uniqid(),
            'unit' => 'piece',
            'moq' => 1,
            'base_price' => 199.99,
            'status' => 'approved',
        ]);
        $productWithQuotes->translations()->create([
            'locale' => 'ar',
            'name' => 'شاشة 15" بوصة O\'Connor "الاحترافية"',
            'short_description' => 'شاشة بدقة عالية'
        ]);
        $productWithQuotes->translations()->create([
            'locale' => 'en',
            'name' => '15" O\'Connor "Professional" Display',
            'short_description' => 'High-res display'
        ]);

        $response = $this->actingAs($user)
            ->withSession(['locale' => 'ar', 'success' => __('products.success_updated')])
            ->get(route('products.index'));

        $response->assertStatus(200);
        $content = $response->getContent();

        // Confirm openDeleteModal doesn't contain unescaped quotes breaking HTML
        $this->assertStringNotContainsString("openDeleteModal({$productWithQuotes->id}, '", $content);
        $this->assertStringContainsString("openDeleteModal({$productWithQuotes->id}, ", $content);
        // Ensure no syntax corruption
        $this->assertStringNotContainsString('catch { { ;this.deleteModal.isDeleting = false;', $content);
    }

    public function test_end_to_end_update_and_create_redirect_renders_flawless_html_without_raw_code()
    {
        Queue::fake();
        $user = User::first() ?? User::factory()->create();
        $user->markEmailAsVerified();
        $business = BusinessProfile::where('user_id', $user->id)->first();
        $category = Category::first();

        $product = Product::create([
            'business_profile_id' => $business->id,
            'category_id' => $category->id,
            'sku' => 'SKU-E2E-' . uniqid(),
            'unit' => 'piece',
            'moq' => 5,
            'base_price' => 75.00,
            'status' => 'approved',
        ]);
        $product->translations()->create([
            'locale' => 'ar',
            'name' => 'منتج اختبار شامل',
            'short_description' => 'وصف تجريبي'
        ]);
        $product->translations()->create([
            'locale' => 'en',
            'name' => 'E2E Test Product',
            'short_description' => 'Test description'
        ]);

        // 1. Test UPDATE redirect
        $updateResponse = $this->actingAs($user)
            ->withSession(['locale' => 'ar'])
            ->put(route('products.update', $product->id), [
                'name' => ['ar' => 'منتج اختبار تم تحديثه', 'en' => 'E2E Updated Product'],
                'short_description' => ['ar' => 'وصف محدث', 'en' => 'Updated desc'],
                'category_id' => $category->id,
                'unit' => 'piece',
                'moq' => 10,
                'base_price' => 80.00,
            ]);

        $updateResponse->assertRedirect(route('products.index'));
        $indexAfterUpdate = $this->actingAs($user)
            ->withSession(['locale' => 'ar', 'success' => session('success')])
            ->get(route('products.index'));

        $indexAfterUpdate->assertStatus(200);
        $htmlUpdate = $indexAfterUpdate->getContent();

        // Strict assertions against any raw code leaking
        $this->assertStringNotContainsString('catch { { ;this.deleteModal.isDeleting = false;', $htmlUpdate);
        $this->assertStringNotContainsString('dir="rtl" class="max-w-7xl mx--', $htmlUpdate);
        $this->assertStringContainsString('x-data="productsIndex()"', $htmlUpdate);
        $this->assertStringContainsString('deleteModal.open', $htmlUpdate);
        $this->assertStringContainsString('function productsIndex()', $htmlUpdate);

        // 2. Test CREATE redirect
        $createResponse = $this->actingAs($user)
            ->withSession(['locale' => 'ar'])
            ->post(route('products.store'), [
                'name' => ['ar' => 'منتج اختبار تم إنشاؤه', 'en' => 'E2E Created Product'],
                'short_description' => ['ar' => 'وصف جديد', 'en' => 'New desc'],
                'category_id' => $category->id,
                'unit' => 'piece',
                'moq' => 15,
                'base_price' => 120.00,
            ]);

        $createResponse->assertRedirect(route('products.index'));
        $indexAfterCreate = $this->actingAs($user)
            ->withSession(['locale' => 'ar', 'success' => session('success')])
            ->get(route('products.index'));

        $indexAfterCreate->assertStatus(200);
        $htmlCreate = $indexAfterCreate->getContent();

        $this->assertStringNotContainsString('catch { { ;this.deleteModal.isDeleting = false;', $htmlCreate);
        $this->assertStringNotContainsString('dir="rtl" class="max-w-7xl mx--', $htmlCreate);
        $this->assertStringContainsString('x-data="productsIndex()"', $htmlCreate);
        $this->assertStringContainsString('deleteModal.open', $htmlCreate);
        $this->assertStringContainsString('function productsIndex()', $htmlCreate);
    }

    public function test_products_create_renders_onboarding_tour_and_retrigger_button()
    {
        $user = User::first() ?? User::factory()->create();
        $user->markEmailAsVerified();

        // 1. Arabic View
        $responseAr = $this->actingAs($user)
            ->withSession(['locale' => 'ar'])
            ->get(route('products.create'));

        $responseAr->assertStatus(200);
        $contentAr = $responseAr->getContent();

        // Validate JSON and JS syntax in Arabic for products.create
        $this->assertTourConfigIsValid($contentAr, 'create');

        // Re-trigger button
        $this->assertStringContainsString('$dispatch(\'open-products-tour\')', $contentAr);

        // Target IDs present in DOM
        $this->assertStringContainsString('id="tour-category-field"', $contentAr);
        $this->assertStringContainsString('id="tour-language-tabs"', $contentAr);
        $this->assertStringContainsString('id="tour-image-upload"', $contentAr);
        $this->assertStringContainsString('id="tour-pricing-grid"', $contentAr);
        $this->assertStringContainsString('id="tour-submit-section"', $contentAr);

        // 2. English View
        $responseEn = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get(route('products.create'));

        $responseEn->assertStatus(200);
        $contentEn = $responseEn->getContent();

        // Validate JSON and JS syntax in English for products.create
        $this->assertTourConfigIsValid($contentEn, 'create');
        $this->assertStringContainsString('Product Guide', $contentEn);
        $this->assertStringContainsString('Select a Category', $contentEn);

        // 3. Spotlight Cutout & Solid Badge Assertions
        $this->assertStringContainsString('id="tour-spotlight-mask"', $contentEn);
        $this->assertStringContainsString('mask="url(#tour-spotlight-mask)"', $contentEn);
        $this->assertStringContainsString('.tour-spotlight-active', $contentEn);
        $this->assertStringContainsString('w-7 h-7 rounded-full bg-primary text-white', $contentEn);
        $this->assertStringContainsString('tour-popover-card', $contentEn);
        $this->assertStringContainsString('scroll-margin-top: 100px', $contentEn);
        $this->assertStringContainsString('scrollToTarget(', $contentEn);
        $this->assertStringContainsString('getHeaderHeight()', $contentEn);
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

    public function test_product_form_renders_custom_alpine_currency_dropdown_without_select_element()
    {
        $user = User::first() ?? User::factory()->create();
        $user->markEmailAsVerified();

        $response = $this->actingAs($user)->get(route('products.create'));
        $response->assertStatus(200);
        $content = $response->getContent();

        // 1. Confirm strict requirement: no native HTML <select> for currency
        $this->assertStringNotContainsString('<select name="currency"', $content, 'Currency selector must NOT use native <select> tag');
        $this->assertStringNotContainsString('<select', $content, 'Form should not use native <select> tags');

        // 2. Confirm custom Alpine.js dropdown component for currency
        $this->assertStringContainsString('x-ref="currencyDropdownBtn"', $content);
        $this->assertStringContainsString('name="currency"', $content);
        $this->assertStringContainsString('initialCurrency', $content);
        $this->assertStringContainsString('form.currency', $content);
        $this->assertStringContainsString('getPreviewPrice()', $content);
        $this->assertStringContainsString('toggle($refs.currencyDropdownBtn)', $content);
    }

    public function test_store_product_saves_currency_or_defaults_to_egp()
    {
        Queue::fake();
        $user = User::first() ?? User::factory()->create();
        $user->markEmailAsVerified();
        $category = Category::first();
        $business = BusinessProfile::where('user_id', $user->id)->first();

        // Case A: Explicit USD
        $usdPayload = [
            'name' => ['en' => 'USD Priced Product'],
            'short_description' => ['en' => 'USD product desc'],
            'category_id' => $category->id,
            'unit' => 'piece',
            'base_price' => 150.00,
            'currency' => 'USD',
        ];

        $responseUsd = $this->actingAs($user)->postJson(route('products.store'), $usdPayload);
        $responseUsd->assertStatus(200);

        $usdProduct = Product::where('business_profile_id', $business->id)->latest('id')->first();
        $this->assertEquals('USD', $usdProduct->currency);
        $this->assertEquals('$150.00', $usdProduct->formatted_price);

        // Case B: Default without currency provided -> defaults to EGP
        $defaultPayload = [
            'name' => ['en' => 'Default EGP Product'],
            'short_description' => ['en' => 'EGP product desc'],
            'category_id' => $category->id,
            'unit' => 'piece',
            'base_price' => 250.00,
        ];

        $responseDef = $this->actingAs($user)->postJson(route('products.store'), $defaultPayload);
        $responseDef->assertStatus(200);

        $egpProduct = Product::where('business_profile_id', $business->id)->latest('id')->first();
        $this->assertEquals('EGP', $egpProduct->currency);
        app()->setLocale('ar');
        $this->assertEquals('250.00 ج.م', $egpProduct->formatted_price);
        app()->setLocale('en');
        $this->assertEquals('250.00 EGP', $egpProduct->formatted_price);
    }

    public function test_update_product_updates_currency()
    {
        Queue::fake();
        $user = User::first() ?? User::factory()->create();
        $user->markEmailAsVerified();
        $category = Category::first();
        $business = BusinessProfile::where('user_id', $user->id)->first();

        $product = Product::create([
            'business_profile_id' => $business->id,
            'category_id' => $category->id,
            'sku' => 'SKU-CURR-' . uniqid(),
            'unit' => 'piece',
            'moq' => 1,
            'base_price' => 300.00,
            'currency' => 'EGP',
            'status' => 'pending',
        ]);
        $product->translations()->create(['locale' => 'en', 'name' => 'Initial EGP Product', 'short_description' => 'Desc']);

        $updatePayload = [
            'name' => ['en' => 'Switched to USD Product'],
            'short_description' => ['en' => 'Desc'],
            'category_id' => $category->id,
            'unit' => 'piece',
            'base_price' => 300.00,
            'currency' => 'USD',
        ];

        $response = $this->actingAs($user)->putJson(route('products.update', $product->id), $updatePayload);
        $response->assertStatus(200);

        $product->refresh();
        $this->assertEquals('USD', $product->currency);
        $this->assertEquals('$300.00', $product->formatted_price);
    }

    public function test_invalid_currency_fails_validation()
    {
        $user = User::first() ?? User::factory()->create();
        $user->markEmailAsVerified();
        $category = Category::first();

        $invalidPayload = [
            'name' => ['en' => 'Invalid Currency Product'],
            'short_description' => ['en' => 'Desc'],
            'category_id' => $category->id,
            'unit' => 'piece',
            'currency' => 'EUR', // Unsupported currency
        ];

        $response = $this->actingAs($user)->postJson(route('products.store'), $invalidPayload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['currency']);
    }

    public function test_products_listing_renders_correct_currency_symbols()
    {
        $user = User::factory()->create(['role' => 'user']);
        $user->markEmailAsVerified();
        $category = Category::first() ?? Category::create(['name_en' => 'General', 'name_ar' => 'عام', 'slug' => 'gen-' . uniqid()]);
        $country = \App\Models\Country::first() ?? \App\Models\Country::create(['code' => 'EG', 'phone_code' => '+20', 'name_en' => 'Egypt', 'name_ar' => 'مصر']);
        $city = \App\Models\City::first() ?? \App\Models\City::create(['country_id' => $country->id, 'name_en' => 'Cairo', 'name_ar' => 'القاهرة']);

        $business = BusinessProfile::create([
            'user_id' => $user->id,
            'owner_id' => $user->id,
            'category_id' => $category->id,
            'city_id' => $city->id,
            'name' => 'Currency Test Vendor ' . uniqid(),
            'slug' => 'currency-test-vendor-' . uniqid(),
            'status' => 'approved',
        ]);

        $productUsd = Product::create([
            'business_profile_id' => $business->id,
            'category_id' => $category->id,
            'sku' => 'SKU-USD-' . uniqid(),
            'unit' => 'piece',
            'moq' => 1,
            'base_price' => 99.00,
            'currency' => 'USD',
            'status' => 'approved',
        ]);
        $productUsd->translations()->create(['locale' => 'en', 'name' => 'USD Listed Item', 'short_description' => 'USD item desc']);

        $productEgp = Product::create([
            'business_profile_id' => $business->id,
            'category_id' => $category->id,
            'sku' => 'SKU-EGP-' . uniqid(),
            'unit' => 'piece',
            'moq' => 1,
            'base_price' => 450.00,
            'currency' => 'EGP',
            'status' => 'approved',
        ]);
        $productEgp->translations()->create(['locale' => 'en', 'name' => 'EGP Listed Item', 'short_description' => 'EGP item desc']);

        // In Arabic
        $responseAr = $this->actingAs($user)
            ->withSession(['locale' => 'ar'])
            ->get(route('products.index'));
        $responseAr->assertStatus(200);
        $contentAr = $responseAr->getContent();
        $this->assertStringContainsString('$99.00', $contentAr);
        $this->assertStringContainsString('450.00 ج.م', $contentAr);

        // In English
        $responseEn = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get(route('products.index'));
        $responseEn->assertStatus(200);
        $contentEn = $responseEn->getContent();
        $this->assertStringContainsString('$99.00', $contentEn);
        $this->assertStringContainsString('450.00 EGP', $contentEn);
    }

    public function test_product_form_renders_custom_alpine_unit_dropdown_without_select_element()
    {
        $user = User::first() ?? User::factory()->create();
        $user->markEmailAsVerified();

        $response = $this->actingAs($user)->get(route('products.create'));
        $response->assertStatus(200);
        $content = $response->getContent();

        // 1. Confirm strict requirement: no native HTML <select> for unit
        $this->assertStringNotContainsString('<select name="unit"', $content, 'Unit selector must NOT use native <select> tag');
        $this->assertStringNotContainsString('<select', $content, 'Form should not use native <select> tags');

        // 2. Confirm custom Alpine.js dropdown component for unit
        $this->assertStringContainsString('x-ref="unitDropdownBtn"', $content);
        $this->assertStringContainsString('name="unit"', $content);
        $this->assertStringContainsString('initialUnit', $content);
        $this->assertStringContainsString('form.unit', $content);
        $this->assertStringContainsString('getPreviewUnit()', $content);
        $this->assertStringContainsString('toggle($refs.unitDropdownBtn)', $content);

        // 3. Confirm all 8 canonical units are present in the component config
        foreach (['piece', 'kg', 'carton', 'box', 'meter', 'set', 'ton', 'liter'] as $unitKey) {
            $this->assertStringContainsString('&quot;key&quot;:&quot;' . $unitKey . '&quot;', $content);
        }
    }

    public function test_invalid_unit_fails_validation()
    {
        $user = User::first() ?? User::factory()->create();
        $user->markEmailAsVerified();
        $category = Category::first();

        $invalidPayload = [
            'name' => ['en' => 'Invalid Unit Product'],
            'short_description' => ['en' => 'Desc'],
            'category_id' => $category->id,
            'unit' => 'some_custom_unsupported_unit',
            'base_price' => 50,
            'currency' => 'EGP',
        ];

        $response = $this->actingAs($user)->postJson(route('products.store'), $invalidPayload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['unit']);
    }

    public function test_all_canonical_units_pass_validation()
    {
        Queue::fake();
        $user = User::first() ?? User::factory()->create();
        $user->markEmailAsVerified();
        $category = Category::first();
        $business = BusinessProfile::where('user_id', $user->id)->first();

        $canonicalUnits = ['piece', 'kg', 'carton', 'box', 'meter', 'set', 'ton', 'liter'];

        foreach ($canonicalUnits as $unitKey) {
            $payload = [
                'name' => ['en' => "Test Product with unit {$unitKey}"],
                'short_description' => ['en' => "Desc for {$unitKey}"],
                'category_id' => $category->id,
                'unit' => $unitKey,
                'moq' => 5,
                'base_price' => 100.00,
                'currency' => 'EGP',
            ];

            $response = $this->actingAs($user)->postJson(route('products.store'), $payload);
            $response->assertStatus(200);

            $product = Product::where('business_profile_id', $business->id)->latest('id')->first();
            $this->assertEquals($unitKey, $product->unit);
            $this->assertNotEmpty($product->localized_unit);
        }
    }

    public function test_vendor_can_save_units_per_package_for_bulk_packaging_units()
    {
        Queue::fake();
        $user = User::first() ?? User::factory()->create();
        $user->markEmailAsVerified();
        $category = Category::first();
        $business = BusinessProfile::where('user_id', $user->id)->first();

        // 1. Create with carton + units_per_package = 24
        $payload = [
            'name' => ['en' => 'Bulk Packaging Widget'],
            'short_description' => ['en' => 'Desc for bulk widget'],
            'category_id' => $category->id,
            'unit' => 'carton',
            'units_per_package' => 24,
            'moq' => 10,
            'base_price' => 500.00,
            'currency' => 'EGP',
        ];

        $response = $this->actingAs($user)->postJson(route('products.store'), $payload);
        $response->assertStatus(200);

        $product = Product::where('business_profile_id', $business->id)->latest('id')->first();
        $this->assertEquals('carton', $product->unit);
        $this->assertEquals(24, $product->units_per_package);
        $this->assertTrue($product->isPackagingUnit());

        app()->setLocale('ar');
        $this->assertEquals('24 قطعة / كرتونة', $product->package_details);

        app()->setLocale('en');
        $this->assertEquals('24 pcs / carton', $product->package_details);

        // 2. Update to box + units_per_package = 50
        $updatePayload = [
            'name' => ['en' => 'Bulk Packaging Widget Updated'],
            'short_description' => ['en' => 'Desc for bulk widget updated'],
            'category_id' => $category->id,
            'unit' => 'box',
            'units_per_package' => 50,
            'moq' => 5,
            'base_price' => 250.00,
            'currency' => 'USD',
        ];

        $updateResponse = $this->actingAs($user)->postJson(route('products.update', $product->id), array_merge($updatePayload, ['_method' => 'PUT']));
        $updateResponse->assertStatus(200);

        $product->refresh();
        $this->assertEquals('box', $product->unit);
        $this->assertEquals(50, $product->units_per_package);
        $this->assertTrue($product->isPackagingUnit());
        $this->assertEquals('50 pcs / box', $product->package_details);
    }

    public function test_invalid_units_per_package_fails_validation()
    {
        $user = User::first() ?? User::factory()->create();
        $user->markEmailAsVerified();
        $category = Category::first();

        $payload = [
            'name' => ['en' => 'Invalid Units per Package Test'],
            'short_description' => ['en' => 'Invalid desc'],
            'category_id' => $category->id,
            'unit' => 'carton',
            'units_per_package' => -5,
        ];

        $response = $this->actingAs($user)->postJson(route('products.store'), $payload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['units_per_package']);
    }

    public function test_product_form_renders_units_per_package_field_and_preview_markup()
    {
        $user = User::first() ?? User::factory()->create();
        $user->markEmailAsVerified();

        $response = $this->actingAs($user)->get(route('products.create'));
        $response->assertStatus(200);

        $content = $response->getContent();
        $this->assertStringContainsString("['carton', 'box', 'set'].includes(form.unit)", $content);
        $this->assertStringContainsString('id="units_per_package"', $content);
        $this->assertStringContainsString('getPreviewPackageDetails()', $content);
    }

    public function test_product_form_loads_and_displays_dedicated_product_categories()
    {
        $user = User::first() ?? User::factory()->create();
        $user->markEmailAsVerified();

        $business = BusinessProfile::where('user_id', $user->id)->first();
        if (!$business) {
            $country = \App\Models\Country::first() ?? \App\Models\Country::create(['code' => 'SA', 'phone_code' => '+966', 'name_en' => 'Saudi Arabia', 'name_ar' => 'المملكة العربية السعودية']);
            $city = \App\Models\City::first() ?? \App\Models\City::create(['country_id' => $country->id, 'name_en' => 'Riyadh', 'name_ar' => 'الرياض']);
            $bizCat = Category::first() ?? Category::create(['name_en' => 'Biz Cat', 'name_ar' => 'تصنيف تجاري', 'slug' => 'biz-' . uniqid()]);

            $business = BusinessProfile::create([
                'user_id' => $user->id,
                'owner_id' => $user->id,
                'category_id' => $bizCat->id,
                'city_id' => $city->id,
                'name' => 'Test Vendor ' . uniqid(),
                'slug' => 'test-vendor-' . uniqid(),
                'status' => 'approved',
            ]);
        }

        // 1. In Arabic
        $responseAr = $this->actingAs($user)
            ->withSession(['locale' => 'ar'])
            ->get(route('products.create'));
        $responseAr->assertStatus(200);
        $contentAr = $responseAr->getContent();

        // Dedicated product categories must be present in Arabic
        $this->assertStringContainsString('إلكترونيات وأجهزة منزلية', $contentAr);
        $this->assertStringContainsString('آلات ومعدات وقطع غيار صناعية', $contentAr);
        $this->assertStringContainsString('مواد غذائية ومحاصيل زراعية', $contentAr);

        // 2. In English
        $responseEn = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get(route('products.create'));
        $responseEn->assertStatus(200);
        $contentEn = $responseEn->getContent();

        // Dedicated product categories must be present in English
        $this->assertStringContainsString('Consumer Electronics', $contentEn);
        $this->assertStringContainsString('Industrial Machinery', $contentEn);
        $this->assertStringContainsString('Food, Beverage', $contentEn);
    }

    public function test_product_category_relationship_resolves_to_product_category_model()
    {
        $user = User::first() ?? User::factory()->create();
        $business = BusinessProfile::first();
        $prodCat = ProductCategory::where('slug', 'electronics-appliances')->first();

        $product = Product::create([
            'business_profile_id' => $business->id,
            'category_id' => $prodCat->id,
            'sku' => 'SKU-PRODCAT-TEST',
            'unit' => 'piece',
            'moq' => 1,
            'base_price' => 250.00,
            'currency' => 'EGP',
            'status' => 'approved',
        ]);

        $this->assertInstanceOf(ProductCategory::class, $product->category);
        $this->assertEquals('electronics-appliances', $product->category->slug);
        $this->assertEquals($prodCat->id, $product->category->id);
    }
}



