<?php

namespace Tests\Feature\Api;

use App\Models\GuestRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class GuestRequestControllerTest extends TestCase
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function testIndex()
    {
        if (!Route::has('api.guest_requests.index')) { $this->expectNotToPerformAssertions(); return; }
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user, 'api');

        $guest_requests = GuestRequest::factory(5)->create();

        $response = $this->getJson(route('api.guest_requests.index')."?search=lorem");
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSuccessful();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function testStore()
    {
        if (!Route::has('api.guest_requests.store')) { $this->expectNotToPerformAssertions(); return; }
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user, 'api');
        $response = $this->postJson(route('api.guest_requests.store'), GuestRequest::factory()->make()->toArray());
        if ($response->exception) {
            $this->expectOutputString('');
            if ($response->status() == 422)
                $this->setOutputCallback(function () use($response) { return json_encode(json_decode($response->content(), true)['errors'], JSON_PRETTY_PRINT); });
            else $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSuccessful();
    }

    /**
     * Display the specified resource.
     *
     * @return void
     */
    public function testShow()
    {
        if (!Route::has('api.guest_requests.show')) { $this->expectNotToPerformAssertions(); return; }
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user, 'api');

        $guest_request = GuestRequest::factory()->create();

        $response = $this->getJson(route('api.guest_requests.show', [ $guest_request->getKey() ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSuccessful();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return void
     */
    public function testUpdate()
    {
        if (!Route::has('api.guest_requests.update')) { $this->expectNotToPerformAssertions(); return; }
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user, 'api');

        $guest_request = GuestRequest::factory()->create();

        $response = $this->putJson(route('api.guest_requests.update', [ $guest_request->getKey() ]), GuestRequest::factory()->make()->toArray());
        if ($response->exception) {
            $this->expectOutputString('');
            if ($response->status() == 422)
                $this->setOutputCallback(function () use($response) { return json_encode(json_decode($response->content(), true)['errors'], JSON_PRETTY_PRINT); });
            else $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSuccessful();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return void
     */
    public function testDestroy()
    {
        if (!Route::has('api.guest_requests.destroy')) { $this->expectNotToPerformAssertions(); return; }
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user, 'api');

        $guest_request = GuestRequest::factory()->create();

        $response = $this->deleteJson(route('api.guest_requests.destroy', [ $guest_request->getKey() ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSuccessful();
    }
}
