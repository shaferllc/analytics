<label for="i-tracking-code" class="text-gray-600 text-sm">{!! __('Include this code in the :head or :body section of your website.', ['head' => '<code class="bg-gray-100 px-1 rounded">&lt;head&gt;</code>', 'body' => '<code class="bg-gray-100 px-1 rounded">&lt;body&gt;</code>']) !!}</label>
<div class="relative mt-2">
    <textarea name="tracking_code" class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" id="i-tracking-code" rows="3" onclick="this.select();" readonly>
&lt;script data-host=&quot;{{ config('app.url') }}&quot; data-dnt=&quot;{{ config('settings.do_not_track') ? 'true' : 'false' }}&quot; src=&quot;{{ !empty(config('settings.cdn_url')) ? config('settings.cdn_url') : asset('js/script.js') }}&quot; id=&quot;{{ $website->id }}&quot; async defer&gt;&lt;/script&gt;
    </textarea>

    <div class="absolute top-2 right-2">
        <button class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold py-1 px-3 rounded-md transition duration-300 ease-in-out" data-tooltip-copy="true" title="{{ __('Copy') }}" data-text-copy="{{ __('Copy') }}" data-text-copied="{{ __('Copied') }}" data-clipboard="true" data-clipboard-target="#i-tracking-code">
            {{ __('Copy') }}
        </button>
    </div>
</div>