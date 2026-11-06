<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EpisodesApiResponseTest extends TestCase
{
    /**
     * A basic feature test example.
     * id_season needed (no default)
     * lcid needed (default "en")
     * @return void
     */
    public function test_successResponse()
    {
        $response = $this->get(route('episode-api')."?id_season=5");

        $response->assertSuccessful();
    }
}
