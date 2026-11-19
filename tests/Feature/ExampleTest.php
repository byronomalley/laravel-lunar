<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_application_returns_a_successful_response(): void
    {
        config(['app.coming_soon' => false]);
        $this->withoutExceptionHandling();
        $response = $this->get('/');
        $response->assertRedirect('/collections/main');
    }

    public function test_coming_soon_middleware(): void
    {
        config(['app.coming_soon' => true]);
        $this->withoutExceptionHandling();
        $response = $this->get('/collections/main');
        $response->assertRedirect('/');
    }
}
