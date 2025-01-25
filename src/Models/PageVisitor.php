<?php

declare(strict_types=1);

namespace Shaferllc\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

class PageVisitor extends Pivot
{
    protected $table = 'analytics_page_visitors';

    public $incrementing = true;

    protected $fillable = [
        'visitor_id',
        'page_id',
        'last_visit_at',
        'first_visit_at',
        'total_visits',
    ];

    protected $casts = [
        'last_visit_at' => 'datetime',
        'first_visit_at' => 'datetime',
        'total_visits' => 'int',
    ];
}
