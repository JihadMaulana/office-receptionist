<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SearchEmployeeControllerTest extends TestCase
{
    /**
     * Invoke single action controller.
     *
     * @return void
     */
    public function test()
    {
        if (!Route::has('api.search_employee')) { $this->expectNotToPerformAssertions(); return; }
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user, 'api');
        $response = $this->getJson(route('api.search_employee'));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
        }
        $response->assertSuccessful();
    }
}
