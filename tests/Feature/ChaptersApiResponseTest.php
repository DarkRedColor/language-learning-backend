<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChaptersApiResponseTest extends TestCase
{
    /**
     * A basic feature test example.
     * id_episode needed (no default)
     * lcid needed (default "en")
     * icons_scale needed (default "L")
     * @return void
     */
    public function test_successResponse()
    {
        $response = $this->get(route('chapter-api')."?id_episode=23");

        $response->assertSuccessful();
    }
}
