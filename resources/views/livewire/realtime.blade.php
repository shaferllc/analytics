<div>

    <div wire:poll.{{ $interval }}s>
        
        <div class="bg-white dark:bg-gray-800 rounded-t-lg shadow-sm mb-3">
            <div class="px-3 border-b dark:border-gray-700">
                <div class="flex flex-col space-y-6">
                    <!-- Title Row -->
                    @include('analytics::livewire.partials.realtime.title')
                    <!-- Stats Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Visitors Card -->
                        @include('analytics::livewire.partials.realtime.visitors-card' )

                        <!-- Pageviews Card -->
                        @include('analytics::livewire.partials.realtime.pageviews-card')
                    </div>
                </div>
                include('analytics::livewire.partials.graph')  
            
            </div>

        
            @include('analytics::livewire.partials.realtime.recent', ['recent' => $this->recent])

        </div>
    </div>
</div>