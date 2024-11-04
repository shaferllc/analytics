static toggleDebug(value, mode = 'js') {
    if (mode === 'js') {
        config.isDebug = value;
        utils.debugLog('JavaScript Debug mode ' + (config.isDebug ? 'enabled' : 'disabled'));
    } else if (mode === 'browser') {
        config.isBrowserDebug = value;
        utils.debugLog('Browser Debug mode ' + (config.isBrowserDebug ? 'enabled' : 'disabled'));
    }
    this.toggleDebugElement(config.isDebug || config.isBrowserDebug);
    if (config.isDebug || config.isBrowserDebug) {
        this.logDebugInfo();
    }
}

static toggleDebugElement(show) {
    const debugElement = document.getElementById('rad-monitor-debug') || this.createDebugElement();
    const toggleButton = document.getElementById('rad-monitor-debug-toggle') || this.createToggleButton();

    const debugVisible = this.getDebugVisibility(show);
    this.updateDebugElementVisibility(debugElement, debugVisible);
    this.updateToggleButtonState(toggleButton, debugVisible);
}
static createDebugElement() {
    const debugElement = document.createElement('div');
    debugElement.id = 'rad-monitor-debug';
    debugElement.style.cssText = `
        position: fixed;
        bottom: 40px;
        right: 0;
        width: 80%;
        height: 80%;
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
            RadMonitor Debugger
            <div id="rad-monitor-debug-stats-toggle" style="cursor: pointer; color: #007bff; margin-left: auto;" onclick="document.getElementById('rad-monitor-debug-stats').style.display = document.getElementById('rad-monitor-debug-stats').style.display === 'none' ? 'grid' : 'none';">
                Toggle Stats ▼
            </div>
        </div>
        <div id="rad-monitor-debug-stats" style="display: none; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; position: sticky; top: 50px; background: rgba(0,0,0,0.9); padding: 15px; z-index: 1;">
            <div class="stat-group" style="background: rgba(255,255,255,0.05); border-radius: 10px; padding: 10px;">
                <h3 style="color: #007bff; cursor: pointer; margin-top: 0;" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none';">Configuration ▼</h3>
                <div style="display: none;">
                    <div>Debug Mode: <span id="rad-monitor-debug-mode"></span></div>
                    <div>Tracking Allowed: <span id="rad-monitor-tracking-allowed"></span></div>
                    <div>Recording: <span id="rad-monitor-is-recording"></span></div>
                    <div>Batch Interval: <span id="rad-monitor-batch-interval"></span></div>
                    <div>Session Duration: <span id="rad-monitor-session-duration-config"></span></div>
                </div>
            </div>
            <div class="stat-group" style="background: rgba(255,255,255,0.05); border-radius: 10px; padding: 10px;">
                <h3 style="color: #007bff; cursor: pointer; margin-top: 0;" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none';">Performance ▼</h3>
                <div style="display: none;">
                    <div>Avg. Request Time: <span id="rad-monitor-avg-request-time">0ms</span></div>
                    <div>Last Activity: <span id="rad-monitor-last-activity"></span></div>
                    <div>Send Failures: <span id="rad-monitor-send-failures">0</span></div>
                    <div>Memory Usage: <span id="rad-monitor-memory-usage">N/A</span></div>
                    <div>CPU Usage: <span id="rad-monitor-cpu-usage">N/A</span></div>
                </div>
            </div>
            <div class="stat-group" style="background: rgba(255,255,255,0.05); border-radius: 10px; padding: 10px;">
                <h3 style="color: #007bff; cursor: pointer; margin-top: 0;" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none';">Request Stats ▼</h3>
                <div style="display: none;">
                    <div>Requests Sent: <span id="rad-monitor-requests-sent">0</span></div>
                    <div>Events Tracked: <span id="rad-monitor-events-tracked">0</span></div>
                    <div>Queue Length: <span id="rad-monitor-queue-length">0</span></div>
                    <div>Batch Size: <span id="rad-monitor-batch-size">0</span></div>
                    <div>Last Batch Time: <span id="rad-monitor-last-batch-time">N/A</span></div>
                </div>
            </div>
            <div class="stat-group" style="background: rgba(255,255,255,0.05); border-radius: 10px; padding: 10px;">
                <h3 style="color: #007bff; cursor: pointer; margin-top: 0;" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none';">Session Info ▼</h3>
                <div style="display: none;">
                    <div>Session ID: <span id="rad-monitor-session-id"></span></div>
                    <div>Session Duration: <span id="rad-monitor-session-duration">0s</span></div>
                    <div>User ID: <span id="rad-monitor-user-id"></span></div>
                    <div>Page Views: <span id="rad-monitor-page-views">0</span></div>
                    <div>Engagement Score: <span id="rad-monitor-engagement-score">0</span></div>
                </div>
            </div>
            <div class="stat-group" style="background: rgba(255,255,255,0.05); border-radius: 10px; padding: 10px;">
                <h3 style="color: #007bff; cursor: pointer; margin-top: 0;" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none';">Network Info ▼</h3>
                <div style="display: none;">
                    <div>Connection Type: <span id="rad-monitor-connection-type">Unknown</span></div>
                    <div>Effective Bandwidth: <span id="rad-monitor-effective-bandwidth">Unknown</span></div>
                    <div>RTT: <span id="rad-monitor-rtt">Unknown</span></div>
                    <div>IP Address: <span id="rad-monitor-ip-address">Unknown</span></div>
                    <div>Data Transferred: <span id="rad-monitor-data-transferred">0 KB</span></div>
                </div>
            </div>
            <div class="stat-group" style="background: rgba(255,255,255,0.05); border-radius: 10px; padding: 10px;">
                <h3 style="color: #007bff; cursor: pointer; margin-top: 0;" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none';">Browser Info ▼</h3>
                <div style="display: none;">
                    <div>User Agent: <span id="rad-monitor-user-agent"></span></div>
                    <div>Screen Resolution: <span id="rad-monitor-screen-resolution"></span></div>
                    <div>Device Pixel Ratio: <span id="rad-monitor-device-pixel-ratio"></span></div>
                    <div>Browser Language: <span id="rad-monitor-browser-language"></span></div>
                    <div>Cookies Enabled: <span id="rad-monitor-cookies-enabled"></span></div>
                </div>
            </div>
            <div class="stat-group" style="background: rgba(255,255,255,0.05); border-radius: 10px; padding: 10px;">
                <h3 style="color: #007bff; cursor: pointer; margin-top: 0;" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none';">Page Performance ▼</h3>
                <div style="display: none;">
                    <div>Load Time: <span id="rad-monitor-load-time">0ms</span></div>
                    <div>DOM Content Loaded: <span id="rad-monitor-dom-content-loaded">0ms</span></div>
                    <div>First Paint: <span id="rad-monitor-first-paint">0ms</span></div>
                    <div>First Contentful Paint: <span id="rad-monitor-first-contentful-paint">0ms</span></div>
                    <div>Largest Contentful Paint: <span id="rad-monitor-largest-contentful-paint">0ms</span></div>
                </div>
            </div>
        </div>
        <div id="rad-monitor-debug-log" style="margin-top: 20px; border-top: 1px solid #444; padding-top: 20px;">
            <h3 style="color: #007bff;">Debug Log</h3>
        </div>
    `;
    
    document.body.appendChild(debugElement);

    const logObserver = new MutationObserver(() => {
        const logElement = document.getElementById('rad-monitor-debug-log');
        logElement.scrollTop = logElement.scrollHeight;
    });
    logObserver.observe(document.getElementById('rad-monitor-debug-log'), { childList: true, subtree: true });

    // Update stats every second
    setInterval(() => {
        this.updateDebugStats();
    }, 1000);

    return debugElement;
}
static updateDebugStats() {
    // Get DOM elements once and cache them
    const elements = {
        sessionId: document.getElementById('rad-monitor-session-id'),
        sessionDuration: document.getElementById('rad-monitor-session-duration'),
        userId: document.getElementById('rad-monitor-user-id'),
        requestsSent: document.getElementById('rad-monitor-requests-sent'),
        eventsTracked: document.getElementById('rad-monitor-events-tracked'),
        queueLength: document.getElementById('rad-monitor-queue-length'),
        avgRequestTime: document.getElementById('rad-monitor-avg-request-time'),
        lastActivity: document.getElementById('rad-monitor-last-activity'),
        sendFailures: document.getElementById('rad-monitor-send-failures'),
        debugMode: document.getElementById('rad-monitor-debug-mode'),
        trackingAllowed: document.getElementById('rad-monitor-tracking-allowed'),
        isRecording: document.getElementById('rad-monitor-is-recording'),
        batchInterval: document.getElementById('rad-monitor-batch-interval'),
        sessionDurationConfig: document.getElementById('rad-monitor-session-duration-config'),
        batchSize: document.getElementById('rad-monitor-batch-size'),
        lastBatchTime: document.getElementById('rad-monitor-last-batch-time'),
        pageViews: document.getElementById('rad-monitor-page-views'),
        engagementScore: document.getElementById('rad-monitor-engagement-score'),
        connectionType: document.getElementById('rad-monitor-connection-type'),
        effectiveBandwidth: document.getElementById('rad-monitor-effective-bandwidth'),
        rtt: document.getElementById('rad-monitor-rtt'),
        userAgent: document.getElementById('rad-monitor-user-agent'),
        screenResolution: document.getElementById('rad-monitor-screen-resolution'),
        devicePixelRatio: document.getElementById('rad-monitor-device-pixel-ratio'),
        browserLanguage: document.getElementById('rad-monitor-browser-language'),
        cookiesEnabled: document.getElementById('rad-monitor-cookies-enabled'),
        ipAddress: document.getElementById('rad-monitor-ip-address'),
        dataTransferred: document.getElementById('rad-monitor-data-transferred'),
        loadTime: document.getElementById('rad-monitor-load-time'),
        domContentLoaded: document.getElementById('rad-monitor-dom-content-loaded'),
        firstPaint: document.getElementById('rad-monitor-first-paint'),
        firstContentfulPaint: document.getElementById('rad-monitor-first-contentful-paint'),
        largestContentfulPaint: document.getElementById('rad-monitor-largest-contentful-paint')
    };

    // Calculate session duration once
    const sessionStart = parseInt(localStorage.getItem('rad_monitor_session_start'), 10);
    const sessionDuration = Math.floor((Date.now() - sessionStart) / 1000);

    // Batch DOM updates
    requestAnimationFrame(() => {
        elements.sessionId.textContent = utils.getSessionId();
        elements.sessionDuration.textContent = `${sessionDuration}s`;
        elements.userId.textContent = RadMonitor.instance.userId || 'Not set';
        elements.requestsSent.textContent = RadMonitor.instance.requestsSent || 0;
        elements.eventsTracked.textContent = RadMonitor.instance.eventsTracked || 0;
        elements.queueLength.textContent = RadMonitor.instance.requestQueue.length;
        elements.avgRequestTime.textContent = `${RadMonitor.instance.avgRequestTime || 0}ms`;
        elements.lastActivity.textContent = new Date(RadMonitor.instance.lastActivity).toISOString();
        elements.sendFailures.textContent = RadMonitor.instance.sendRequestFailures;
        elements.debugMode.textContent = config.isDebug ? 'Enabled' : 'Disabled';
        elements.trackingAllowed.textContent = RadMonitor.instance.isTrackingAllowed ? 'Yes' : 'No';
        elements.isRecording.textContent = RadMonitor.instance.isRecording ? 'Active' : 'Inactive';
        elements.batchInterval.textContent = `${internalConfig.batchInterval}ms`;
        elements.sessionDurationConfig.textContent = `${internalConfig.SESSION_DURATION}ms`;
        elements.batchSize.textContent = RadMonitor.instance.lastBatchSize || 0;
        elements.lastBatchTime.textContent = RadMonitor.instance.lastBatchTime ? new Date(RadMonitor.instance.lastBatchTime).toISOString() : 'N/A';
        elements.pageViews.textContent = RadMonitor.instance.pageViews || 0;
        elements.engagementScore.textContent = RadMonitor.instance.engagementScore || 0;

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
    toggleButton.id = 'rad-monitor-debug-toggle';
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
    const debugElement = document.getElementById('rad-monitor-debug');
    const isVisible = debugElement.style.display === 'block';
    RadMonitor.updateDebugElementVisibility(debugElement, !isVisible);
    RadMonitor.updateToggleButtonState(this, !isVisible);
    document.cookie = `rad_monitor_debug_visible=${!isVisible}; path=/; max-age=31536000`;
}

static getDebugVisibility(show) {
    const debugVisibleCookie = document.cookie.split('; ').find(row => row.startsWith('rad_monitor_debug_visible='));
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
