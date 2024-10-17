<?php

namespace ShaferLLC\Analytics\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use ShaferLLC\Analytics\Models\Plan;
use ShaferLLC\Analytics\Models\Website;
use ShaferLLC\Analytics\Models\Stat;
use ShaferLLC\Analytics\Models\Recent;

/**
 * Class User
 *
 * @mixin Builder
 * @package ShaferLLC\Analytics\Models
 */
class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'billing_information' => 'object',
        'email_verified_at' => 'datetime',
        'plan_created_at' => 'datetime',
        'plan_recurring_at' => 'datetime',
        'plan_ends_at' => 'datetime',
        'plan_trial_ends_at' => 'datetime',
        'tfa_code_created_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function plan()
    {
        if ($this->planIsDefault() || !$this->planIsActive()) {
            $this->plan_id = 1;
        }

        return $this->belongsTo(Plan::class)->withTrashed();
    }

    public function websites()
    {
        return $this->hasMany(Website::class);
    }

    public function stats()
    {
        return $this->hasManyThrough(Stat::class, Website::class);
    }

    public function recents()
    {
        return $this->hasManyThrough(Recent::class, Website::class);
    }
}
