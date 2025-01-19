<?php

namespace Shaferllc\Analytics\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SessionsResource extends JsonResource
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
            'id' => $this->id,
            'session_id' => $this->session_id,
            'timezone' => $this->timezone,
            'language' => $this->language,
            'city' => $this->meta_data->city,
            'country' => $this->meta_data->country,
            'continent' => $this->meta_data->continent,
            'total_pages' => $this->total_pages,
            'total_duration' => $this->total_duration,
            'total_time_spent' => $this->total_time_spent,
            'total_visits' => $this->total_visits,
            'avg_time_per_page' => $this->avg_time_per_page,
            'first_visit' => $this->first_visit,
            'last_visit' => $this->last_visit,
            'session_duration' => $this->session_duration,
            'pages' => $this->pages->map(function ($page) {
                return [
                    'id' => $page->id,
                    'page' => $page->page,
                    'created_at' => $page->created_at,
                    'updated_at' => $page->updated_at,
                    'path' => $page->path,
                    'title' => $page->meta_data->title,
                    'charset' => $page->meta_data->charset,
                    'visit_count' => $page->meta_data->visit_count,
                    'visitor_id' => $page->page_visitor->visitor_id,
                    'page_id' => $page->page_visitor->page_id,
                    'site_id' => $page->page_visitor->site_id,
                    'start_session_at' => $page->page_visitor->start_session_at,
                    'end_session_at' => $page->page_visitor->end_session_at,
                    'total_duration_seconds' => $page->page_visitor->total_duration_seconds,
                    'total_time_spent' => $page->page_visitor->total_time_spent,
                    'total_visits' => $page->page_visitor->total_visits,
                    'last_visit_at' => $page->page_visitor->last_visit_at,
                    'first_visit_at' => $page->page_visitor->first_visit_at,
                    'avg_time_on_page' => $page->page_visitor->total_visits > 0
                        ? $page->page_visitor->total_duration_seconds / $page->page_visitor->total_visits
                        : 0,
                    'bounce_rate' => $page->page_visitor->total_duration_seconds < 10 ? 100 : 0,
                ];
            }),
            'global_stats' => $this->additional['global_stats'] ?? null,
        ];
    }
}
