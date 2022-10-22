<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GuestRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * GuestRequestController
 * @extends Controller
 */
class GuestRequestController extends Controller
{
    /**
     * Rules
     * @param  \Illuminate\Http\Request|null $request
     * @param GuestRequest $guest_request
     * @return array
     */
    public static function rules(Request $request = null, GuestRequest $guest_request = null)
    {
        return [
            'hasMany' => [
                //'relations' => [
                //    'relations.*.name' => 'required_without:relations.*.id|string|max:255'
                //]
            ],
            'hasOne' => [
                //'relation' => [
                //    'relation.name' => 'required_without:relation.id|string|max:255'
                //]
            ],
            'store' => [
                //'parent_id' => 'required|exists:parents,id',
                'guest_name' => 'required|string|max:255',
                'requested_user_id'=>'required|exists',
                'guest_need'=> 'required|string|max:255',
                'servings'=> 'nullable|string',
            ],

            'update' => [
                //'parent_id' => 'exists:parents,id',
                'guest_name' => 'required|string|max:255',
                'requested_user_id'=>'required|exists',
                'guest_need'=> 'required|string|max:255',
                'servings'=> 'nullable|string',
            ]
        ];
    }

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $guest_requests = GuestRequest::filter()
            ->paginate()->appends(request()->query());
        $this->authorize('index', 'App\Models\GuestRequest');

        return JsonResource::collection($guest_requests);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $this->authorize('create', 'App\Models\GuestRequest');
        $request->validate(self::rules($request)['store']);
        foreach (self::rules($request)['hasMany'] as $key => $rule)
            $request->validate(array_merge([ $key => 'array' ], $rule));
        foreach (self::rules($request)['hasOne'] as $key => $rule)
            $request->validate(array_merge([ $key => 'array' ], $rule));

        $guest_request = new GuestRequest;
        foreach (self::rules($request)['store'] as $key => $value) {
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

        $deletedModels = [];
        $hasMany = [];
        foreach (self::rules($request)['hasMany'] as $key => $rule) {
            $hasMany[$key] = [];
            if ($request->exists($key)) {
                /** @var Model $model */
                $model = $guest_request->{$key}()->getRelated();
                /** @var Collection $models */
                $models = $model->newQuery()->findMany(collect($request->all()[$key] ?? [])->map(function ($data) use($model) {
                    return $data[$model->getKeyName()] ?? null;
                })->filter());
                foreach ($request->all()[$key] ?? [] as $index => $data) {
                    /** @var Model $model */
                    $model = $guest_request->{$key}()->getRelated();
                    if (isset($data[$model->getKeyName()])) $model = $models->find($data[$model->getKeyName()]) ?: $model;
                    if (isset($data['_action']) && in_array($data['_action'], [ 'delete', 'destroy' ])) {
                        if ($model->exists) $deletedModels[] = $model;
                        continue;
                    }
                    foreach ($rule as $k => $value) {
                        $attribute = last(explode('.*.', $k));
                        $k = str_replace('*', $index, $k);
                        if (Str::contains($value, ['file', 'image', 'mimetypes', 'mimes'])) {
                            if ($request->hasFile($k)) {
                                $model->{$attribute} = $request->file($k)->store($key);
                            } elseif ($request->exists($k)) {
                                $model->{$attribute} = $request->input($k);
                            }
                        } elseif ($request->exists($k)) {
                            $model->{$attribute} = $request->input($k);
                        }
                    }
                    $hasMany[$key][] = $model;
                }
            }
        }
        $hasOne = [];
        foreach (self::rules($request)['hasOne'] as $key => $rule) {
            if ($request->exists($key)) {
                /** @var Model $model */
                $model = $guest_request->{$key}()->getRelated();
                $data = $request->all()[$key];
                if (isset($data[$model->getKeyName()]))
                    $model = $model->newQuery()->find(($request->all()[$key] ?? [])[$model->getKeyName()] ?? null) ?: $model;
                if (isset($data['_action']) && in_array($data['_action'], [ 'delete', 'destroy' ])) {
                    if ($model->exists) $deletedModels[] = $model;
                    continue;
                }
                foreach ($rule as $k => $value) {
                    $attribute = last(explode('.', $k));
                    if (Str::contains($value, ['file', 'image', 'mimetypes', 'mimes'])) {
                        if ($request->hasFile($k)) {
                            $model->{$attribute} = $request->file($k)->store($key);
                        } elseif ($request->exists($k)) {
                            $model->{$attribute} = $request->input($k);
                        }
                    } elseif ($request->exists($k)) {
                        $model->{$attribute} = $request->input($k);
                    }
                }
                $hasOne[$key] = $model;
            }
        }

        try {
            DB::beginTransaction();
            $guest_request->save();
            foreach (self::rules($request)['hasMany'] as $key => $rule) {
                if (!$request->exists($key)) continue;
                $models = $guest_request->{$key}()->saveMany($hasMany[$key]);
                $guest_request->setRelation($key, new Collection($models));
            }
            foreach (self::rules($request)['hasOne'] as $key => $rule) {
                if (!$request->exists($key) || empty($hasOne[$key])) continue;
                $model = $guest_request->{$key}()->save($hasOne[$key]);
                $guest_request->setRelation($key, $model);
            }
            foreach ($deletedModels as $model) $model->delete();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new HttpException(500, $exception->getMessage(), $exception);
        }

        return (new JsonResource($guest_request))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GuestRequest  $guest_request
     * @return JsonResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(GuestRequest $guest_request)
    {
        $this->authorize('view', $guest_request);

        return new JsonResource($guest_request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GuestRequest  $guest_request
     * @return JsonResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, GuestRequest $guest_request)
    {
        $this->authorize('update', $guest_request);
        $request->validate(self::rules($request, $guest_request)['update']);
        foreach (self::rules($request, $guest_request)['hasMany'] as $key => $rule)
            $request->validate(array_merge([ $key => 'array' ], $rule));
        foreach (self::rules($request, $guest_request)['hasOne'] as $key => $rule)
            $request->validate(array_merge([ $key => 'array' ], $rule));

        foreach (self::rules($request, $guest_request)['update'] as $key => $value) {
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

        $deletedModels = [];
        $hasMany = [];
        foreach (self::rules($request, $guest_request)['hasMany'] as $key => $rule) {
            $hasMany[$key] = [];
            if ($request->exists($key)) {
                /** @var Model $model */
                $model = $guest_request->{$key}()->getRelated();
                /** @var Collection $models */
                $models = $model->newQuery()->findMany(collect($request->all()[$key] ?? [])->map(function ($data) use($model) {
                    return $data[$model->getKeyName()] ?? null;
                })->filter());
                foreach ($request->all()[$key] ?? [] as $index => $data) {
                    /** @var Model $model */
                    $model = $guest_request->{$key}()->getRelated();
                    if (isset($data[$model->getKeyName()])) $model = $models->find($data[$model->getKeyName()]) ?: $model;
                    if (isset($data['_action']) && in_array($data['_action'], [ 'delete', 'destroy' ])) {
                        if ($model->exists) $deletedModels[] = $model;
                        continue;
                    }
                    foreach ($rule as $k => $value) {
                        $attribute = last(explode('.*.', $k));
                        $k = str_replace('*', $index, $k);
                        if (Str::contains($value, ['file', 'image', 'mimetypes', 'mimes'])) {
                            if ($request->hasFile($k)) {
                                $model->{$attribute} = $request->file($k)->store($key);
                            } elseif ($request->exists($k)) {
                                $model->{$attribute} = $request->input($k);
                            }
                        } elseif ($request->exists($k)) {
                            $model->{$attribute} = $request->input($k);
                        }
                    }
                    $hasMany[$key][] = $model;
                }
            }
        }
        $hasOne = [];
        foreach (self::rules($request, $guest_request)['hasOne'] as $key => $rule) {
            if ($request->exists($key)) {
                /** @var Model $model */
                $model = $guest_request->{$key}()->getRelated();
                $data = $request->all()[$key];
                if (isset($data[$model->getKeyName()]))
                    $model = $model->newQuery()->find(($request->all()[$key] ?? [])[$model->getKeyName()] ?? null) ?: $model;
                if (isset($data['_action']) && in_array($data['_action'], [ 'delete', 'destroy' ])) {
                    if ($model->exists) $deletedModels[] = $model;
                    continue;
                }
                foreach ($rule as $k => $value) {
                    $attribute = last(explode('.', $k));
                    if (Str::contains($value, ['file', 'image', 'mimetypes', 'mimes'])) {
                        if ($request->hasFile($k)) {
                            $model->{$attribute} = $request->file($k)->store($key);
                        } elseif ($request->exists($k)) {
                            $model->{$attribute} = $request->input($k);
                        }
                    } elseif ($request->exists($k)) {
                        $model->{$attribute} = $request->input($k);
                    }
                }
                $hasOne[$key] = $model;
            }
        }

        try {
            DB::beginTransaction();
            $guest_request->save();
            foreach (self::rules($request, $guest_request)['hasMany'] as $key => $rule) {
                if (!$request->exists($key)) continue;
                $models = $guest_request->{$key}()->saveMany($hasMany[$key]);
                $guest_request->setRelation($key, new Collection($models));
            }
            foreach (self::rules($request, $guest_request)['hasOne'] as $key => $rule) {
                if (!$request->exists($key) || empty($hasOne[$key])) continue;
                $model = $guest_request->{$key}()->save($hasOne[$key]);
                $guest_request->setRelation($key, $model);
            }
            foreach ($deletedModels as $model) $model->delete();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new HttpException(500, $exception->getMessage(), $exception);
        }

        return new JsonResource($guest_request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GuestRequest  $guest_request
     * @return JsonResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     */
    public function destroy(GuestRequest $guest_request)
    {
        $this->authorize('delete', $guest_request);
        $guest_request->delete();

        return new JsonResource($guest_request);
    }
}
