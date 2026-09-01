<?php

test('http responses include secure Content-Security-Policy headers complying with Mozilla Observatory guidelines', function () {
    $response = $this->get('/');

    $response->assertOk();

    // Verify basic security headers
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

    // Verify CSP header presence
    $csp = $response->headers->get('Content-Security-Policy');
    expect($csp)->not->toBeNull();

    // Verify object-src is strictly 'none' (prevents plugin execution)
    expect($csp)->toContain("object-src 'none'");

    // Verify script-src and style-src allow self, unsafe-eval, unsafe-inline for Livewire 3 / Alpine reactivity
    expect($csp)->toContain("script-src 'self' 'unsafe-inline' 'unsafe-eval'");
    expect($csp)->toContain("style-src 'self' 'unsafe-inline'");

    // Verify base-uri, form-action, and frame-ancestors restrictions
    expect($csp)->toContain("base-uri 'self'");
    expect($csp)->toContain("form-action 'self'");
    expect($csp)->toContain("frame-ancestors 'self'");
});

test('unencrypted http requests redirect to https when force_https is enabled or in production', function () {
    config(['app.force_https' => true]);

    $response = $this->get('http://localhost/request');

    $response->assertStatus(301);
    expect($response->headers->get('Location'))->toBe('https://localhost/request');
});
