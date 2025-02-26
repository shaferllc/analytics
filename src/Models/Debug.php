<?php

namespace Shaferllc\Analytics\Models;

use App\Models\Site;
use Illuminate\Database\Eloquent\Model;


class Debug extends Model
{

    protected $table = 'analytics_debug';
    protected $fillable = [
        'message',
        'site_id',
        'user_agent',
        'url',
        'session_id',
        'timestamp',
        'level'
    ];


    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
