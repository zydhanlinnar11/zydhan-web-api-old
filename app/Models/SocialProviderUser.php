<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SocialProviderUser extends Pivot
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'social_id', 'token', 'refresh_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'token', 'refresh_token',
    ];
}