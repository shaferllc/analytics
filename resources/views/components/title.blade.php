@props([
    'firstIcon' => null,
    'lastIcon' => null,
    'icon' => null,
    'title' => null,
    'totalPageviews' => null,
    'totalText' => null,
    'dateRange' => null,
    'first' => null,
    'last' => null,
    'website' => null,
    'data' => null,
    'page' => null,
    'search' => null,
    'sortBy' => null,
    'sort' => null,
    'perPage' => null,
    'from' => null,
    'to' => null,
])
    <div>
        <div class="flex flex-col md:flex-row items-center justify-between bg-gradient-to-r from-blue-600 to-purple-700 p-6 rounded-xl shadow-2xl space-y-4 md:space-y-0">
            <div class="flex items-center space-x-4">
                <div class="bg-white bg-opacity-20 p-3 rounded-full">
                    <x-icon :name="$icon" class="w-10 h-10 text-white animate-pulse" />
                </div>
                <h2 class="text-3xl font-extrabold text-white tracking-wider">{{ $title }}</h2>
            </div>
            <div class="flex flex-col items-center md:items-end space-y-2">
                <div class="text-white text-opacity-80 text-sm font-medium">
                    {{ $totalText }}
                </div>
                @if($totalPageviews)
                    <div class="text-3xl font-bold text-white text-shadow text-center w-full">
                        {{ Number::format($totalPageviews) }}
                    </div>
                @endif
            </div>
            <div class="w-full md:w-auto">
                @include('analytics::stats.date_filter')
            </div>
        </div>

        <x-analytics::popular
            :first="$first"
            :last="$last"
            :website="$website"
            :firstIcon="$firstIcon"
            :lastIcon="$lastIcon"
        />


    @if($data)
        <div class="my-4">
            @if($data->hasPages())
                @include('analytics::stats.filters', ['data' => $data])
            @endif
        </div>
    @endif
</div>
