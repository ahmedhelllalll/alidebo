<?php

use App\Models\BusinessProfile;
use Illuminate\Support\Facades\Cache;
use function Pest\Laravel\get;

it('renders the home page successfully', function () {
    get('/')
        ->assertStatus(200)
        ->assertViewIs('welcome');
});

it('caches the featured companies on the home page for performance', function () {
    // Assert that Cache::remember is called for featured_companies
    Cache::shouldReceive('remember')
        ->once()
        ->withArgs(function ($key) {
            return $key === 'featured_companies';
        })
        ->andReturn(collect([])); // Return empty collection for the mock

    get('/')->assertStatus(200);
});

it('gracefully handles empty database on home page', function () {
    // Ensure no businesses exist
    BusinessProfile::query()->delete();

    get('/')
        ->assertStatus(200)
        ->assertViewIs('welcome');
});
