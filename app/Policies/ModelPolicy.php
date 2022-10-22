<?php

namespace App\Policies;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;

/**
 * ModelPolicy Policy
 */
class ModelPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @param null $parent
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function index(User $user, $parent = null)
    {
        $allow = true;
        if ($parent) $allow = $allow && $user->can('view', $parent);
        return $allow;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Test  $model
     * @param null $parent
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, $model, $parent = null)
    {
        $allow = true;
        if ($parent) $allow = $allow && $user->can('view', $parent);
        return $allow;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @param null $parent
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, $parent = null)
    {
        $allow = true;
        if ($parent) $allow = $allow && $user->can('update', $parent);
        return $allow;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Test  $model
     * @param null $parent
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, $model, $parent = null)
    {
        $allow = true;
        if ($parent) $allow = $allow && $user->can('update', $parent);
        return $allow;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Test  $model
     * @param null $parent
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, $model, $parent = null)
    {
        $allow = true;
        if ($parent) $allow = $allow && $user->can('update', $parent);
        return $allow;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Test  $model
     * @param null $parent
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, $model, $parent = null)
    {
        $allow = true;
        if ($parent) $allow = $allow && $user->can('update', $parent);
        return $allow;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Test  $model
     * @param null $parent
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, $model, $parent = null)
    {
        $allow = true;
        if ($parent) $allow = $allow && $user->can('update', $parent);
        return $allow;
    }
}
