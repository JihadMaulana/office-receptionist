<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * SearchEmployeeController
 */
class SearchEmployeeController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth:api');
    }

    /**
     * Invoke single action controller.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResource
     */
    public function __invoke(Request $request)
    {
        $users = User::query()->whereIn((new User)->qualifyColumn('role_id'), [2,3])->filter()
            ->paginate()->appends(request()->query());
//        $users = User::filter()
//            ->paginate()->appends(request()->query());

        return JsonResource::collection($users);
    }
}
