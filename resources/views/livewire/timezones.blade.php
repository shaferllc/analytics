<x-site :site="$site">
    <div class="space-y-8">
        <x-breadcrumbs :breadcrumbs="[
            ['url' => route('sites.analytics.overview', ['site' => $site->id]), 'label' => __('Analytics Dashboard')],
            ['url' => route('sites.analytics.timezones', ['site' => $site->id]), 'label' => __('Timezones'), 'icon' => 'heroicon-o-globe-americas']
        ]" />

        <div class="flex justify-end gap-4">
            <x-ts-dropdown position="bottom-end">
                <x-slot:action>
                    <x-ts-button x-on:click="show = !show" sm>{{ __('Time Range') }}</x-ts-button>
                </x-slot:action>
                <x-ts-dropdown.items wire:click="setTimeRange('today')" :active="$daterange === 'today'">{{ __('Today') }}</x-ts-dropdown.item>
                <x-ts-dropdown.items wire:click="setTimeRange('7d')" :active="$daterange === '7d'">{{ __('Last 7 Days') }}</x-ts-dropdown.item>
                <x-ts-dropdown.items wire:click="setTimeRange('30d')" :active="$daterange === '30d'">{{ __('Last 30 Days') }}</x-ts-dropdown.item>
                <x-ts-dropdown.items wire:click="setTimeRange('90d')" :active="$daterange === '90d'">{{ __('Last 90 Days') }}</x-ts-dropdown.item>
            </x-ts-dropdown>

            <x-ts-button wire:click="exportData" sm>
                <x-icon name="heroicon-o-arrow-down-tray" class="w-4 h-4 mr-2" />
                {{ __('Export') }}
            </x-ts-button>
        </div>

        <x-loading />
        @if($timezones->isNotEmpty())
            <div class="space-y-6">
                <div class="relative bg-white/90 dark:bg-slate-800/90 rounded-2xl shadow-lg border border-slate-200/60 dark:border-slate-700/60 p-6 overflow-hidden" x-data="{ isOpen: true }">
                    <div class="cursor-pointer" @click="isOpen = !isOpen">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div class="space-y-2 flex-initial">
                                <div class="flex items-center gap-4">
                                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                        <x-icon name="heroicon-o-globe-americas" class="w-6 h-6 text-blue-500" />
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-semibold text-slate-900 dark:text-slate-100">
                                            {{ __('Timezones') }}
                                        </h2>
                                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                            {{ __('Visitor Location Analysis') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 p-2 hover:bg-slate-100/50 dark:hover:bg-slate-700/20 rounded-lg transition-colors">
                                <div>
                                    <template x-if="isOpen">
                                        <x-icon name="heroicon-o-chevron-up" class="w-6 h-6 text-slate-400 bg-slate-100/50 dark:bg-slate-500/20 rounded-lg p-1" />
                                    </template>
                                    <template x-if="!isOpen">
                                        <x-icon name="heroicon-o-chevron-down" class="w-6 h-6 text-slate-400 bg-slate-100/50 dark:bg-slate-500/20 rounded-lg p-1" />
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <div id="timezone-map" class="h-[400px] bg-slate-50 dark:bg-slate-800 rounded-xl" x-data="timeZoneMap(@js($timezones))" wire:ignore>
                        </div>
                    </div>
                    <div x-show="isOpen" x-collapse>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            @foreach($timezones as $timezone)
                                <div class="relative bg-slate-50/50 dark:bg-slate-700/20 rounded-xl p-6" x-data="{ showDetails: false }">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center gap-3">
                                            <x-icon name="heroicon-o-globe-alt" class="w-6 h-6 text-slate-400" />
                                            <span class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $timezone['value'] }}</span>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-200">
                                            {{ number_format($timezone['unique_visitors']) }} {{ __('Users') }}
                                        </span>
                                    </div>

                                    <!-- Basic Stats - Always Visible -->
                                    <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                                        <div>
                                            <p class="text-slate-500 dark:text-slate-400">{{ __('First Seen') }}</p>
                                            <p class="font-medium text-slate-700 dark:text-slate-300">{{ Carbon\Carbon::parse($timezone['first_seen'])->format('M j, Y') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-slate-500 dark:text-slate-400">{{ __('Last Seen') }}</p>
                                            <p class="font-medium text-slate-700 dark:text-slate-300">{{ Carbon\Carbon::parse($timezone['last_seen'])->format('M j, Y') }}</p>
                                        </div>
                                    </div>

                                    <!-- Show More Button -->
                                    <button
                                        @click="showDetails = !showDetails"
                                        class="w-full py-2 px-4 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 bg-slate-100 dark:bg-slate-800/50 hover:bg-slate-200 dark:hover:bg-slate-700/50 rounded-lg transition-colors flex items-center justify-center gap-2"
                                    >
                                        <span x-text="showDetails ? '{{ __('Show Less') }}' : '{{ __('Show More') }}'"></span>
                                        <x-icon
                                            :name="'heroicon-o-chevron-down'"
                                            class="w-4 h-4 transition-transform"
                                            ::class="showDetails ? 'rotate-180' : ''"
                                        />
                                    </button>

                                    <!-- Detailed Content -->
                                    <div x-cloak x-show="showDetails" x-collapse class="mt-4 space-y-4">
                                        <!-- Visit Stats -->
                                        <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                                            <div class="grid grid-cols-3 gap-4">
                                                <div>
                                                    <div class="flex items-start space-x-3">
                                                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/20">
                                                            <x-icon name="heroicon-o-users" class="w-5 h-5 text-blue-500 dark:text-blue-400"/>
                                                        </div>
                                                        <div>
                                                            <p class="text-slate-500 dark:text-slate-400 text-xs">{{ __('Unique Visitors') }}</p>
                                                            <p class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ number_format($timezone['unique_visitor_count']) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="flex items-start space-x-3">
                                                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-900/20">
                                                            <x-icon name="heroicon-o-arrow-path" class="w-5 h-5 text-emerald-500 dark:text-emerald-400"/>
                                                        </div>
                                                        <div>
                                                            <p class="text-slate-500 dark:text-slate-400 text-xs">{{ __('Total Visits') }}</p>
                                                            <p class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ number_format($timezone['total_visits']) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="flex items-start space-x-3">
                                                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-purple-50 dark:bg-purple-900/20">
                                                            <x-icon name="heroicon-o-chart-bar" class="w-5 h-5 text-purple-500 dark:text-purple-400"/>
                                                        </div>
                                                        <div>
                                                            <p class="text-slate-500 dark:text-slate-400 text-xs">{{ __('Visits/Visitor') }}</p>
                                                            <p class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ number_format($timezone['visits_per_visitor'], 1) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Meta Data -->
                                        @if(!empty($timezone['meta_data']))
                                            <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-4">{{ __('Location Details') }}</p>
                                                <div class="grid grid-cols-2 gap-6">
                                                    @foreach($timezone['meta_data'] as $key => $meta)
                                                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4">
                                                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300 capitalize mb-2">
                                                                {{ str_replace('-', ' ', $key) }}
                                                            </p>
                                                            @if(isset($meta['most_common']))
                                                                <div class="flex items-center gap-2 mb-3">
                                                                    <span class="text-sm font-medium text-slate-900 dark:text-slate-100">
                                                                        {{ $meta['most_common'] }}
                                                                    </span>
                                                                    <span class="text-xs px-2 py-0.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded">
                                                                        {{ number_format($meta['most_common_percentage'] ?? 0, 1) }}%
                                                                    </span>
                                                                </div>
                                                                <div class="space-y-2">
                                                                    @foreach($meta['distribution'] ?? [] as $value => $count)
                                                                        <div class="flex gap-4 items-center text-xs">
                                                                            <span class="text-slate-600 dark:text-slate-400">{{ $value }}</span>
                                                                            <div class="flex-1 relative h-2 bg-slate-100 dark:bg-slate-700 rounded">
                                                                                <div class="absolute left-0 top-0 h-full bg-blue-200 dark:bg-blue-800 rounded"
                                                                                     style="width: {{ $meta['distribution_percentages'][$value] ?? 0 }}%"></div>
                                                                            </div>
                                                                            <span class="text-slate-500 dark:text-slate-500">
                                                                                {{ $count }} ({{ number_format($meta['distribution_percentages'][$value] ?? 0, 1) }}%)
                                                                            </span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <x-pagination :paginator="$timezones" type="compact"/>
                    </div>
                </div>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-20 bg-gradient-to-br from-slate-50 to-white dark:from-slate-900/90 dark:to-slate-800/90 rounded-2xl border border-dashed border-slate-200/60 dark:border-slate-700/60">
                <x-icon name="heroicon-o-globe-americas" class="w-12 h-12 text-slate-400 mb-4"/>
                <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-2">{{ __('No Timezone Data Available') }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Start Monitoring To Collect Location Data') }}</p>
            </div>
        @endif
    </div>
</x-site>

@push('scripts')
    <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/maps.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/geodata/worldTimeZoneAreasHigh.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/geodata/worldTimeZonesHigh.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/themes/material.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('timeZoneMap', (timezonesData) => ({
            chart: null,

            init() {
                this.createMap();
            },

            createMap() {
                // Themes
                am4core.useTheme(am4themes_material);
                am4core.useTheme(am4themes_animated);

                // Create map instance
                let chart = am4core.create("timezone-map", am4maps.MapChart);
                this.chart = chart;

                // Set map definition and projection
                chart.geodata = am4geodata_worldTimeZoneAreasHigh;
                chart.projection = new am4maps.projections.Miller();
                chart.panBehavior = "rotateLong";

                // Set up colors
                const interfaceColors = new am4core.InterfaceColorSet();
                const startColor = chart.colors.getIndex(0);

                // Create main polygon series
                let polygonSeries = chart.series.push(new am4maps.MapPolygonSeries());
                polygonSeries.useGeodata = true;
                polygonSeries.calculateVisualCenter = true;

                // Configure polygon appearance
                let polygonTemplate = polygonSeries.mapPolygons.template;
                polygonTemplate.fillOpacity = 0.6;
                polygonTemplate.nonScalingStroke = true;
                polygonTemplate.strokeWidth = 1;
                polygonTemplate.stroke = interfaceColors.getFor("background");
                polygonTemplate.strokeOpacity = 1;
                polygonTemplate.tooltipText = "{timezone}: {value} visitors";

                // Create hover state
                let hs = polygonTemplate.states.create("hover");
                hs.properties.fillOpacity = 0.8;

                // Add timezone bounds
                let boundsSeries = chart.series.push(new am4maps.MapPolygonSeries());
                boundsSeries.geodata = am4geodata_worldTimeZonesHigh;
                boundsSeries.useGeodata = true;
                boundsSeries.mapPolygons.template.fill = am4core.color(interfaceColors.getFor("alternativeBackground"));
                boundsSeries.mapPolygons.template.fillOpacity = 0.07;
                boundsSeries.mapPolygons.template.nonScalingStroke = true;
                boundsSeries.mapPolygons.template.strokeWidth = 0.5;
                boundsSeries.mapPolygons.template.strokeOpacity = 1;
                boundsSeries.mapPolygons.template.stroke = interfaceColors.getFor("background");

                // Process and set data
                const tzData = timezonesData?.data ? Object.values(timezonesData.data) : [];
                if (tzData.length > 0) {
                    polygonSeries.data = tzData.map(tz => ({
                        timezone: tz.value,
                        value: parseInt(tz.unique_visitors) || 0,
                        id: tz.value
                    }));
                }

                // Add heat rules
                polygonSeries.heatRules.push({
                    property: "fill",
                    target: polygonSeries.mapPolygons.template,
                    min: startColor.brighten(1),
                    max: startColor.brighten(-0.3)
                });

                // Add map controls
                chart.zoomControl = new am4maps.ZoomControl();

                // Add view switch button
                let button = chart.createChild(am4core.SwitchButton);
                button.align = "right";
                button.marginTop = 40;
                button.marginRight = 40;
                button.valign = "top";
                button.leftLabel.text = "Map";
                button.rightLabel.text = "Globe";

                button.events.on("toggled", function() {
                    chart.deltaLatitude = 0;
                    chart.deltaLongitude = 0;
                    chart.deltaGamma = 0;

                    if (button.isActive) {
                        chart.projection = new am4maps.projections.Orthographic;
                        chart.panBehavior = "rotateLongLat";
                    } else {
                        chart.projection = new am4maps.projections.Miller;
                        chart.panBehavior = "move";
                    }
                });
            },

            destroy() {
                if (this.chart) {
                    this.chart.dispose();
                }
            }
        }));
    });
    </script>
@endpush
