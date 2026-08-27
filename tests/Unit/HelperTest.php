<?php

use App\Models\Setting;

test('money formats amount with dollar sign and two decimals', function () {
    expect(money(69.99))->toBe('$69.99')
        ->and(money(0))->toBe('$0.00')
        ->and(money(1234.5))->toBe('$1,234.50')
        ->and(money(null))->toBe('$0.00');
});

test('money handles string and integer inputs', function () {
    expect(money('42.5'))->toBe('$42.50')
        ->and(money(100))->toBe('$100.00');
});

test('country_name returns the country name for known codes', function () {
    expect(country_name('MX'))->toBe('México')
        ->and(country_name('GT'))->toBe('Guatemala')
        ->and(country_name('US'))->toBe('Estados Unidos')
        ->and(country_name('CO'))->toBe('Colombia');
});

test('country_name returns dash for null input', function () {
    expect(country_name(null))->toBe('—');
});

test('country_name returns the ISO code when unknown', function () {
    expect(country_name('ZZ'))->toBe('ZZ');
});

test('theme_color_is_default returns true for default color', function () {
    Setting::set('theme_color', '#059669');

    expect(theme_color_is_default())->toBeTrue();
});

test('theme_color_is_default returns false for custom color', function () {
    Setting::set('theme_color', '#2563eb');

    expect(theme_color_is_default())->toBeFalse();
});

test('theme_color_ramp generates all shade keys', function () {
    $ramp = theme_color_ramp('#059669');

    expect($ramp)->toHaveKeys([50, 100, 200, 300, 400, '500', 600, 700, 800, 900, 950])
        ->and($ramp['500'])->toBe('#059669');
});

test('theme_color_ramp lighter shades are brighter than base', function () {
    $ramp = theme_color_ramp('#059669');

    foreach ([50, 100, 200, 300, 400] as $shade) {
        $shadeHex = ltrim($ramp[$shade], '#');
        $baseHex = ltrim($ramp['500'], '#');
        expect(hexdec($shadeHex))->toBeGreaterThan(hexdec($baseHex));
    }
});

test('theme_color_ramp darker shades are dimmer than base', function () {
    $ramp = theme_color_ramp('#059669');

    foreach ([600, 700, 800, 900, 950] as $shade) {
        $shadeHex = ltrim($ramp[$shade], '#');
        $baseHex = ltrim($ramp['500'], '#');
        expect(hexdec($shadeHex))->toBeLessThan(hexdec($baseHex));
    }
});
