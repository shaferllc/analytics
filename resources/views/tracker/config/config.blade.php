// Global configuration object
w.RadMonitorConfig = w.RadMonitorConfig || {};

const defaultConfig = {
    host: 'https://checkers.test',
    websiteId: '',
    debug: false,
    isBrowserDebug: false,
    heatMap: false,
    events: ['scroll', 'beforeunload', 'click', 'load'],
    requireConsent: false,
};

const config = Object.assign({}, defaultConfig, w.RadMonitorConfig);

// Configuration
const internalConfig = {
    isDebug: config.debug,
    isBrowserDebug: config.isBrowserDebug,
    SESSION_DURATION: 30 * 60 * 1000,
    batchInterval: 10000, // 10 seconds in milliseconds
    consentCookieName: 'rad_monitor_consent',
    consentDuration: 12 * 60 * 60 * 1000,
    dataRetentionPeriod: 90 * 24 * 60 * 60 * 1000,
    csrfTokenName: 'rad_monitor_csrf_token',
};
