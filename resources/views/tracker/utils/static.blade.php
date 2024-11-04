// Public methods

static trackEvent(eventName, eventData) {
    if (!RadMonitor.instance.isTrackingAllowed) {
        utils.debugLog('Tracking not allowed. Custom event not tracked.');
        return;
    }
    RadMonitor.instance.queueRequest({
        name: 'custom_event',
        value: {
            eventName,
            eventData: utils.sanitizeEventData(eventData)
        }
    });
    utils.debugLog('Custom event tracked:', eventName, eventData);
}

static abTest(testName, variants) {
    if (!RadMonitor.instance.abTests[testName]) {
        const selectedVariant = variants[Math.floor(crypto.getRandomValues(new Uint32Array(1))[0] / (0xffffffff + 1) * variants.length)];
        RadMonitor.instance.abTests[testName] = selectedVariant;
        RadMonitor.instance.queueRequest({
            name: 'ab_test_assignment',
            value: {
                testName,
                variant: selectedVariant
            }
        });
    }
    return RadMonitor.instance.abTests[testName];
}

static setUserId(id) {
    if (RadMonitor.instance.isTrackingAllowed) {
        RadMonitor.instance.userId = id;
        RadMonitor.instance.queueRequest({
            name: 'user_identified',
            value: {
                userId: utils.hashUserId(id)
            }
        });
        utils.debugLog('User ID set:', id);
    } else {
        utils.debugLog('Tracking not allowed. User ID not set.');
    }
}

static startRecording() {
    if (RadMonitor.instance.isTrackingAllowed && !RadMonitor.instance.isRecording) {
        RadMonitor.instance.isRecording = true;
        document.addEventListener('click', RadMonitor.instance.recordEvent);
        document.addEventListener('input', RadMonitor.instance.recordEvent);
        utils.debugLog('Session recording started');
    }
}

static stopRecording() {
    if (RadMonitor.instance.isRecording) {
        RadMonitor.instance.isRecording = false;
        document.removeEventListener('click', RadMonitor.instance.recordEvent);
        document.removeEventListener('input', RadMonitor.instance.recordEvent);
        RadMonitor.instance.queueRequest({
            name: 'session_recording',
            value: RadMonitor.instance.recordedEvents
        });
        RadMonitor.instance.recordedEvents.length = 0; // Clear the array
        utils.debugLog('Session recording stopped and data sent');
    }
}

static setUserProperties(properties) {
    if (RadMonitor.instance.isTrackingAllowed) {
        RadMonitor.instance.queueRequest({
            name: 'set_user_properties',
            value: utils.sanitizeEventData(properties)
        });
        utils.debugLog('User properties set:', properties);
    } else {
        utils.debugLog('Tracking not allowed. User properties not set.');
    }
}

static trackPageView(pageTitle, pageUrl) {
    if (RadMonitor.instance.isTrackingAllowed) {
        RadMonitor.instance.queueRequest({
            name: 'page_view',
            value: {
                title: pageTitle || document.title,
                url: utils.anonymize.url(pageUrl || window.location.href)
            }
        });
        utils.debugLog('Page view tracked:', pageTitle, pageUrl);
    } else {
        utils.debugLog('Tracking not allowed. Page view not tracked.');
    }
}

static trackError(errorMessage, errorDetails) {
    if (RadMonitor.instance.isTrackingAllowed) {
        RadMonitor.instance.queueRequest({
            name: 'error',
            value: {
                message: errorMessage,
                details: errorDetails
            }
        });
        utils.debugLog('Error tracked:', errorMessage, errorDetails);
    } else {
        utils.debugLog('Tracking not allowed. Error not tracked.');
    }
}

static setConsentStatus(status) {
    RadMonitor.instance.isTrackingAllowed = status;
    w.localStorage.setItem(internalConfig.consentCookieName, status ? Date.now().toString() : 'denied');
    utils.debugLog('Consent status set:', status);
    if (status) {
        RadMonitor.instance.initializeAnalytics();
    }
}

static getConsentStatus() {
    return RadMonitor.instance.isTrackingAllowed;
}

static clearUserData() {
    if (RadMonitor.instance.isTrackingAllowed) {
        RadMonitor.instance.userId = null;
        RadMonitor.instance.abTests = {};
        w.localStorage.removeItem('rad_monitor_session_id');
        w.localStorage.removeItem('rad_monitor_session_start');
        RadMonitor.instance.queueRequest({
            name: 'clear_user_data',
            value: { timestamp: new Date().toISOString() }
        });
        utils.debugLog('User data cleared');
    } else {
        utils.debugLog('Tracking not allowed. User data not cleared.');
    }
}

static setCustomDimension(dimensionName, value) {
    if (RadMonitor.instance.isTrackingAllowed) {
        RadMonitor.instance.queueRequest({
            name: 'set_custom_dimension',
            value: {
                dimension: dimensionName,
                value: value
            }
        });
        utils.debugLog('Custom dimension set:', dimensionName, value);
    } else {
        utils.debugLog('Tracking not allowed. Custom dimension not set.');
    }
}

static trackTiming(category, variable, time, label) {
    if (RadMonitor.instance.isTrackingAllowed) {
        RadMonitor.instance.queueRequest({
            name: 'timing',
            value: {
                category: category,
                variable: variable,
                time: time,
                label: label
            }
        });
        utils.debugLog('Timing tracked:', category, variable, time, label);
    } else {
        utils.debugLog('Tracking not allowed. Timing not tracked.');
    }
}

static getConfig() {
    return { ...config };
}

static updateConfig(newConfig) {
    Object.assign(config, newConfig);
    utils.debugLog('Config updated:', config);
}

static getSessionId() {
    return utils.getSessionId();
}