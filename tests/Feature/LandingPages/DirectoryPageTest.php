<?php

use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\Country;
use App\Models\City;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use function Pest\Laravel\get;

beforeEach(function () {
    // Set up some data for testing the directory
    $this->category = Category::factory()->create(['status' => 'active']);
    $this->country = Country::factory()->create(['status' => 'active']);
    $this->city = City::factory()->create(['country_id' => $this->country->id, 'status' => 'active']);
    
    // Create an approved business
    $this->business = BusinessProfile::factory()->create([
        'status' => 'approved',
        'category_id' => $this->category->id,
        'city_id' => $this->city->id,
        'name' => 'Safe Secure Company'
    ]);
});

it('renders the directory index page successfully', function () {
    get('/directory')
        ->assertStatus(200)
        ->assertSee($this->business->name);
});

it('caches the directory index results to improve performance', function () {
    Cache::shouldReceive('remember')
        ->once()
        ->andReturn([
            'businesses' => collect([$this->business]),
            'categories' => collect([$this->category]),
            'countries' => collect([$this->country])
        ]);

    // This should trigger the Cache facade mock
    get('/directory')->assertStatus(200);
});

it('returns HTML fragments for AJAX requests (performance optimization)', function () {
    get('/directory', ['X-Requested-With' => 'XMLHttpRequest'])
        ->assertStatus(200)
        ->assertHeader('Content-Type', 'text/html; charset=UTF-8');
        // A full layout should not be rendered, just the fragment
});

it('safely handles malicious SQL injection attempts in search', function () {
    // Malicious search string
    $maliciousSearch = "' OR 1=1 --";
    
    // Laravel query builder protects against this automatically,
    // so we just assert it doesn't throw a 500 SQL error and returns 200.
    get('/directory?search=' . urlencode($maliciousSearch))
        ->assertStatus(200);
});

it('caches the live search results', function () {
    // Use a unique search term for this test
    $searchTerm = 'Secure';
    
    Cache::shouldReceive('remember')
        ->once()
        ->withArgs(function ($key, $ttl, $callback) use ($searchTerm) {
            return $key === 'live_search_' . md5($searchTerm);
        })
        ->andReturn([
            'categories' => [],
            'locations' => [],
            'companies' => collect([$this->business])
        ]);

    get('/directory/search?q=' . $searchTerm)
        ->assertStatus(200);
});

it('returns structured JSON for live search', function () {
    get('/directory/search?q=' . $this->business->name)
        ->assertStatus(200)
        ->assertJsonStructure([
            'categories',
            'locations',
            'companies' => [
                '*' => ['id', 'type', 'name', 'logo', 'category', 'url']
            ]
        ]);
});

it('safely handles empty search queries', function () {
    get('/directory/search?q=')
        ->assertStatus(200)
        ->assertJson([
            'categories' => [],
            'locations' => [],
            'companies' => []
        ]);
});
