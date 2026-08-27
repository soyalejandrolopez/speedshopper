<?php

use App\Livewire\Admin\Settings\SettingsIndex;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

it('lets an admin upload a logo from settings', function () {
    $this->actingAs(createAdmin());

    Livewire::test(SettingsIndex::class)
        ->set('logo', UploadedFile::fake()->image('logo.png'))
        ->call('save')
        ->assertHasNoErrors();

    expect(Setting::get('logo_path'))->not->toBeEmpty()
        ->and(\Illuminate\Support\Facades\Storage::disk('public')->exists(Setting::get('logo_path')))->toBeTrue();
});

it('lets an admin remove the logo', function () {
    $this->actingAs(createAdmin());

    Setting::set('logo_path', 'branding/logo.png');
    \Illuminate\Support\Facades\Storage::disk('public')->put('branding/logo.png', 'fake');

    Livewire::test(SettingsIndex::class)
        ->call('removeLogo');

    expect(Setting::get('logo_path'))->toBe('')
        ->and(\Illuminate\Support\Facades\Storage::disk('public')->exists('branding/logo.png'))->toBeFalse();
});

it('renders the settings page with the branding section', function () {
    $this->actingAs(createAdmin());

    Livewire::test(SettingsIndex::class)
        ->assertOk()
        ->assertSee(__('Branding'))
        ->assertSee(__('Logo'))
        ->assertSee(__('Favicon'));
});
