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
            this.abTests = {};
            this.avgRequestTime = 0;
            this.batchTimer = null;
            this.copyPasteCount = 0;
            this.countdownTimer = null;
            this.csrfToken = null;
            this.debugLevel = TSMonitorConfig.debugLevel || 'info';
            this.engagementScore = 0;
            this.eventsTracked = 0;
            this.excludedElements = [];
            this.isProcessingQueue = false;
            this.isRecording = false;
            this.lastActivity = Date.now();
            this.lastInteraction = Date.now();
            this.maxEventsPerSession = 1000;
            this.mouseMovements = [];
            this.pageVisits = 0;
            this.params = {};
            this.privacyMode = false;
            this.recordedEvents = [];
            this.requestQueue = [];
            this.requestsSent = 0;
            this.samplingRate = 100;
            this.sendRequestFailures = 0;
            this.sessionId = utils.getSessionId();
            this.sessionTimeout = 30 * 60 * 1000;
            this.trackingEnabled = true;
            this.userId = utils.getUserId();
            this.videoWatchTime = 0;
        }

        init() {
            const startSessionTime = new Date();

            if (TSMonitorConfig.browserDebug) {
                TSMonitor.toggleBrowserDebug(true);
            }

            utils.debugInfo('Initializing analytics');

            const queueEvent = (name, value, sendImmediately = false, type = 'event') =>
                this.queueRequest({name, value, sendImmediately, type});

            // Apply configuration from meta tags or global config
            this.applyConfiguration();

            // Queue essential tracking events
            this.queueEssentialEvents(startSessionTime);

            // Setup session end handlers
            this.setupSessionEndHandlers(startSessionTime);

            // Initialize event tracking
            w.addEventListener('load', () => {
                utils.initializeEventListeners(w.TSMonitorConfig.events);
                this.startBatchTimer();
            });

            // Initialize basic trackers
            this.initializeBasicTrackers();
        }

        applyConfiguration() {
            const config = {
                csrfToken: TSMonitorConfig.csrfToken ?? document.querySelector('meta[name="csrf-token"]')?.content,
                debugLevel: TSMonitorConfig.debugLevel || 'info',
                excludedElements: TSMonitorConfig.excludedElements ?? [],
                maxEventsPerSession: TSMonitorConfig.maxEventsPerSession ?? 1000,
                privacyMode: TSMonitorConfig.privacyMode ?? false,
                samplingRate: TSMonitorConfig.samplingRate ?? 100,
                sessionTimeout: TSMonitorConfig.sessionTimeout ?? 30 * 60 * 1000,
                trackingEnabled: TSMonitorConfig.trackingEnabled ?? true,
            };

            Object.assign(this, config);
        }

        queueEssentialEvents(startSessionTime) {

            const queueEvent = (name, value) => this.queueRequest({name, value});

            const sendPageData = async () => {
                    let pm;

                    try {
                        pm = await utils.getPerformanceMetrics();
                    } catch (e) {
                        pm = null;
                    }

                    queueEvent('page_data', {
                        page_title: utils.getPageTitle(),
                        page_description: utils.getPageDescription(),
                        page_keywords: utils.getPageKeywords(),
                        path: utils.getPagePath(),
                        canonical_url: utils.getCanonicalUrl(),
                        redirect_count: utils.getRedirectCount(),
                        charset: utils.getCharacterSet(),
                        robots_meta: utils.getRobotsMeta(),
                        hreflang_tags: utils.getHreflangTags(),
                        og_metadata: utils.getOgMetadata(),
                        twitter_metadata: utils.getTwitterMetadata(),
                        structured_data: utils.getStructuredData(),
                        last_modified: utils.getLastModified(),

                        // url_path: utils.getUrlPath(),
                    });

                    queueEvent('user_session_data', {
                        campaign: utils.getCampaign(),
                        landing_page: utils.getLandingPage(),
                        search_engine: utils.getSearchEngine(),
                        social_network: utils.getSocialNetwork(),
                        hash: utils.getPageHash(),
                        referrer: utils.getReferrer(),
                        query: utils.getPageQuery(),
                        city: utils.getCity(),
                        continent: utils.getContinent(),
                        country: utils.getCountry(),
                        region: utils.getRegion(),
                        ip_address: utils.getIpAddress(),
                        language: utils.getLanguage(),
                        time_zone: utils.getTimezone(),
                        start_time: startSessionTime instanceof Date ? startSessionTime : new Date(),
                        user_id: utils.getUserId(),
                        session_duration: utils.getSessionDuration(),
                        page_depth: utils.getPageDepth(),
                        navigation_type: utils.getNavigationType(),
                        performance_metrics: pm,
                        url_query: utils.getUrlQuery(),
                        load_time: utils.getLoadTime(),
                    });

                    queueEvent('browser_data', {
                        ...utils.getUserAgent(),
                        browser_name: utils.getBrowser(),
                        browser_version: utils.getBrowserVersion(),
                        engine_version: utils.getEngineVersion(),
                        color_scheme: utils.getPreferredColorScheme(),
                        reduced_motion: utils.getReducedMotionPreference(),
                        language: utils.getLanguage(),
                        timezone: utils.getTimezone(),
                        viewport_width: utils.getViewportWidth(),
                        viewport_height: utils.getViewportHeight(),
                        resolution: utils.getResolution(),
                        cpu_cores: utils.getCPUCores(),
                        device_brand: utils.getDeviceBrand(),
                        device_model: utils.getDeviceModel(),
                        device_memory: utils.getDeviceMemory(),
                        device_pixel_ratio: utils.getDevicePixelRatio(),
                        device_type: utils.getDeviceType(),
                        operating_system: utils.getOS(),
                        os_name: utils.getOSName(),
                        os_version: utils.getOSVersion(),
                        connection_speed: utils.getConnectionSpeed(),
                    });
            };

            sendPageData();


        }

        setupSessionEndHandlers(startSessionTime) {
            const handleSessionEnd = utils.debounce(() => {
                try {
                    const endSessionTime = new Date();

                    // Get comprehensive engagement metrics
                    const engagementMetrics = utils.getEngagementMetrics();

                    // Calculate final engagement score
                    const totalInteractions = utils.getTotalInteractions();
                    const sessionDuration = endSessionTime - startSessionTime;
                    const engagementScore = utils.calculateEngagementScore(
                        sessionDuration,
                        totalInteractions.total,
                        engagementMetrics.scrollDepth
                    );

                    this.queueRequest({
                        name: 'end_session',
                        value: {
                            end_time: endSessionTime,
                            exit_page: utils.getPagePath(),
                            session_duration: sessionDuration,
                            engagement_metrics: {
                                ...engagementMetrics,
                                interactions: totalInteractions,
                                score: engagementScore,
                                total_time: sessionDuration
                            },
                            engagement_score: engagementScore,
                            performance_metrics: utils.getPerformanceMetrics()
                        },
                        sendImmediately: true
                    });
                } catch (e) {
                    utils.debugError('Failed to send session end event:', e);
                }
            }, 100);

            ['beforeunload', 'unload', 'pagehide', 'visibilitychange', 'freeze'].forEach(event => {
                w.addEventListener(event, handleSessionEnd);
            });

            // Handle mobile app state changes
            if (document.addEventListener) {
                document.addEventListener("visibilitychange", () => {
                    if (document.visibilityState === 'hidden') {
                        handleSessionEnd();
                    }
                });
            }

            // Handle mobile back/forward navigation
            w.addEventListener('popstate', handleSessionEnd);

            // Handle tab closing
            w.addEventListener('beforeunload', (e) => {
                handleSessionEnd();
                // Allow normal unload to proceed
                delete e['returnValue'];
            });
        }

        initializeBasicTrackers() {
            utils.debugInfo('Initializing basic trackers');

            // Initialize interaction pattern analysis
            utils.analyzeInteractionPatterns(this.recordedEvents);

            // Initialize visibility tracking
            utils.trackElementVisibility();

            // Initialize engagement tracking
            utils.trackEngagement();
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
