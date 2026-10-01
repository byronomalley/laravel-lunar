<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Option A: Stop Laravel from hiding the real error/redirect reason
        $this->withoutExceptionHandling();

        // Option B: See the exact URL it is redirecting to

        $response = $this->get('/');

//        dd($response->headers->get('Location'));


        $response->assertStatus(200);
    }
}
