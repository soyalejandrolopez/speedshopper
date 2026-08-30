<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        seedRoles();
    }

    public function test_registration_screen_redirects_to_request_page(): void
    {
        $response = $this->get('/register');

        $response->assertRedirect(route('request'));
    }
}
