<?php

namespace Tests\Feature;

use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProductAlertLocalizationTest extends TestCase
{
    protected function setupUserAndBusiness(): array
    {
        Queue::fake();

        $user = User::first() ?? User::factory()->create();
        $user->markEmailAsVerified();

        $category = Category::first() ?? Category::create([
            'name_en' => 'Electronics',
            'name_ar' => 'إلكترونيات',
            'slug' => 'electronics-' . uniqid(),
        ]);

        $business = BusinessProfile::where('user_id', $user->id)->first();
        if (!$business) {
            $country = Country::first() ?? Country::create(['code' => 'SA', 'phone_code' => '+966', 'name_en' => 'Saudi Arabia', 'name_ar' => 'المملكة العربية السعودية']);
            $city = City::first() ?? City::create(['country_id' => $country->id, 'name_en' => 'Riyadh', 'name_ar' => 'الرياض']);

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

        return [$user, $business, $category];
    }

    public function test_alert_component_is_fully_localized_in_arabic()
    {
        [$user, $business, $category] = $this->setupUserAndBusiness();

        // 1. Check form rendering in Arabic
        $response = $this->actingAs($user)->withSession(['locale' => 'ar'])->get(route('products.create'));
        $response->assertStatus(200);
        $content = $response->getContent();

        // Check localized titles in toast
        $this->assertStringContainsString('تم بنجاح', $content);
        $this->assertStringContainsString('خطأ', $content);
        $this->assertStringContainsString('إغلاق الإشعار', $content);
        $this->assertStringContainsString('وحدة القياس مطلوبة.', $content);
        $this->assertStringContainsString('الحد الأقصى 10 صور لكل منتج.', $content);
        $this->assertStringContainsString('خطأ في التحقق من البيانات. يرجى مراجعة الحقول.', $content);

        // 2. Trigger success alert in Arabic
        $successResponse = $this->actingAs($user)
            ->withSession(['locale' => 'ar'])
            ->postJson(route('products.store'), [
                'unit' => 'piece',
                'category_id' => $category->id,
                'name' => ['en' => 'Arabic Test Product', 'ar' => 'منتج اختبار عربي'],
                'short_description' => ['en' => 'Short desc in English', 'ar' => 'وصف قصير بالعربي'],
            ]);

        $successResponse->assertStatus(200);
        $successResponse->assertJson([
            'success' => true,
            'message' => 'تم حفظ المنتج بنجاح وهو قيد المراجعة حالياً.',
        ]);

        // 3. Trigger validation error alert in Arabic
        $errorResponse = $this->actingAs($user)
            ->withSession(['locale' => 'ar'])
            ->postJson(route('products.store'), [
                // Missing required unit, name.en, short_description.en
            ]);

        $errorResponse->assertStatus(422);
        $errorResponse->assertJsonValidationErrors(['name', 'short_description', 'unit']);
    }

    public function test_alert_component_is_fully_localized_in_english()
    {
        [$user, $business, $category] = $this->setupUserAndBusiness();

        // 1. Check form rendering in English
        $response = $this->actingAs($user)->withSession(['locale' => 'en'])->get(route('products.create'));
        $response->assertStatus(200);
        $content = $response->getContent();

        // Check localized titles in toast
        $this->assertStringContainsString('Success', $content);
        $this->assertStringContainsString('Error', $content);
        $this->assertStringContainsString('Dismiss notification', $content);
        $this->assertStringContainsString('Unit of measure is required.', $content);
        $this->assertStringContainsString('Maximum 10 images allowed per product.', $content);
        $this->assertStringContainsString('Validation error. Please check the entered data.', $content);

        // 2. Trigger success alert in English
        $successResponse = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->postJson(route('products.store'), [
                'unit' => 'piece',
                'category_id' => $category->id,
                'name' => ['en' => 'English Test Product'],
                'short_description' => ['en' => 'Short desc in English'],
            ]);

        $successResponse->assertStatus(200);
        $successResponse->assertJson([
            'success' => true,
            'message' => 'Product submitted successfully and is pending approval.',
        ]);

        // 3. Trigger validation error alert in English
        $errorResponse = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->postJson(route('products.store'), [
                // Missing required fields
            ]);

        $errorResponse->assertStatus(422);
        $errorResponse->assertJsonValidationErrors(['name', 'short_description', 'unit']);
    }

    public function test_alert_component_is_fully_localized_in_turkish()
    {
        [$user, $business, $category] = $this->setupUserAndBusiness();

        // 1. Check form rendering in Turkish
        $response = $this->actingAs($user)->withSession(['locale' => 'tr'])->get(route('products.create'));
        $response->assertStatus(200);
        $content = $response->getContent();

        // Check localized titles in toast
        $this->assertStringContainsString('Başarılı', $content);
        $this->assertStringContainsString('Hata', $content);
        $this->assertStringContainsString('Bildirimi kapat', $content);

        // 2. Trigger success alert in Turkish
        $successResponse = $this->actingAs($user)
            ->withSession(['locale' => 'tr'])
            ->postJson(route('products.store'), [
                'unit' => 'piece',
                'category_id' => $category->id,
                'name' => ['en' => 'Turkish Test Product', 'tr' => 'Türkçe Test Ürünü'],
                'short_description' => ['en' => 'Short desc in English', 'tr' => 'Türkçe kısa açıklama'],
            ]);

        $successResponse->assertStatus(200);
        $successResponse->assertJson([
            'success' => true,
            'message' => 'Ürün başarıyla gönderildi ve onay bekliyor.',
        ]);
    }
}
