<?php

use App\Models\ContactMessage;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\assertDatabaseHas;
use Illuminate\Support\Facades\RateLimiter;

it('renders the contact page successfully', function () {
    get('/contact')
        ->assertStatus(200);
});

it('validates missing fields on contact submission', function () {
    post('/contact', [], ['Accept' => 'application/json'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'message']);
});

it('validates invalid email on contact submission', function () {
    post('/contact', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'invalid-email',
        'message' => 'This is a valid message length.',
    ], ['Accept' => 'application/json'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('successfully submits a contact message and saves to database', function () {
    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'message' => 'This is a valid message that is longer than 10 characters.',
    ];

    post('/contact', $data)
        ->assertStatus(200)
        ->assertJson(['status' => 'success']);

    assertDatabaseHas('contact_messages', $data);
});

it('handles malicious inputs (XSS) safely', function () {
    $data = [
        'first_name' => '<script>alert("first")</script>',
        'last_name' => '<script>alert("last")</script>',
        'email' => 'hacker@example.com',
        'message' => '<script>alert("xss")</script> This is a malicious message.',
    ];

    post('/contact', $data)->assertStatus(200);

    assertDatabaseHas('contact_messages', [
        'email' => 'hacker@example.com',
        'first_name' => '<script>alert("first")</script>'
    ]);
});

it('enforces rate limiting on the contact form', function () {
    // Laravel may not apply rate limiting in feature tests unless specifically requested.
    // If this test fails, we may need to enable rate limiting middleware.
    $this->withMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);

    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'message' => 'This is a valid message length.',
    ];

    // Assuming the throttle is 5 per minute (throttle:5,1)
    for ($i = 0; $i < 5; $i++) {
        post('/contact', $data)->assertStatus(200);
    }

    // The 6th request should fail with 429 Too Many Requests
    post('/contact', $data)->assertStatus(429);
});
