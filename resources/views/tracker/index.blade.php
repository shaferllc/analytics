(function (w) {
    'use strict';

    // Configuration
    {!! view('analytics::tracker.config.config')->render() !!}
    
    // Utility functions
    {!! view('analytics::tracker.utils.utilities')->render() !!}
    
    // Request parameters
    const requestParams = {
        browser: utils.getBrowser(),
        browser_version: utils.getBrowserVersion(),
        os: utils.getOS(),
        city: utils.getCity(),
        country: utils.getCountry(),
        device: /Mobile|iP(hone|od|ad)|Android|BlackBerry|IEMobile|Kindle|NetFront|Silk-Accelerated|(hpw|web)OS|Fennec|Minimo|Opera M(obi|ini)|Blazer|Dolfin|Dolphin|Skyfire|Zune/.test(navigator.userAgent) ? 'mobile' : 'desktop',
        ip: utils.getIpAddress(),
        language: utils.getLanguage(),
        page: w.location.href,
        page_title: document.title,
        referrer: w.document.referrer,
        session_id: utils.getSessionId(),
        timestamp: new Date().toISOString(),
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
        url_path: utils.anonymize.url(w.location.pathname === '/' ? w.location.href : w.location.pathname),
        url_query: w.location.search,
        user_agent: utils.getUserAgent(),
    };

    // Event handlers
    {!! view('analytics::tracker.utils.handlers')->render() !!}

    // Main RadMonitor class
    class RadMonitor {
        constructor() {
            this.params = {};
            this.sessionId = null;
            this.lastActivity = Date.now();
            this.requestQueue = [];
            this.isProcessingQueue = false;
            this.sendRequestFailures = 0;
            this.isTrackingAllowed = false;
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
            if (config.debug) {
                RadMonitor.toggleDebug(true, 'browser');
            }
            utils.debugLog('Checking Do Not Track');

            this.isTrackingAllowed = config.requireConsent ? this.checkConsent() : true;
            utils.debugLog('Checking consent:', this.isTrackingAllowed);

            utils.debugLog('isTrackingAllowed:', this.isTrackingAllowed);

            //if (this.isTrackingAllowed) {
                utils.debugLog('Initializing analytics');

                const queueEvent = (name, value) => this.queueRequest({ name, value });

                w.addEventListener('popstate', () => queueEvent('pop_state'));
                w.addEventListener('load', () => {
                    utils.initializeEventListeners(config.events);
                    this.startBatchTimer();
                });

                const originalPushState = history.pushState;
                history.pushState = (...args) => {
                    const referrer = utils.anonymize.url(w.location.href);
                    originalPushState.apply(history, args);
                    queueEvent('push_state', referrer);
                };

                queueEvent('pageview');

                this.trackTimeOnPage();

                utils.debugLog('Tracking exit rate');
                this.trackExitRate();

                utils.debugLog('Processing batched requests');
                this.processBatchedRequests();

                utils.debugLog('Sending offline requests');
                this.sendOfflineRequests();
           // }
        }

       

        // Request parameters
        {!! view('analytics::tracker.utils.request')->render() !!}

        // Trackers
        {!! view('analytics::tracker.utils.trackers')->render() !!}

        // Consent
        {!! view('analytics::tracker.utils.consent')->render() !!}

        // functions
        {!! view('analytics::tracker.utils.functions')->render() !!}

        // Static
        {!! view('analytics::tracker.utils.static')->render() !!}

        // Debugger
        {!! view('analytics::tracker.utils.debugger')->render() !!}

    }

    // Initialize RadMonitor
    RadMonitor.instance = new RadMonitor();
    RadMonitor.instance.init();

    // Expose RadMonitor to the global scope
    w.RadMonitor = RadMonitor;

})(window);

// Example usage of RadMonitor functions
// RadMonitor.toggleDebug(true);
// RadMonitor.toggleDebug(true, 'browser');
// RadMonitor.trackEvent('button_click', { buttonId: 'submit-form' });
// RadMonitor.abTest('homepage_layout', ['A', 'B']);
// RadMonitor.setUserId('user123');
// RadMonitor.startRecording();
// RadMonitor.stopRecording();


// // Usage examples for RadMonitor

// // 1. Track a custom event
// RadMonitor.trackEvent('product_view', { productId: 'ABC123', category: 'Electronics' });

// // 2. Set up an A/B test
// const variant = RadMonitor.abTest('pricing_page', ['original', 'discount', 'free_shipping']);
// console.log('User is in variant:', variant);

// // 3. Set a user ID for the current session
// RadMonitor.setUserId('user_789');

// // 4. Start session recording
// RadMonitor.startRecording();

// // 5. Stop session recording after some user interactions
// setTimeout(() => {
//     RadMonitor.stopRecording();
// }, 60000); // Stop recording after 1 minute

// RadMonitor.setUserPropertitoISOStringes({ userType: 'premium', accountAge: 365 });
// RadMonitor.trackPageView('Custom Page Title', 'https://example.com/custom-page');
// RadMonitor.trackError('API Error', { status: 500, message: 'Internal Server Error' });
// RadMonitor.setConsentStatus(true);
// console.log('Consent Status:', RadMonitor.getConsentStatus());
// RadMonitor.clearUserData();
// console.log('Current Session ID:', RadMonitor.getSessionId());
// RadMonitor.setCustomDimension('userSegment', 'highValue');
// RadMonitor.trackTiming('apiCall', 'getUserData', 250, 'GET /api/user');
// console.log('Current Config:', RadMonitor.getConfig());
// RadMonitor.updateConfig({ debug: true, heatMap: true })
