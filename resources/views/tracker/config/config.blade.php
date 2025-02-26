// Global configuration object
w.TSMonitorConfig = w.TSMonitorConfig || {

};

const defaultConfig = {
    host: w.TSMonitorConfig.host,
    id: '',
    debug: false,
    browserDebug: false,
    serverDebug: false,
    events: ['scroll', 'beforeunload', 'click', 'load', 'mousemove', 'keydown', 'submit', 'focus', 'blur', 'resize', 'error', 'contextmenu', 'touchstart', 'touchend', 'touchmove'],
    trackingEnabled: true,
    excludedElements: ['[data-no-track]', '.no-track'],
    samplingRate: 100, // 100% by default
    privacyMode: false,
    maxEventsPerSession: 1000,
    sessionTimeout: 30 * 60 * 1000, // 30 minutes
};

// Merge configurations
const config = Object.assign({}, defaultConfig, w.TSMonitorConfig);

// Make sure currentPage is always available
if (!config.currentPage) {
    config.currentPage = defaultConfig.currentPage;
}

// Configuration
const internalConfig = {
    isDebug: config.debug,
    browserDebug: config.browserDebug,
    SESSION_DURATION: 30 * 60 * 1000,
    batchInterval: 10000, // 10 seconds in milliseconds
    dataRetentionPeriod: 90 * 24 * 60 * 60 * 1000,
    csrfTokenName: 'ts_monitor_csrf_token',
    debugLogEndpoint: w.TSMonitorConfig.host + '/api/v1/debug',
};

// Merge internal config with global config
Object.assign(config, internalConfig);

// Ensure required config values are set
if (!config.host) {
    console.error('TSMonitor: Missing required host configuration');
    config.trackingEnabled = false;
}

if (!config.id) {
    console.error('TSMonitor: Missing required site ID configuration');
    config.trackingEnabled = false;
}

// Validate and normalize configuration values
config.samplingRate = Math.min(Math.max(Number(config.samplingRate) || 100, 0), 100);
config.maxEventsPerSession = Math.max(Number(config.maxEventsPerSession) || 1000, 1);
config.sessionTimeout = Math.max(Number(config.sessionTimeout) || 1800000, 60000);

// Set up debug logging configuration
if (config.debug) {
    console.log('[TSMonitor] Debug mode enabled');
    config.browserDebug = true;
}

// Validate event tracking configuration
if (!Array.isArray(config.events)) {
    config.events = defaultConfig.events;
} else {
    config.events = config.events.filter(event =>
        typeof event === 'string' && defaultConfig.events.includes(event)
    );
}

// Ensure excluded elements is an array
if (!Array.isArray(config.excludedElements)) {
    config.excludedElements = defaultConfig.excludedElements;
}

// Set up CSRF token handling
if (!config.csrfToken) {
    const csrfTokenElement = document.querySelector(`meta[name="${internalConfig.csrfTokenName}"]`);
    if (csrfTokenElement) {
        config.csrfToken = csrfTokenElement.getAttribute('content');
    }
}

// Make config globally accessible
w.TSMonitorConfig = config;
