<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Division;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * DivisionController
 * @extends Controller
 */
class DivisionController extends Controller
{
    /**
     * Rules
     * @param  \Illuminate\Http\Request|null $request
     * @param Division $division
     * @return array
     */
    public static function rules(Request $request = null, Division $division = null)
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
                'name' => 'required|string|max:255',
            ],
            'update' => [
                //'parent_id' => 'exists:parents,id',
                'name' => 'string|max:255',
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
        $divisions = Division::filter()
            ->paginate()->appends(request()->query());
        $this->authorize('index', 'App\Models\Division');

        return JsonResource::collection($divisions);
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
        $this->authorize('create', 'App\Models\Division');
        $request->validate(self::rules($request)['store']);
        foreach (self::rules($request)['hasMany'] as $key => $rule)
            $request->validate(array_merge([ $key => 'array' ], $rule));
        foreach (self::rules($request)['hasOne'] as $key => $rule)
            $request->validate(array_merge([ $key => 'array' ], $rule));

        $division = new Division;
        foreach (self::rules($request)['store'] as $key => $value) {
            if (Str::contains($value, [ 'file', 'image', 'mimetypes', 'mimes' ])) {
                if ($request->hasFile($key)) {
                    $division->{$key} = $request->file($key)->store('divisions');
                } elseif ($request->exists($key)) {
                    $division->{$key} = $request->{$key};
                }
            } elseif ($request->exists($key)) {
                $division->{$key} = $request->{$key};
            }
        }

        $deletedModels = [];
        $hasMany = [];
        foreach (self::rules($request)['hasMany'] as $key => $rule) {
            $hasMany[$key] = [];
            if ($request->exists($key)) {
                /** @var Model $model */
                $model = $division->{$key}()->getRelated();
                /** @var Collection $models */
                $models = $model->newQuery()->findMany(collect($request->all()[$key] ?? [])->map(function ($data) use($model) {
                    return $data[$model->getKeyName()] ?? null;
                })->filter());
                foreach ($request->all()[$key] ?? [] as $index => $data) {
                    /** @var Model $model */
                    $model = $division->{$key}()->getRelated();
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
                $model = $division->{$key}()->getRelated();
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
            $division->save();
            foreach (self::rules($request)['hasMany'] as $key => $rule) {
                if (!$request->exists($key)) continue;
                $models = $division->{$key}()->saveMany($hasMany[$key]);
                $division->setRelation($key, new Collection($models));
            }
            foreach (self::rules($request)['hasOne'] as $key => $rule) {
                if (!$request->exists($key) || empty($hasOne[$key])) continue;
                $model = $division->{$key}()->save($hasOne[$key]);
                $division->setRelation($key, $model);
            }
            foreach ($deletedModels as $model) $model->delete();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new HttpException(500, $exception->getMessage(), $exception);
        }

        return (new JsonResource($division))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Division  $division
     * @return JsonResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Division $division)
    {
        $this->authorize('view', $division);

        return new JsonResource($division);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Division  $division
     * @return JsonResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Division $division)
    {
        $this->authorize('update', $division);
        $request->validate(self::rules($request, $division)['update']);
        foreach (self::rules($request, $division)['hasMany'] as $key => $rule)
            $request->validate(array_merge([ $key => 'array' ], $rule));
        foreach (self::rules($request, $division)['hasOne'] as $key => $rule)
            $request->validate(array_merge([ $key => 'array' ], $rule));

        foreach (self::rules($request, $division)['update'] as $key => $value) {
            if (Str::contains($value, [ 'file', 'image', 'mimetypes', 'mimes' ])) {
                if ($request->hasFile($key)) {
                    $division->{$key} = $request->file($key)->store('divisions');
                } elseif ($request->exists($key)) {
                    $division->{$key} = $request->{$key};
                }
            } elseif ($request->exists($key)) {
                $division->{$key} = $request->{$key};
            }
        }

        $deletedModels = [];
        $hasMany = [];
        foreach (self::rules($request, $division)['hasMany'] as $key => $rule) {
            $hasMany[$key] = [];
            if ($request->exists($key)) {
                /** @var Model $model */
                $model = $division->{$key}()->getRelated();
                /** @var Collection $models */
                $models = $model->newQuery()->findMany(collect($request->all()[$key] ?? [])->map(function ($data) use($model) {
                    return $data[$model->getKeyName()] ?? null;
                })->filter());
                foreach ($request->all()[$key] ?? [] as $index => $data) {
                    /** @var Model $model */
                    $model = $division->{$key}()->getRelated();
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
        foreach (self::rules($request, $division)['hasOne'] as $key => $rule) {
            if ($request->exists($key)) {
                /** @var Model $model */
                $model = $division->{$key}()->getRelated();
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
            $division->save();
            foreach (self::rules($request, $division)['hasMany'] as $key => $rule) {
                if (!$request->exists($key)) continue;
                $models = $division->{$key}()->saveMany($hasMany[$key]);
                $division->setRelation($key, new Collection($models));
            }
            foreach (self::rules($request, $division)['hasOne'] as $key => $rule) {
                if (!$request->exists($key) || empty($hasOne[$key])) continue;
                $model = $division->{$key}()->save($hasOne[$key]);
                $division->setRelation($key, $model);
            }
            foreach ($deletedModels as $model) $model->delete();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new HttpException(500, $exception->getMessage(), $exception);
        }

        return new JsonResource($division);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Division  $division
     * @return JsonResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     */
    public function destroy(Division $division)
    {
        $this->authorize('delete', $division);
        $division->delete();

        return new JsonResource($division);
    }
}
