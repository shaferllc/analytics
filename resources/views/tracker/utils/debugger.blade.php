static toggleDebug(value, mode = 'js') {
    if (mode === 'js') {
        TSMonitorConfig.isDebug = value;
        utils.debugLog('JavaScript Debug mode ' + (TSMonitor.isDebug ? 'enabled' : 'disabled'));
    } else if (mode === 'browser') {
        TSMonitorConfig.browserDebug = value;
        utils.debugLog('Browser Debug mode ' + (TSMonitorConfig.browserDebug ? 'enabled' : 'disabled'));
        this.toggleDebugElement(TSMonitorConfig.isDebug || TSMonitorConfig.browserDebug);
    }
}

static debugEnabled() {
    return TSMonitorConfig.isDebug || TSMonitorConfig.browserDebug;
}

static toggleDebugElement(show) {
    const debugElement = document.getElementById('ts-monitor-debug') || this.createDebugElement();
    const toggleButton = document.getElementById('ts-monitor-debug-toggle') || this.createToggleButton();

    const debugVisible = this.getDebugVisibility(show);
    this.updateDebugElementVisibility(debugElement, debugVisible);
    this.updateToggleButtonState(toggleButton, debugVisible);
}
static createDebugElement() {
    const debugElement = document.createElement('div');
    debugElement.id = 'ts-monitor-debug';
    debugElement.style.cssText = `
        position: fixed;
        bottom: 40px;
        right: 10px;
        width: 400px;
        height: 600px;
        background: rgba(0,0,0,0.9);
        color: white;
        overflow-y: auto;
        font-family: monospace;
        font-size: 12px;
        z-index: 9999;
        display: none;
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
        padding: 20px;
    `;

    // Add advanced stats to the debug element
    debugElement.innerHTML = `
        <div style="color: #00bcd4; margin-bottom: 20px; position: sticky; top: 0; background: rgba(0,0,0,0.9); padding: 10px; z-index: 1; display: flex; align-items: center; justify-content: center;">
            <svg width="30" height="30" style="vertical-align: middle; margin-right: 10px;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="#00bcd4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 17L12 22L22 17" stroke="#00bcd4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 12L12 17L22 12" stroke="#00bcd4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            TSMonitor Debugger
            <div id="ts-monitor-debug-stats-toggle" style="cursor: pointer; color: #007bff; margin-left: auto;" onclick="document.getElementById('ts-monitor-debug-stats').style.display = document.getElementById('ts-monitor-debug-stats').style.display === 'none' ? 'grid' : 'none';">
                Toggle Stats ▼
            </div>
        </div>
        <div id="ts-monitor-debug-stats" style="display: none; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; position: sticky; top: 50px; background: rgba(0,0,0,0.9); padding: 15px; z-index: 1;">
            <div class="stat-group" style="background: rgba(255,255,255,0.05); border-radius: 10px; padding: 10px;">
                <h3 style="color: #007bff; cursor: pointer; margin-top: 0;" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none';">Configuration ▼</h3>
                <div style="display: none;">
                    <div>Debug Mode: <span id="ts-monitor-debug-mode"></span></div>
                    <div>Tracking Allowed: <span id="ts-monitor-tracking-allowed"></span></div>
                    <div>Recording: <span id="ts-monitor-is-recording"></span></div>
                    <div>Batch Interval: <span id="ts-monitor-batch-interval"></span></div>
                    <div>Session Duration: <span id="ts-monitor-session-duration-config"></span></div>
                </div>
            </div>
            <div class="stat-group" style="background: rgba(255,255,255,0.05); border-radius: 10px; padding: 10px;">
                <h3 style="color: #007bff; cursor: pointer; margin-top: 0;" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none';">Performance ▼</h3>
                <div style="display: none;">
                    <div>Avg. Request Time: <span id="ts-monitor-avg-request-time">0ms</span></div>
                    <div>Last Activity: <span id="ts-monitor-last-activity"></span></div>
                    <div>Send Failures: <span id="ts-monitor-send-failures">0</span></div>
                    <div>Memory Usage: <span id="ts-monitor-memory-usage">N/A</span></div>
                    <div>CPU Usage: <span id="ts-monitor-cpu-usage">N/A</span></div>
                </div>
            </div>
            <div class="stat-group" style="background: rgba(255,255,255,0.05); border-radius: 10px; padding: 10px;">
                <h3 style="color: #007bff; cursor: pointer; margin-top: 0;" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none';">Request Stats ▼</h3>
                <div style="display: none;">
                    <div>Requests Sent: <span id="ts-monitor-requests-sent">0</span></div>
                    <div>Events Tracked: <span id="ts-monitor-events-tracked">0</span></div>
                    <div>Queue Length: <span id="ts-monitor-queue-length">0</span></div>
                    <div>Batch Size: <span id="ts-monitor-batch-size">0</span></div>
                    <div>Last Batch Time: <span id="ts-monitor-last-batch-time">N/A</span></div>
                </div>
            </div>
            <div class="stat-group" style="background: rgba(255,255,255,0.05); border-radius: 10px; padding: 10px;">
                <h3 style="color: #007bff; cursor: pointer; margin-top: 0;" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none';">Session Info ▼</h3>
                <div style="display: none;">
                    <div>Session ID: <span id="ts-monitor-session-id"></span></div>
                    <div>Session Duration: <span id="ts-monitor-session-duration">0s</span></div>
                    <div>User ID: <span id="ts-monitor-user-id"></span></div>
                    <div>Page Views: <span id="ts-monitor-page-views">0</span></div>
                    <div>Engagement Score: <span id="ts-monitor-engagement-score">0</span></div>
                </div>
            </div>
            <div class="stat-group" style="background: rgba(255,255,255,0.05); border-radius: 10px; padding: 10px;">
                <h3 style="color: #007bff; cursor: pointer; margin-top: 0;" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none';">Network Info ▼</h3>
                <div style="display: none;">
                    <div>Connection Type: <span id="ts-monitor-connection-type">Unknown</span></div>
                    <div>Effective Bandwidth: <span id="ts-monitor-effective-bandwidth">Unknown</span></div>
                    <div>RTT: <span id="ts-monitor-rtt">Unknown</span></div>
                    <div>IP Address: <span id="ts-monitor-ip-address">Unknown</span></div>
                    <div>Data Transferred: <span id="ts-monitor-data-transferred">0 KB</span></div>
                </div>
            </div>
            <div class="stat-group" style="background: rgba(255,255,255,0.05); border-radius: 10px; padding: 10px;">
                <h3 style="color: #007bff; cursor: pointer; margin-top: 0;" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none';">Browser Info ▼</h3>
                <div style="display: none;">
                    <div>User Agent: <span id="ts-monitor-user-agent"></span></div>
                    <div>Screen Resolution: <span id="ts-monitor-screen-resolution"></span></div>
                    <div>Device Pixel Ratio: <span id="ts-monitor-device-pixel-ratio"></span></div>
                    <div>Browser Language: <span id="ts-monitor-browser-language"></span></div>
                    <div>Cookies Enabled: <span id="ts-monitor-cookies-enabled"></span></div>
                </div>
            </div>
            <div class="stat-group" style="background: rgba(255,255,255,0.05); border-radius: 10px; padding: 10px;">
                <h3 style="color: #007bff; cursor: pointer; margin-top: 0;" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none';">Page Performance ▼</h3>
                <div style="display: none;">
                    <div>Load Time: <span id="ts-monitor-load-time">0ms</span></div>
                    <div>DOM Content Loaded: <span id="ts-monitor-dom-content-loaded">0ms</span></div>
                    <div>First Paint: <span id="ts-monitor-first-paint">0ms</span></div>
                    <div>First Contentful Paint: <span id="ts-monitor-first-contentful-paint">0ms</span></div>
                    <div>Largest Contentful Paint: <span id="ts-monitor-largest-contentful-paint">0ms</span></div>
                </div>
            </div>
        </div>
        <div id="ts-monitor-debug-log" style="margin-top: 20px; border-top: 1px solid #444; padding-top: 20px;">
            <h3 style="color: #007bff;">Debug Log</h3>
        </div>
    `;

    document.body.appendChild(debugElement);

    const logObserver = new MutationObserver(() => {
        const logElement = document.getElementById('ts-monitor-debug-log');
        logElement.scrollTop = logElement.scrollHeight;
    });
    logObserver.observe(document.getElementById('ts-monitor-debug-log'), { childList: true, subtree: true });

    // Update stats every second
    setInterval(() => {
        this.updateDebugStats();
    }, 1000);

    return debugElement;
}
static updateDebugStats() {
    // Get DOM elements once and cache them
    const elements = {
        sessionId: document.getElementById('ts-monitor-session-id'),
        sessionDuration: document.getElementById('ts-monitor-session-duration'),
        userId: document.getElementById('ts-monitor-user-id'),
        requestsSent: document.getElementById('ts-monitor-requests-sent'),
        eventsTracked: document.getElementById('ts-monitor-events-tracked'),
        queueLength: document.getElementById('ts-monitor-queue-length'),
        avgRequestTime: document.getElementById('ts-monitor-avg-request-time'),
        lastActivity: document.getElementById('ts-monitor-last-activity'),
        sendFailures: document.getElementById('ts-monitor-send-failures'),
        debugMode: document.getElementById('ts-monitor-debug-mode'),
        trackingAllowed: document.getElementById('ts-monitor-tracking-allowed'),
        isRecording: document.getElementById('ts-monitor-is-recording'),
        batchInterval: document.getElementById('ts-monitor-batch-interval'),
        sessionDurationConfig: document.getElementById('ts-monitor-session-duration-config'),
        batchSize: document.getElementById('ts-monitor-batch-size'),
        lastBatchTime: document.getElementById('ts-monitor-last-batch-time'),
        pageViews: document.getElementById('ts-monitor-page-views'),
        engagementScore: document.getElementById('ts-monitor-engagement-score'),
        connectionType: document.getElementById('ts-monitor-connection-type'),
        effectiveBandwidth: document.getElementById('ts-monitor-effective-bandwidth'),
        rtt: document.getElementById('ts-monitor-rtt'),
        userAgent: document.getElementById('ts-monitor-user-agent'),
        screenResolution: document.getElementById('ts-monitor-screen-resolution'),
        devicePixelRatio: document.getElementById('ts-monitor-device-pixel-ratio'),
        browserLanguage: document.getElementById('ts-monitor-browser-language'),
        cookiesEnabled: document.getElementById('ts-monitor-cookies-enabled'),
        ipAddress: document.getElementById('ts-monitor-ip-address'),
        dataTransferred: document.getElementById('ts-monitor-data-transferred'),
        loadTime: document.getElementById('ts-monitor-load-time'),
        domContentLoaded: document.getElementById('ts-monitor-dom-content-loaded'),
        firstPaint: document.getElementById('ts-monitor-first-paint'),
        firstContentfulPaint: document.getElementById('ts-monitor-first-contentful-paint'),
        largestContentfulPaint: document.getElementById('ts-monitor-largest-contentful-paint')
    };

    // Calculate session duration once
    const sessionStart = parseInt(localStorage.getItem('ts_monitor_session_start'), 10);
    const sessionDuration = Math.floor((Date.now() - sessionStart) / 1000);

    // Batch DOM updates
    requestAnimationFrame(() => {
        elements.sessionId.textContent = utils.getSessionId();
        elements.sessionDuration.textContent = `${sessionDuration}s`;
        elements.userId.textContent =  TSMonitor.instance.userId || 'Not set';
        elements.requestsSent.textContent = TSMonitor.instance.requestsSent || 0;
        elements.eventsTracked.textContent = TSMonitor.instance.eventsTracked || 0;
        elements.queueLength.textContent = TSMonitor.instance.requestQueue.length;
        elements.avgRequestTime.textContent = `${TSMonitor.instance.avgRequestTime || 0}ms`;
        elements.lastActivity.textContent = new Date(TSMonitor.instance.lastActivity).toISOString();
        elements.sendFailures.textContent = TSMonitor.instance.sendRequestFailures;
        elements.debugMode.textContent = config.isDebug ? 'Enabled' : 'Disabled';
        elements.isRecording.textContent = TSMonitor.instance.isRecording ? 'Active' : 'Inactive';
        elements.batchInterval.textContent = `${internalConfig.batchInterval}ms`;
        elements.sessionDurationConfig.textContent = `${internalConfig.SESSION_DURATION}ms`;
        elements.batchSize.textContent = TSMonitor.instance.lastBatchSize || 0;
        elements.lastBatchTime.textContent = TSMonitor.instance.lastBatchTime ? new Date(TSMonitor.instance.lastBatchTime).toISOString() : 'N/A';
        elements.pageViews.textContent = TSMonitor.instance.pageViews || 0;
        elements.engagementScore.textContent = TSMonitor.instance.engagementScore || 0;

        // Network info
        if (navigator.connection) {
            elements.connectionType.textContent = navigator.connection.effectiveType || 'Unknown';
            elements.effectiveBandwidth.textContent = navigator.connection.downlink ? `${navigator.connection.downlink} Mbps` : 'Unknown';
            elements.rtt.textContent = navigator.connection.rtt ? `${navigator.connection.rtt} ms` : 'Unknown';
        }

        // Browser info
        elements.userAgent.textContent = navigator.userAgent;
        elements.screenResolution.textContent = `${window.screen.width}x${window.screen.height}`;
        elements.devicePixelRatio.textContent = window.devicePixelRatio || 1;
        elements.browserLanguage.textContent = navigator.language || navigator.userLanguage;
        elements.cookiesEnabled.textContent = navigator.cookieEnabled ? 'Yes' : 'No';

        // Performance metrics
        const perfEntries = performance.getEntriesByType('navigation');
        if (perfEntries.length > 0) {
            const perfEntry = perfEntries[0];
            elements.loadTime.textContent = `${Math.round(perfEntry.loadEventEnd - perfEntry.navigationStart)}ms`;
            elements.domContentLoaded.textContent = `${Math.round(perfEntry.domContentLoadedEventEnd - perfEntry.navigationStart)}ms`;
        }

        // Paint metrics
        performance.getEntriesByType('paint').forEach(entry => {
            if (entry.name === 'first-paint') {
                elements.firstPaint.textContent = `${Math.round(entry.startTime)}ms`;
            } else if (entry.name === 'first-contentful-paint') {
                elements.firstContentfulPaint.textContent = `${Math.round(entry.startTime)}ms`;
            }
        });

        // Data transfer calculation
        const totalTransferred = performance.getEntriesByType('resource')
            .reduce((total, resource) => total + (resource.transferSize || 0), 0);
        elements.dataTransferred.textContent = `${(totalTransferred / 1024).toFixed(2)} KB`;
    });

    // Async operations
    utils.getLargestContentfulPaint().then(lcp => {
        if (lcp && elements.largestContentfulPaint) {
            elements.largestContentfulPaint.textContent = `${Math.round(lcp)}ms`;
        }
    });

    // IP address fetch (with debounce/throttle)
    if (!this.lastIpFetch || Date.now() - this.lastIpFetch > 60000) {
        this.lastIpFetch = Date.now();
        fetch('https://api.ipify.org?format=json')
            .then(response => response.json())
            .then(data => {
                elements.ipAddress.textContent = utils.anonymize.ip(data.ip);
            })
            .catch(() => {
                elements.ipAddress.textContent = 'Unavailable';
            });
    }
}

static createToggleButton() {
    const toggleButton = document.createElement('button');
    toggleButton.id = 'ts-monitor-debug-toggle';
    toggleButton.style.cssText = `
        position: fixed;
        bottom: 10px;
        right: 10px;
        padding: 8px 15px;
        background: linear-gradient(45deg, #007bff, #00bcd4);
        color: white;
        border: none;
        cursor: pointer;
        z-index: 10000;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        border-radius: 25px;
        transition: all 0.3s ease;
        font-weight: bold;
        font-size: 14px;
    `;
    toggleButton.innerHTML = '🔍 Debug';
    toggleButton.onclick = this.handleToggleButtonClick;
    toggleButton.onmouseover = () => { toggleButton.style.transform = 'scale(1.05)'; };
    toggleButton.onmouseout = () => { toggleButton.style.transform = 'scale(1)'; };
    document.body.appendChild(toggleButton);

    return toggleButton;
}

static handleToggleButtonClick() {
    const debugElement = document.getElementById('ts-monitor-debug');
    const isVisible = debugElement.style.display === 'block';
    TSMonitor.updateDebugElementVisibility(debugElement, !isVisible);
    TSMonitor.updateToggleButtonState(this, !isVisible);
    document.cookie = `ts_monitor_debug_visible=${!isVisible}; path=/; max-age=31536000`;
}

static getDebugVisibility(show) {
    const debugVisibleCookie = document.cookie.split('; ').find(row => row.startsWith('ts_monitor_debug_visible='));
    return debugVisibleCookie ? debugVisibleCookie.split('=')[1] === 'true' : show;
}

static updateDebugElementVisibility(debugElement, visible) {
    debugElement.style.display = visible ? 'block' : 'none';
}

static updateToggleButtonState(toggleButton, visible) {
    toggleButton.textContent = visible ? '🔽 Hide Debug' : '🔼 Show Debug';
    toggleButton.style.background = visible ? 'linear-gradient(45deg, #28a745, #20c997)' : 'linear-gradient(45deg, #007bff, #00bcd4)';
}

static logDebugInfo() {
    this.updateDebugStats();
    utils.debugLog('Debug info updated');
}
