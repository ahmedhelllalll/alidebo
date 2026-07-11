<?php

use function Pest\Laravel\get;

it('renders the about page successfully', function () {
    get('/about')
        ->assertStatus(200)
        ->assertViewIs('about');
});

it('does not require authentication to view about page', function () {
    get('/about')->assertStatus(200); // Guest user can access
});
