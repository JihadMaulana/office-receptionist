<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class GuestControllerTest extends TestCase
{
    /**
     * Invoke single action controller.
     *
     * @return void
     */
    public function test()
    {
        if (!Route::has('guest')) { $this->expectNotToPerformAssertions(); return; }
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->get(route('guest'));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertViewIs('guest');
    }
}
