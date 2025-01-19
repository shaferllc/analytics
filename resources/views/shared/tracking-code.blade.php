<div class="space-y-4">
    <div class="flex items-center space-x-2">
        <div class="flex-shrink-0">
            <svg class="w-5 h-5 text-cyan-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M6.28 5.22a.75.75 0 010 1.06L2.56 10l3.72 3.72a.75.75 0 01-1.06 1.06L.97 10.53a.75.75 0 010-1.06l4.25-4.25a.75.75 0 011.06 0zm7.44 0a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 01-1.06-1.06L17.44 10l-3.72-3.72a.75.75 0 010-1.06z" clip-rule="evenodd" />
            </svg>
        </div>
        <label for="i-tracking-code" class="text-gray-100 text-sm font-medium">{!! __('Add this tracking code to your website:', ['head' => '<code class="bg-gray-700/50 px-1.5 py-0.5 rounded text-cyan-300 font-mono">&lt;head&gt;</code>', 'body' => '<code class="bg-gray-700/50 px-1.5 py-0.5 rounded text-cyan-300 font-mono">&lt;body&gt;</code>']) !!}</label>
    </div>

    <div class="relative group">
        <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/10 via-blue-500/10 to-purple-500/10 rounded-lg blur"></div>
        <div class="relative">
            <textarea
                name="tracking_code"
                class="w-full p-4 bg-gray-900/90 border border-gray-700/50 rounded-lg text-cyan-300 font-mono text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 transition-all duration-300"
                id="i-tracking-code"
                rows="3"
                onclick="this.select();"
                readonly
                spellcheck="false">&lt;script data-host=&quot;{{ config('app.url') }}&quot; src=&quot;{{ config('settings.cdn_url') }}&quot; id=&quot;{{ $site->id }}&quot; async defer&gt;&lt;/script&gt;</textarea>

            <div class="absolute top-3 right-3">
                <button
                    class="inline-flex items-center px-4 py-2 bg-cyan-500 hover:bg-cyan-400 text-gray-900 text-sm font-semibold rounded-lg transition-all duration-300 transform hover:scale-105 hover:shadow-lg hover:shadow-cyan-500/25"
                    onclick="copyToClipboard()"
                    x-data="{ copied: false }"
                    x-on:click="copied = true; setTimeout(() => copied = false, 2000)"
                >
                    <svg x-show="!copied" class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M7 3.5A1.5 1.5 0 018.5 2h3.879a1.5 1.5 0 011.06.44l3.122 3.12A1.5 1.5 0 0117 6.622V12.5a1.5 1.5 0 01-1.5 1.5h-1v-3.379a3 3 0 00-.879-2.121L10.5 5.379A3 3 0 008.379 4.5H7v-1z" />
                        <path d="M4.5 6A1.5 1.5 0 003 7.5v9A1.5 1.5 0 004.5 18h7a1.5 1.5 0 001.5-1.5v-5.879a1.5 1.5 0 00-.44-1.06L9.44 6.439A1.5 1.5 0 008.378 6H4.5z" />
                    </svg>
                    <svg x-show="copied" class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                    </svg>
                    <span x-text="copied ? '{{ __('Copied!') }}' : '{{ __('Copy') }}'"></span>
                </button>
            </div>

            <script>
                function copyToClipboard() {
                    const textarea = document.getElementById('i-tracking-code');
                    textarea.select();
                    document.execCommand('copy');
                }
            </script>
        </div>
    </div>

    <div class="flex items-center space-x-2 text-gray-400 text-xs">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
        </svg>
        <span>{{ __('Place this code just before the closing') }} <code class="bg-gray-700/50 px-1.5 py-0.5 rounded font-mono">&lt;/body&gt;</code> {{ __('tag for optimal performance') }}</span>
    </div>
</div>
