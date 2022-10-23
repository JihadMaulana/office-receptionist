<?php

namespace App\Http\Controllers;

use App\Models\GuestRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * RequestMeetController
 */
class RequestMeetController extends Controller
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
     * Rules
     * @param  \Illuminate\Http\Request|null $request
     * @param GuestRequest $guest_request
     * @return array
     */
    public static function rules(Request $request = null, GuestRequest $guest_request = null)
    {
        return [
            'guest_name' => 'required|string|max:255',
            'requested_user_id'=>'exists:users,id|nullable',
            'guest_need'=> 'string|max:255|nullable',
        ];
    }

    /**
     * Invoke single action controller.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function __invoke(Request $request)
    {
        $request->validate(self::rules($request));

        $guest_request = new GuestRequest;
        foreach (self::rules($request) as $key => $value) {
            if (Str::contains($value, [ 'file', 'image', 'mimetypes', 'mimes' ])) {
                if ($request->hasFile($key)) {
                    $guest_request->{$key} = $request->file($key)->store('guest_requests');
                } elseif ($request->exists($key)) {
                    $guest_request->{$key} = $request->{$key};
                }
            } elseif ($request->exists($key)) {
                $guest_request->{$key} = $request->{$key};
            }
        }

        try {
            DB::beginTransaction();
            $guest_request->save();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new HttpException(500, $exception->getMessage(), $exception);
        }

        return (new JsonResource($guest_request))->response()->setStatusCode(201);
    }
}
