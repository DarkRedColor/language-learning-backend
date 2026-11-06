<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LanguagesApiResponseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_successResponse()
    {
        $response = $this->get(route('language-api'));

        $response->assertSuccessful();
    }
}
