@props(['breadcrumbs'])

@if($breadcrumbs)
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm rounded-xl p-2 shadow-sm border border-slate-200/60 dark:border-slate-700/60">
            @foreach($breadcrumbs as $breadcrumb)
                <li class="inline-flex items-center">
                    @if(!$loop->first)
                        <x-icon name="heroicon-o-chevron-right" class="w-4 h-4 text-slate-400 dark:text-slate-300 mx-1.5" />
                    @endif
                    <a href="{{ $breadcrumb['url'] }}" @class([
                        'inline-flex items-center text-sm font-medium transition-all duration-200 rounded-lg px-3 py-1.5',
                        'text-white bg-gradient-to-br from-emerald-500 to-teal-500 shadow-emerald-500/20 hover:shadow-emerald-500/30 hover:scale-105 hover:bg-gradient-to-br hover:from-emerald-600 hover:to-teal-600' => $loop->last,
                        'text-slate-600 dark:text-slate-300 hover:bg-slate-100/50 dark:hover:bg-slate-700/50 bg-slate-100/50 dark:bg-slate-700/50 hover:scale-105 transition-all duration-200 hover:shadow-slate-500/20' => !$loop->last
                    ])>
                        @if($loop->first)
                            <x-icon name="heroicon-o-home" class="w-4 h-4 mr-2 text-slate-400 dark:text-white" />
                        @elseif(isset($breadcrumb['icon']))
                            <x-icon :name="$breadcrumb['icon']" class="w-4 h-4 mr-2 text-slate-400 dark:text-white" />
                        @endif
                        <span class="truncate max-w-[150px] md:max-w-[200px] hover:max-w-none hover:whitespace-normal">{{ $breadcrumb['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ol>
    </nav>
@endif
