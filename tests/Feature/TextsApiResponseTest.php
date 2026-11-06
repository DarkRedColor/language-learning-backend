<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TextsApiResponseTest extends TestCase
{
    /**
     * A basic feature test example.
     * lcid in body of get request needed (default "en")
     * @return void
     */
    public function test_successResponse()
    {

        $response = $this->get(route('text-api'));

        $response->assertSuccessful();
    }
}
