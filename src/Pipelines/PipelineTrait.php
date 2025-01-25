<?php

namespace Shaferllc\Analytics\Pipelines;

use App\Models\Site;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Shaferllc\Analytics\Models\Meta;
use Shaferllc\Analytics\Models\Page;
use Shaferllc\Analytics\Models\Event;
use Shaferllc\Analytics\Models\Visitor;

trait PipelineTrait
{

    private function createParentEvent(Visitor $visitor, Site $site, Page $page, array $event)
    {
        $event = $page->events()->firstOrCreate([
            'name' => Arr::get($event, 'name'),
            'hash' => md5(json_encode(Arr::get($event, 'value'))),
        ], [
            'visit_count' => 0
        ]);

        $event->increment('visit_count');

        $event->page()->syncWithoutDetaching($page);
        $event->visitors()->syncWithoutDetaching($visitor);
        $visitor->events()->syncWithoutDetaching($event);

        return $event;
    }

    private function processEventValues(Event $parent_event, array $values)
    {
        $values = array_filter($values, fn($value) => !is_null($value) && (!is_array($value) || !empty($value)));


        foreach ($values as $key => $value) {
            if (!is_array($value)) {
                $this->createMetaRecord($parent_event, $key, $value);
            } else {
                $parent = $this->createMetaRecord($parent_event, $key, $value);
                $this->processNestedValues($parent_event, $parent, $key, $value);
            }
        }
    }

    private function createMetaRecord(Event $parent_event, string $key, $value)
    {
        $parent = $parent_event->meta()->firstOrCreate([
            'itemable_id' => $parent_event->id,
            'itemable_type' => Event::class,
            'meta_type' => $key,
            'meta_data' => $value
        ], [
            'visit_count' => 0
        ]);

        $parent->increment('visit_count');

        if($parent->wasRecentlyCreated) {
            $parent->meta_data->set([$key => $value]);
        } else {
            ray('first time');
        }

        $parent->save();

        return $parent;
    }

    private function processNestedValues(Event $parent_event, Meta $parent_meta, string $parent_key, array $values, string $prefix = '')
    {

        $values = array_filter($values, fn($value) => !is_null($value));

        foreach ($values as $key => $value) {
            $meta_key = $prefix ? $prefix . '_' . $key : $parent_key . '_' . $key;

            $child = Meta::firstOrCreate([
                'itemable_id' => $parent_event->id,
                'itemable_type' => Event::class,
                'metaable_type' => Meta::class,
                'metaable_id' => $parent_meta->id,
                'meta_type' => $meta_key,
                'parent_id' => $parent_meta->id,
                'parent_type' => $parent_meta->getMorphClass(),
            ], [
                'visit_count' => 0
            ]);

            $child->increment('visit_count');

            if($child->wasRecentlyCreated) {
                $child->meta_data->set([$key => $value]);
            }

            $child->save();

            if (is_array($value)) {
                $this->processNestedValues($parent_event, $child, $key, $value, $meta_key);
            } else {
                $child->meta_data->set([$key => $value]);
                $child->save();
            }

        }
    }
}
