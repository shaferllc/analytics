<?php

namespace Shaferllc\Analytics\Pipelines;

use App\Models\Site;
use Illuminate\Support\Arr;
use Shaferllc\Analytics\Models\Meta;
use Shaferllc\Analytics\Models\Page;
use Shaferllc\Analytics\Models\Event;
use Shaferllc\Analytics\Models\Visitor;

trait PipelineTrait
{

    private function createParentEvent(Visitor $visitor, Site $site, Page $page, array $event)
    {
        return $visitor->events()->create([
            'site_id' => $site->id,
            'visitor_id' => $visitor->id,
            'page_id' => $page->id,
            'name' => Arr::get($event, 'name'),
            'timestamp' => Arr::get($event, 'timestamp', now()),
        ]);
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
        $parent = $parent_event->meta()->create([
            'itemable_id' => $parent_event->id,
            'itemable_type' => Event::class,
            'meta_type' => $key,
            'meta_data' => $value
        ]);

        $parent->meta_data->set([$key => $value]);
        $parent->save();

        return $parent;
    }

    private function processNestedValues(Event $parent_event, Meta $parent_meta, string $parent_key, array $values, string $prefix = '')
    {

        $values = array_filter($values, fn($value) => !is_null($value));

        foreach ($values as $key => $value) {
            $meta_key = $prefix ? $prefix . '_' . $key : $parent_key . '_' . $key;

            $child = $parent_meta->parentMeta()->create([
                'itemable_id' => $parent_event->id,
                'itemable_type' => Event::class,
                'metaable_type' => Meta::class,
                'metaable_id' => $parent_meta->id,
                'meta_type' => $meta_key,
                'parent_id' => $parent_meta->id,
                'parent_type' => $parent_meta->getMorphClass(),
            ]);

            $parent_meta->meta_data->set([$key => $value]);
            $parent_meta->save();

            if (is_array($value)) {
                $this->processNestedValues($parent_event, $child, $key, $value, $meta_key);
            } else {
                $child->meta_data->set([$key => $value]);
                $child->save();
            }

        }
    }
}
