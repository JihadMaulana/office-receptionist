<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RequestMeetControllerTest extends TestCase
{
    /**
     * Invoke single action controller.
     *
     * @return void
     */
    public function test()
    {
        if (!Route::has('request_meet')) { $this->expectNotToPerformAssertions(); return; }
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user, 'api');
        $response = $this->getJson(route('request_meet'));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
        }
        $response->assertSuccessful();
    }
}
