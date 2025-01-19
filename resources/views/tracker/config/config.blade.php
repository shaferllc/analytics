// Global configuration object
w.TSMonitorConfig = w.TSMonitorConfig || {

};

const defaultConfig = {
    host: '{{ config('app.url') }}',
    siteId: '',
    debug: false,
    browserDebug: false,
    events: ['scroll', 'beforeunload', 'click', 'load', 'mousemove', 'keydown', 'submit', 'focus', 'blur', 'resize', 'error', 'contextmenu', 'touchstart', 'touchend', 'touchmove'],
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
};

// Make config globally accessible
w.TSMonitorConfig = config;
