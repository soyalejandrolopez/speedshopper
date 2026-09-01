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

    // Verify deny by default (default-src 'none') and object-src 'none'
    expect($csp)->toContain("default-src 'none'");
    expect($csp)->toContain("object-src 'none'");

    // Verify script-src and style-src do not contain unsafe-inline, unsafe-eval, or data:
    expect($csp)->toContain("script-src 'self'");
    expect($csp)->not->toMatch('/script-src[^;]*\'unsafe-inline\'/');
    expect($csp)->not->toMatch('/script-src[^;]*\'unsafe-eval\'/');
    expect($csp)->not->toMatch('/script-src[^;]*data:/');
    expect($csp)->not->toMatch('/script-src[^;]*https:\s*(;|$)/');
    expect($csp)->not->toMatch('/style-src[^;]*\'unsafe-inline\'/');

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
