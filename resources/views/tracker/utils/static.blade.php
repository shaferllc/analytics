// Public methods

static trackEvent(eventName, eventData) {

    TSMonitor.instance.queueRequest({
        name: 'custom_event',
        value: {
            eventName,
            eventData: utils.sanitizeEventData(eventData)
        }
    });
    utils.debugInfo('Custom event tracked:', eventName, eventData);
}

static abTest(testName, variants) {
    if (!TSMonitor.instance.abTests[testName]) {
        const selectedVariant = variants[Math.floor(crypto.getRandomValues(new Uint32Array(1))[0] / (0xffffffff + 1) * variants.length)];
        TSMonitor.instance.abTests[testName] = selectedVariant;
        TSMonitor.instance.queueRequest({
            name: 'ab_test_assignment',
            value: {
                testName,
                variant: selectedVariant
            }
        });
    }
    return TSMonitor.instance.abTests[testName];
}

static setUserId(id) {
        TSMonitor.instance.userId = id;
        TSMonitor.instance.queueRequest({
            name: 'user_identified',
            value: {
                userId: utils.hashUserId(id)
            }
        });
        utils.debugInfo('User ID set:', id);
}

    static startRecording() {
    if ( !TSMonitor.instance.isRecording) {
        TSMonitor.instance.isRecording = true;
        document.addEventListener('click', TSMonitor.instance.recordEvent);
        document.addEventListener('input', TSMonitor.instance.recordEvent);
        utils.debugInfo('Session recording started');
    }
}

static stopRecording() {
    if (TSMonitor.instance.isRecording) {
        TSMonitor.instance.isRecording = false;
        document.removeEventListener('click', TSMonitor.instance.recordEvent);
        document.removeEventListener('input', TSMonitor.instance.recordEvent);
        TSMonitor.instance.queueRequest({
            name: 'session_recording',
            value: TSMonitor.instance.recordedEvents
        });
        TSMonitor.instance.recordedEvents.length = 0; // Clear the array
        utils.debugInfo('Session recording stopped and data sent');
    }
}

static setUserProperties(properties) {
        TSMonitor.instance.queueRequest({
            name: 'set_user_properties',
            value: utils.sanitizeEventData(properties)
        });
        utils.debugInfo('User properties set:', properties);
}

static trackPageView(pageTitle, pageUrl) {
        TSMonitor.instance.queueRequest({
            name: 'page_view',
            value: {
                title: pageTitle || document.title,
                url: utils.anonymize.url(pageUrl || window.location.href)
            }
        });
        utils.debugInfo('Page view tracked:', pageTitle, pageUrl);
}

static trackError(errorMessage, errorDetails) {
        TSMonitor.instance.queueRequest({
            name: 'error',
            value: {
                message: errorMessage,
                details: errorDetails
            }
        });
        utils.debugInfo('Error tracked:', errorMessage, errorDetails);
}
static clearUserData() {
        TSMonitor.instance.userId = null;
        TSMonitor.instance.abTests = {};
        w.localStorage.removeItem('ts_monitor_session_id');
        w.localStorage.removeItem('ts_monitor_session_start');
        TSMonitor.instance.queueRequest({
            name: 'clear_user_data',
            value: { timestamp: new Date().toISOString() }
        });
        utils.debugInfo('User data cleared');
}

static setCustomDimension(dimensionName, value) {
        TSMonitor.instance.queueRequest({
            name: 'set_custom_dimension',
            value: {
                dimension: dimensionName,
                value: value
            }
        });
        utils.debugInfo('Custom dimension set:', dimensionName, value);
}

static trackTiming(category, variable, time, label) {
        TSMonitor.instance.queueRequest({
            name: 'timing',
            value: {
                category: category,
                variable: variable,
                time: time,
                label: label
            }
        });
        utils.debugInfo('Timing tracked:', category, variable, time, label);
}

static getConfig() {
    return { ...config };
}

static updateConfig(newConfig) {
    Object.assign(config, newConfig);
    utils.debugInfo('Config updated:', config);
}

static getSessionId() {
    return utils.getSessionId();
}
