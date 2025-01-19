
    @php
        $menu = [
            route('websites.analytics.index', ['website' => $site]) => [

                'title' => __('Dashboard')
            ]
        ];

    @endphp

    <div class="nav d-block text-truncate">
        @foreach ($menu as $key => $value)
            <li class="nav-item">
                <a class="nav-link d-flex px-4 @if (str_starts_with(request()->url(), $key) && !isset($value['menu'])) active @endif" @if(isset($value['menu'])) data-toggle="collapse" href="#sub-menu-{{ $key }}" role="button" @if (array_filter(array_keys($value['menu']), function ($url) { return str_starts_with(request()->url(), $url); })) aria-expanded="true" @else aria-expanded="false" @endif aria-controls="collapse-{{ $key }}" @else href="{{ $key }}" @endif>
                    <span class="sidebar-icon d-flex align-items-center">grid. view icon</span>
                    <span class="flex-grow-1 text-truncate">{{ $value['title'] }}</span>
                    @if (isset($value['menu'])) <span class="d-flex align-items-center ml-auto sidebar-expand">@include('icons.expand-more', ['class' => 'fill-current text-muted width-3 height-3'])</span> @endif
                </a>
            </li>

            @if (isset($value['menu']))
                <div class="collapse sub-menu @if (array_filter(array_keys($menu[$key]['menu']), function ($url) { return str_starts_with(request()->url(), $url); })) show @endif" id="sub-menu-{{ $key }}">
                    @foreach ($value['menu'] as $subKey => $subValue)
                        <a href="{{ $subKey }}" class="nav-link px-4 d-flex text-truncate @if (str_starts_with(request()->url(), $subKey)) active @endif">
                            <span class="sidebar-icon d-flex align-items-center">@include('icons.' . $subValue['icon'], ['class' => 'fill-current width-4 height-4'])</span>
                            <span class="flex-grow-1 text-truncate">{{ $subValue['title'] }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>

    <hr>

    @auth
        <div class="nav d-block text-truncate">
            @foreach($sites as $site)
                <li class="nav-item">
                    <a class="nav-link d-flex px-4 @if(request()->route()->parameter('id') == $site->domain) active @endif" href="{{ route('analytics.stats.overview', ['website' => $site, 'from' => $range['from'], 'to' => $range['to']]) }}">
                        <span class="sidebar-icon d-flex align-items-center"><img src="https://icons.duckduckgo.com/ip3/{{ $site->domain }}.ico" rel="noreferrer" class="width-4 height-4 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}"></span>
                        <span class="flex-grow-1 text-truncate" dir="ltr">{{ $site->domain }}</span>
                    </a>
                </li>
            @endforeach
        </div>
    @endauth

