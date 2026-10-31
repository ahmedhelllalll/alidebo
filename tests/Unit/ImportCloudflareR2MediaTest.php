<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Imports\BusinessProfilesImport;
use App\Models\BusinessProfile;
use App\Models\BusinessMedia;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ImportCloudflareR2MediaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.mysql.database' => 'alidebo_staging']);
        DB::purge('mysql');
        DB::reconnect('mysql');
    }

    /**
     * Test isPreUploadedR2Url correctly identifies Cloudflare R2 domains and rejects external URLs.
     */
    public function test_is_pre_uploaded_r2_url_validation()
    {
        $admin = User::first() ?? User::factory()->create();
        $importer = new BusinessProfilesImport($admin->email, $admin->id);

        // Valid Cloudflare R2 URLs
        $this->assertTrue($importer->isPreUploadedR2Url('https://media.alidebo.com/businesses/logos/test-logo.webp'));
        $this->assertTrue($importer->isPreUploadedR2Url('http://media.alidebo.com/businesses/logos/test-logo.webp'));
        $this->assertTrue($importer->isPreUploadedR2Url('https://cdn.alidebo.com/businesses/covers/test-cover.jpg'));
        $this->assertTrue($importer->isPreUploadedR2Url('https://pub-12345abcdef.r2.dev/businesses/gallery/img1.png'));
        $this->assertTrue($importer->isPreUploadedR2Url('https://my-account.r2.cloudflarestorage.com/alidebo-media/logo.png'));
        $this->assertTrue($importer->isPreUploadedR2Url('businesses/logos/local-key.webp'));
        $this->assertTrue($importer->isPreUploadedR2Url('businesses/covers/cover-123.jpg'));
        $this->assertTrue($importer->isPreUploadedR2Url('businesses/gallery/photo-456.png'));

        // Dynamic config check
        config(['filesystems.disks.r2.url' => 'https://custom-r2-cdn.alidebo.com']);
        $this->assertTrue($importer->isPreUploadedR2Url('https://custom-r2-cdn.alidebo.com/images/test.jpg'));

        // Invalid & External URLs that must be rejected
        $this->assertFalse($importer->isPreUploadedR2Url('https://bayancare.com/wp-content/uploads/2026/08/logo.jpg'));
        $this->assertFalse($importer->isPreUploadedR2Url('https://example.com/photo.jpg'));
        $this->assertFalse($importer->isPreUploadedR2Url('https://wordpress.com/uploads/pic.png'));
        $this->assertFalse($importer->isPreUploadedR2Url('https://facebook.com/photos/123.jpg'));
        $this->assertFalse($importer->isPreUploadedR2Url('https://google.com/images/branding/googlelogo.png'));
        $this->assertFalse($importer->isPreUploadedR2Url('https://external-cdn.net/assets/image.webp'));

        // Spoofing attempts that must be rejected
        $this->assertFalse($importer->isPreUploadedR2Url('https://evil-site.com/businesses/logos/fake.jpg'));
        $this->assertFalse($importer->isPreUploadedR2Url('https://media.alidebo.com.evil.com/logo.jpg'));
        $this->assertFalse($importer->isPreUploadedR2Url('https://fake-r2.dev.attacker.com/cover.jpg'));

        // Malformed / Empty
        $this->assertFalse($importer->isPreUploadedR2Url(''));
        $this->assertFalse($importer->isPreUploadedR2Url(null));
        $this->assertFalse($importer->isPreUploadedR2Url('not-a-valid-url'));
    }

    /**
     * Test resolveMediaUrl returns the URL for R2 assets and silently returns null for external URLs.
     */
    public function test_resolve_media_url_behavior()
    {
        $admin = User::first() ?? User::factory()->create();
        $importer = new BusinessProfilesImport($admin->email, $admin->id);

        // Valid R2 URL passes through
        $r2Url = 'https://media.alidebo.com/businesses/logos/acme-logo.webp';
        $this->assertEquals($r2Url, $importer->resolveMediaUrl($r2Url, 'logos'));

        // External URLs are silently ignored (return null)
        $this->assertNull($importer->resolveMediaUrl('https://example.com/photo.jpg', 'logos'));
        $this->assertNull($importer->resolveMediaUrl('https://wordpress-site.org/img.png', 'covers'));
        $this->assertNull($importer->resolveMediaUrl('https://facebook.com/photo.jpg', 'gallery'));
    }

    /**
     * Test that an imported row with valid Cloudflare R2 media links persists the media records properly.
     */
    public function test_import_persists_valid_cloudflare_r2_media()
    {
        Http::fake();

        $admin = User::first() ?? User::factory()->create();
        $importer = new BusinessProfilesImport($admin->email, $admin->id);

        $uniqueName = 'R2 Media Test Co ' . uniqid();
        $logoUrl = 'https://media.alidebo.com/businesses/logos/valid-logo-' . uniqid() . '.webp';
        $coverUrl = 'https://pub-abc.r2.dev/businesses/covers/valid-cover-' . uniqid() . '.jpg';
        $galleryUrl1 = 'https://media.alidebo.com/businesses/gallery/valid-gallery-1.webp';
        $galleryUrl2 = 'https://pub-abc.r2.dev/businesses/gallery/valid-gallery-2.jpg';

        $row = collect([
            'company_name'   => $uniqueName,
            'main_category'  => 'السيارات والمركبات',
            'sub_category'   => 'قطع غيار',
            'description'    => 'Test company with R2 media',
            'business_logo'  => $logoUrl,
            'business_cover' => $coverUrl,
            'image_1'        => $galleryUrl1,
            'image_2'        => $galleryUrl2,
            'phone'          => '01000' . rand(100000, 999999),
        ]);

        // Process row using the collection handler
        $importer->collection(collect([$row]));

        $profile = BusinessProfile::where('name', $uniqueName)->first();
        $this->assertNotNull($profile, 'Business profile should have been created');
        $this->assertEquals($logoUrl, $profile->logo);
        $this->assertEquals($coverUrl, $profile->cover);
        $this->assertEquals('r2', $profile->disk);

        $media = BusinessMedia::where('business_profile_id', $profile->id)->orderBy('order')->get();
        $this->assertCount(2, $media);
        $this->assertEquals($galleryUrl1, $media[0]->file_path);
        $this->assertEquals('r2', $media[0]->disk);
        $this->assertEquals($galleryUrl2, $media[1]->file_path);
        $this->assertEquals('r2', $media[1]->disk);

        // Assert NO outbound HTTP requests were made for R2 images
        Http::assertNothingSent();

        // Cleanup
        $profile->media()->delete();
        $profile->delete();
    }

    /**
     * Test that an imported row with external non-R2 URLs silently ignores the media
     * while importing the rest of the profile and making zero outbound HTTP requests.
     */
    public function test_import_silently_ignores_external_media_with_zero_http_requests()
    {
        Http::fake();

        $admin = User::first() ?? User::factory()->create();
        $importer = new BusinessProfilesImport($admin->email, $admin->id);

        $uniqueName = 'External Media Test Co ' . uniqid();
        $externalLogo = 'https://bayancare.com/wp-content/uploads/2026/08/logo.jpg';
        $externalCover = 'https://example.com/assets/images/cover.jpg';
        $externalGallery1 = 'https://facebook.com/photos/gallery1.png';
        $externalGallery2 = 'https://external-cdn.net/images/gallery2.webp';

        $row = collect([
            'company_name'   => $uniqueName,
            'main_category'  => 'السيارات والمركبات',
            'sub_category'   => 'قطع غيار',
            'description'    => 'Test company with external remote media',
            'business_logo'  => $externalLogo,
            'business_cover' => $externalCover,
            'image_1'        => $externalGallery1,
            'image_2'        => $externalGallery2,
            'phone'          => '01001' . rand(100000, 999999),
        ]);

        // Process row using the collection handler
        $importer->collection(collect([$row]));

        $profile = BusinessProfile::where('name', $uniqueName)->first();
        $this->assertNotNull($profile, 'Business profile should be successfully imported despite external media');

        // Logo and cover should be NULL
        $this->assertNull($profile->logo, 'External logo must be ignored and set to null');
        $this->assertNull($profile->cover, 'External cover must be ignored and set to null');

        // Gallery records must NOT be created
        $mediaCount = BusinessMedia::where('business_profile_id', $profile->id)->count();
        $this->assertEquals(0, $mediaCount, 'External gallery images must be omitted with 0 records created');

        // Crucial: STRICT ZERO HTTP OUTBOUND REQUESTS GUARANTEE
        Http::assertNothingSent();

        // Cleanup
        $profile->delete();
    }
}
