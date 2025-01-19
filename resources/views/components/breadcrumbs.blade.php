@props(['breadcrumbs'])

@if($breadcrumbs)
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center">
            @foreach($breadcrumbs as $breadcrumb)
                <li class="inline-flex items-center">
                    @if(!$loop->first)
                        <div class="flex items-center">
                            <x-icon name="heroicon-o-chevron-right" class="w-5 h-5 text-gray-500 dark:text-gray-300 mx-2 transition-colors group-hover:text-blue-600 dark:group-hover:text-blue-400" />
                        </div>
                    @endif
                    <a href="{{ $breadcrumb['url'] }}" @class([
                        'group inline-flex items-center text-sm font-medium transition-all duration-200 rounded-full px-4 py-2',
                        'text-gray-700 dark:text-gray-50 bg-white dark:bg-gray-600 hover:bg-gray-50 dark:hover:bg-gray-500 border border-gray-50 dark:border-gray-500' => $loop->last,
                        'text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-50 hover:scale-105 bg-white dark:bg-gray-600 hover:bg-gray-50 dark:hover:bg-gray-500 border border-gray-50 dark:border-gray-500' => !$loop->last
                    ])>
                        @if($loop->first)
                            <x-icon name="heroicon-o-home" class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-300 transition-colors group-hover:text-blue-600 dark:group-hover:text-blue-400" />
                        @elseif(isset($breadcrumb['icon']))
                            <x-icon :name="$breadcrumb['icon']" class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-300 transition-colors group-hover:text-blue-600 dark:group-hover:text-blue-400" />
                        @endif
                        <span class="truncate max-w-[150px] md:max-w-[200px]">{{ $breadcrumb['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ol>
    </nav>
@endif
