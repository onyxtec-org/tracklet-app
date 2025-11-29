<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        // Dashboard route now requires authentication, so it redirects to login
        $response = $this->get('/');

        $response->assertStatus(302); // Redirect to login
        $response->assertRedirect('/login');
    }
}
