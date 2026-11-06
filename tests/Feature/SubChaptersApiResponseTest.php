<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubChaptersApiResponseTest extends TestCase
{
    /**
     * A basic feature test example.
     * id_chapter needed (no default)
     * lcid needed (default "en")
     * icons_scale needed (default "L")
     * @return void
     */
    public function test_successResponse()
    {
        $response = $this->get(route('sub-chapter-api')."?id_chapter");

        $response->assertSuccessful();
    }
}
