<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SeasonsApiResponseTest extends TestCase
{
    /**
     * A basic feature test example.
     * lcid needed (default "en")
     * icons_scale needed (default "L")
     * @return void
     */
    public function test_successResponseTest()
    {
        $response = $this->get(route('season-api'));

        $response->assertSuccessful();
    }
}
