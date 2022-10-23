<?php

namespace App\Models;

use Atnic\LaravelGenerator\Traits\HasExtendedAttributes;
use Atnic\LaravelGenerator\Traits\ResolveRouteBindingWithFilter;
use Atnic\LaravelGenerator\Traits\SetterGetterExtendedAttribute;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Smartisan\Filters\Traits\Filterable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasFactory, Filterable, HasExtendedAttributes, ResolveRouteBindingWithFilter, SetterGetterExtendedAttribute;

    protected $filters = 'App\Filters\UserFilter';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function division(){
    return $this->belongsTo(Division::Class);
    }
    public function guest_request(){
        return $this->hasMany(GuestRequest::Class);
    }
}
