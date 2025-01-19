<?php

namespace Shaferllc\Analytics\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SessionsCollection extends ResourceCollection
{

    public $preserveKeys = true;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'data' => $this->collection,
            'global_stats' => $this->additional['global_stats'] ?? null,
        ];
    }
}
