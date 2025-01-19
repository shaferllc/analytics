<?php

namespace Shaferllc\Analytics\Models;

use App\Models\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Report extends Model
{

    use HasUlids;
    protected $fillable = [
        'site_id',
        'internationalization',
        'generated_at',
        'screenshot',
        'seo_score',
        'performance_score',
        'issues'
    ];

    protected $casts = [
        'internationalization' => 'array',
        'generated_at' => 'datetime',
        'screenshot' => 'string',
        'issues' => 'array'
    ];

    public function website()
    {
        return $this->belongsTo(Site::class);
    }
}
