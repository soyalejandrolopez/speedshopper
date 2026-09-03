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

it('injects #670753 custom color across emerald, teal and brand families', function () {
    seedRoles();
    Setting::set('theme_color', '#670753');

    $this->get('/')
        ->assertOk()
        ->assertSee('--color-brand-500: #670753')
        ->assertSee('--color-emerald-500: #670753')
        ->assertSee('--color-teal-500: #670753')
        ->assertSee('--theme-color: #670753');
});

it('saves and synchronizes theme color immediately with saveThemeColor', function () {
    $this->actingAs(createAdmin());

    Livewire::test(SettingsIndex::class)
        ->call('saveThemeColor', '#d86ec1')
        ->assertDispatched('theme-color-saved', color: '#d86ec1');

    expect(Setting::get('theme_color'))->toBe('#d86ec1');

    $this->get('/')
        ->assertOk()
        ->assertSee('--color-emerald-500: #d86ec1')
        ->assertSee('--color-teal-500: #d86ec1')
        ->assertSee('--color-brand-500: #d86ec1')
        ->assertSee('--theme-color: #d86ec1');
});
