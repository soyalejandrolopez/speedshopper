<?php

use App\Livewire\Admin\Settings\SettingsIndex;
use App\Models\Setting;
use Livewire\Livewire;

it('builds a shade ramp from a base color', function () {
    $ramp = theme_color_ramp('#2563eb');

    expect($ramp['500'])->toBe('#2563eb');
    expect(hexdec(ltrim($ramp['50'], '#')))->toBeGreaterThan(hexdec(ltrim($ramp['500'], '#')));
    expect(hexdec(ltrim($ramp['950'], '#')))->toBeLessThan(hexdec(ltrim($ramp['500'], '#')));
    expect(hexdec(ltrim($ramp['400'], '#')))->toBeGreaterThan(hexdec(ltrim($ramp['600'], '#')));
});

it('injects the theme override when a custom color is set', function () {
    seedRoles();
    Setting::set('theme_color', '#2563eb');

    $this->get('/')
        ->assertOk()
        ->assertSee('--color-emerald-600: #1d4db7')
        ->assertSee('--color-teal-600: #1d4db7');
});

it('does not override the palette when using the default color', function () {
    seedRoles();
    Setting::set('theme_color', '#059669');

    $this->get('/')
        ->assertOk()
        ->assertDontSee('--color-emerald-600:');
});

it('shows the theme picker in settings', function () {
    $this->actingAs(createAdmin());

    Livewire::test(SettingsIndex::class)
        ->assertOk()
        ->assertSee(__('Site Theme'))
        ->assertSee(__('Preview'));
});

it('saves the theme color from settings', function () {
    $this->actingAs(createAdmin());

    Livewire::test(SettingsIndex::class)
        ->set('settings.theme_color', '#7c3aed')
        ->call('save')
        ->assertHasNoErrors();

    expect(Setting::get('theme_color'))->toBe('#7c3aed');
});
