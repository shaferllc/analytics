(function (w) {
    'use strict';


    // Configuration
    {!! view('analytics::tracker.config.config')->render() !!}

    // Utility functions
    {!! view('analytics::tracker.utils.utilities')->render() !!}


    // Event handlers
    {!! view('analytics::tracker.utils.handlers')->render() !!}

    // Main TSMonitor class
    class TSMonitor {
        constructor() {
            this.params = {};
            this.sessionId = null;
            this.lastActivity = Date.now();
            this.requestQueue = [];
            this.isProcessingQueue = false;
            this.sendRequestFailures = 0;
            this.abTests = {};
            this.userId = null;
            this.isRecording = false;
            this.requestsSent = 0;
            this.eventsTracked = 0;
            this.recordedEvents = [];
            this.batchTimer = null;
            this.countdownTimer = null;
            this.engagementScore = 0;
            this.pageVisits = 0;
            this.lastInteraction = Date.now();
            this.mouseMovements = [];
            this.copyPasteCount = 0;
            this.videoWatchTime = 0;
            this.avgRequestTime = 0;
        }

        init() {

            const startSessionTime = new Date();

            if (TSMonitorConfig.browserDebug) {
                TSMonitor.toggleDebug(true);
            }

            if (TSMonitorConfig.debug) {
                TSMonitor.toggleDebug(true, 'js');
            }

            utils.debugLog('Initializing analytics');

            const queueEvent = (name, value, sendImmediately = false, type = 'event') => this.queueRequest({name, value, sendImmediately, type });

            queueEvent('start_session', {
                iso_time: startSessionTime.toISOString(),
                unix_timestamp: Math.floor(startSessionTime.getTime() / 1000),
                utc_string: startSessionTime.toUTCString(),
                locale_string: startSessionTime.toLocaleString(),
                start_time: startSessionTime,
                user_id: utils.getUserId(),
                time_zone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                language: utils.getBrowserLanguage(),
                city: utils.getCity(),
                continent: utils.getContinent(),
                country: utils.getCountry(),
            });


            queueEvent('page_data', {
                query: utils.getPageQuery(),
                // url: utils.getPageUrl(),
                // path: utils.getPagePath(),
                hash: utils.getPageHash(),
                referrer: utils.getReferrer(),
                redirect_count: utils.getRedirectCount(),
                page_title: utils.getPageTitle(),
                page_description: utils.getPageDescription(),
                page_keywords: utils.getPageKeywords(),
                canonical_url: utils.getCanonicalUrl(),
                og_metadata: utils.getOgMetadata(),
                twitter_metadata: utils.getTwitterMetadata(),
                structured_data: utils.getStructuredData(),
                hreflang_tags: utils.getHreflangTags(),
                robots_meta: utils.getRobotsMeta(),
            });

            queueEvent('traffic_source_data', {
                campaign: utils.getCampaign() || null,
                landing_page: utils.getLandingPage(),
                search_engine: utils.getSearchEngine() || null,
                social_network: utils.getSocialNetwork() || null,
            });

            queueEvent('browser_data', {
                user_agent: utils.getUserAgent(),
                browser: utils.getBrowser() + ' ' + utils.getBrowserVersion(),
                browser_language: utils.getBrowserLanguage(),
                color_scheme: utils.getPreferredColorScheme(),
                device_pixel_ratio: utils.getDevicePixelRatio(),
                reduced_motion: utils.getReducedMotionPreference(),
                viewport_width: utils.getViewportWidth(),
            });

           // queueEvent('performance_metrics', {
           //     current_fps: utils.getCurrentFPS(),
           //     page_load_metrics: utils.getPageLoadMetrics(),
           //     largest_contentful_paint: utils.getLargestContentfulPaint(),
           //     first_input_delay: utils.getFirstInputDelay(),
           //     cumulative_layout_shift: utils.getCumulativeLayoutShift(),
           //     resource_timing: utils.getResourceTiming(),
           //     load_time: utils.getLoadTime(),
           //     network_latency: utils.getNetworkLatency(),
           //     page_load_time: utils.getPageLoadTime(),
           // });

          //  queueEvent('user_interaction_data', {
          //      element_z_level: utils.getElementZLevel(),
          //      scroll_direction: utils.getScrollDirection(),
          //      scroll_speed: utils.getScrollSpeed(),
          //      scroll_depth: utils.getScrollDepth(),
          //      navigation_type: utils.getNavigationType(),
          //      redirect_count: utils.getRedirectCount(),
          //      page_depth: utils.getPageDepth(),
          //  });

           // queueEvent('device_data', {
           //     battery_status: utils.getBatteryStatus(),
           //     cpu_cores: utils.getCPUCores(),
           //     device: utils.getDevice(),
           //     device_type: utils.getDeviceType(),
           //     os: utils.getOS(),
           //     memory_usage: utils.getMemoryUsage(),
           //     screen_locked: utils.isScreenLocked(),
           //     resolution: utils.getResolution(),
           //     memory: utils.getDeviceMemory(),
           //     connection_speed: utils.getConnectionSpeed(),
           // });

            // Use both beforeunload and unload events for better coverage
            const handleSessionEnd = () => {

                try {
                    const endSessionTime = new Date();

                    queueEvent('end_session', {
                        iso_time: endSessionTime.toISOString(),
                        unix_timestamp: Math.floor(endSessionTime.getTime() / 1000),
                        utc_string: endSessionTime.toUTCString(),
                        locale_string: endSessionTime.toLocaleString(),
                        end_time: endSessionTime,
                        total_duration_seconds: Math.abs(Math.round(endSessionTime - startSessionTime)),
                        total_duration: Math.abs(endSessionTime - startSessionTime),
                        page_url: utils.getPageUrl(),
                        exit_page: utils.getPagePath()
                    }, true);

                } catch (e) {
                    console.error('Failed to send session end event:', e);
                }
            };

            w.addEventListener('beforeunload', handleSessionEnd);
            w.addEventListener('unload', handleSessionEnd);
            w.addEventListener('pagehide', handleSessionEnd);

            w.addEventListener('load', () => {
                utils.initializeEventListeners(w.TSMonitorConfig.events);
                this.startBatchTimer();
            });

            utils.debugLog('Track Ad Clicks');
            this.trackAdClicks();

            utils.debugLog('Tracking outbound links');
            this.trackOutboundLinks();

            utils.debugLog('Tracking viewport size');
            this.trackViewport();

            // utils.debugLog('Tracking tab visibility');
            // this.trackTabVisibility();

            // utils.debugLog('Tracking Gutenberg editor interactions');
            // this.trackGutenbergEditorInteractions();

         //  utils.debugLog('Tracking GPU errors');
         //  this.trackGPUInternalErrors();

         //  utils.debugLog('Tracking GPU out of memory errors');
         //  this.trackGPUOutOfMemoryError();

         //  utils.debugLog('Tracking GPU pipeline errors');
         //  this.trackGPUPipelineError();

         //  utils.debugLog('Tracking GPU validation errors');
         //  this.trackGPUValidationError();

         //  utils.debugLog('Tracking GPU uncaptured errors');
         //  this.trackGPUUncapturedError();

         //  utils.debugLog('Tracking GPU errors');
         //  this.trackGPUErrors();

         //  utils.debugLog('Tracking scroll depth');
         //  this.trackScrollDepth();

         //  utils.debugLog('Tracking file downloads');
         //  this.trackFileDownloads();

         //  utils.debugLog('Tracking form interactions');
         //  this.trackFormInteractions();

         //  utils.debugLog('Tracking search interactions');
         //  this.trackSearchInteractions();

         //  utils.debugLog('Tracking heatmap clicks');
         //  this.trackHeatmapClicks();

         //  utils.debugLog('Tracking user frustration');
         //  this.trackUserFrustration();

         //  utils.debugLog('Tracking form submissions');
         //  this.trackFormSubmissions(); //todo

         //  utils.debugLog('Tracking media interactions');
         //  this.trackMediaInteractions(); //todo

         //  utils.debugLog('Tracking exit rate');
         //  this.trackExitRate();

         // // utils.debugLog('Tracking geolocation errors');
         // // this.trackGeolocationErrors();

         //  utils.debugLog('Tracking bounce rate');
         //  this.trackBounceRate(); //todo

         //  utils.debugLog('Tracking video watching');
         //  this.trackVideoWatching(); //todo

         //  utils.debugLog('Tracking copy/paste');
         //  this.trackCopyPaste();

         //  utils.debugLog('Tracking mouse movements');
         //  this.trackMouseMovements();

            //     utils.debugLog('Tracking user inactivity');
            //     this.trackUserInactivity(); //todo

            //     utils.debugLog('Tracking engagement');
            //     // this.trackEngagement(); //todo

                // utils.debugLog('Tracking JS errors');
                // this.trackJSErrors();

                // utils.debugLog('Sending offline requests');
                // this.sendOfflineRequests();
           // }
        }



        // Request parameters
        {!! view('analytics::tracker.utils.request')->render() !!}

        // Trackers
        {!! view('analytics::tracker.utils.trackers')->render() !!}

        // functions
        {!! view('analytics::tracker.utils.functions')->render() !!}

        // Static
        {!! view('analytics::tracker.utils.static')->render() !!}

        // Debugger
        {!! view('analytics::tracker.utils.debugger')->render() !!}

    }

    // Initialize TSMonitor
    TSMonitor.instance = new TSMonitor();
    TSMonitor.instance.init();

    // Expose TSMonitor to the global scope
    w.TSMonitor = TSMonitor;

})(window);

// Example usage of TSMonitor functions
// TSMonitor.toggleDebug(true);
// TSMonitor.toggleDebug(true, 'browser');
// TSMonitor.trackEvent('button_click', { buttonId: 'submit-form' });
// TSMonitor.abTest('homepage_layout', ['A', 'B']);
// TSMonitor.setUserId('user123');
// TSMonitor.startRecording();
// TSMonitor.stopRecording();


// // Usage examples for TSMonitor

// // 1. Track a custom event
// TSMonitor.trackEvent('product_view', { productId: 'ABC123', category: 'Electronics' });

// // 2. Set up an A/B test
// const variant = TSMonitor.abTest('pricing_page', ['original', 'discount', 'free_shipping']);
// console.log('User is in variant:', variant);

// // 3. Set a user ID for the current session
// TSMonitor.setUserId('user_789');

// // 4. Start session recording
// TSMonitor.startRecording();

// // 5. Stop session recording after some user interactions
// setTimeout(() => {
//     TSMonitor.stopRecording();
// }, 60000); // Stop recording after 1 minute

// TSMonitor.setUserPropertitoISOStringes({ userType: 'premium', accountAge: 365 });
// TSMonitor.trackPageView('Custom Page Title', 'https://example.com/custom-page');
// TSMonitor.trackError('API Error', { status: 500, message: 'Internal Server Error' });
// TSMonitor.setConsentStatus(true);
// console.log('Consent Status:', TSMonitor.getConsentStatus());
// TSMonitor.clearUserData();
// console.log('Current Session ID:', TSMonitor.getSessionId());
// TSMonitor.setCustomDimension('userSegment', 'highValue');
// TSMonitor.trackTiming('apiCall', 'getUserData', 250, 'GET /api/user');
// console.log('Current Config:', TSMonitor.getConfig());
// TSMonitor.updateConfig({ debug: true, heatMap: true })
