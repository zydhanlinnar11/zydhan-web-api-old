<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialProvider extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'client_id', 'client_secret'];

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($social_provider) {
            if (!$social_provider['id']) {
                $social_provider['id'] = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    /**
     * The users that belong to the social provider.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps()->withPivot('social_id', 'token', 'refresh_token');
    }
}
