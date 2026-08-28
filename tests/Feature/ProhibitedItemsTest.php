<?php

use function Pest\Laravel\get;

it('renders the prohibited and restricted items page in spanish', function () {
    app()->setLocale('es');

    get(route('prohibited-items'))
        ->assertOk()
        ->assertSee(__('Productos Prohibidos y Restringidos'))
        ->assertSee(__('No aceptamos:'))
        ->assertSee(__('Armas de fuego, municiones, explosivos o artículos relacionados.'))
        ->assertSee(__('Productos que requieren revisión antes de comprar o enviar:'))
        ->assertSee(__('Perfumes o fragancias.'))
        ->assertSee(__('Importante'));
});

it('renders the prohibited and restricted items page in english', function () {
    app()->setLocale('en');

    get(route('prohibited-items'))
        ->assertOk()
        ->assertSee('Prohibited and Restricted Items')
        ->assertSee('We do not accept:')
        ->assertSee('Firearms, ammunition, explosives, or related items.')
        ->assertSee('Products that require review before purchasing or shipping:')
        ->assertSee('Perfumes or fragrances.')
        ->assertSee('Important');
});

it('resolves all alias routes for prohibited items', function () {
    get('/productos-prohibidos')->assertOk();
    get('/prohibidos-y-restringidos')->assertOk();
    get('/prohibited-items')->assertOk();
});
