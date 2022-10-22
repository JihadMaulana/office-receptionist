<?php

namespace Tests\Feature\Api;

use App\Models\Division;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DivisionControllerTest extends TestCase
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function testIndex()
    {
        if (!Route::has('api.divisions.index')) { $this->expectNotToPerformAssertions(); return; }
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user, 'api');

        $divisions = Division::factory(5)->create();

        $response = $this->getJson(route('api.divisions.index')."?search=lorem");
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
        if (!Route::has('api.divisions.store')) { $this->expectNotToPerformAssertions(); return; }
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user, 'api');
        $response = $this->postJson(route('api.divisions.store'), Division::factory()->make()->toArray());
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
        if (!Route::has('api.divisions.show')) { $this->expectNotToPerformAssertions(); return; }
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user, 'api');

        $division = Division::factory()->create();

        $response = $this->getJson(route('api.divisions.show', [ $division->getKey() ]));
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
        if (!Route::has('api.divisions.update')) { $this->expectNotToPerformAssertions(); return; }
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user, 'api');

        $division = Division::factory()->create();

        $response = $this->putJson(route('api.divisions.update', [ $division->getKey() ]), Division::factory()->make()->toArray());
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
        if (!Route::has('api.divisions.destroy')) { $this->expectNotToPerformAssertions(); return; }
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user, 'api');

        $division = Division::factory()->create();

        $response = $this->deleteJson(route('api.divisions.destroy', [ $division->getKey() ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSuccessful();
    }
}
