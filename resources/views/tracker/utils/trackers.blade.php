trackFileDownloads() {
    document.addEventListener('click', (event) => {
        const link = event.target.closest('a');
        if (link && link.href && link.href.match(/\.(pdf|doc|docx|xls|xlsx|zip|rar)$/i)) {
            this.queueRequest({
                name: 'file_download',
                value: {
                    fileName: link.href.split('/').pop(),
                    fileType: link.href.split('.').pop().toLowerCase()
                }
            });
        }
    });
}



trackEngagement() {
    // Increase score for various actions
    document.addEventListener('click', () => utils.updateEngagementScore(1));
    document.addEventListener('scroll', utils.throttle(() => utils.updateEngagementScore(0.5), 1000));
    document.addEventListener('keypress', utils.throttle(() => utils.updateEngagementScore(0.3), 1000));

    // Report engagement score periodically
    setInterval(() => {
        this.queueRequest({
            name: 'engagement_score',
            value: {
                score: RadMonitor.instance.engagementScore,
                timestamp: new Date().toISOString(),
                sessionId: utils.getSessionId(),
                userId: utils.hashUserId(RadMonitor.instance.userId),
                path: utils.anonymize.url(window.location.pathname),
                referrer: utils.anonymize.url(w.document.referrer),
                userAgent: utils.anonymize.userAgent(navigator.userAgent),
                devicePixelRatio: window.devicePixelRatio || 1
            }
        });
        utils.debugLog('Engagement score reported:', RadMonitor.instance.engagementScore);
        RadMonitor.instance.engagementScore = 0; // Reset after reporting
    }, 60000); // Report every minute
}

trackOutboundLinks() {
    document.addEventListener('click', (event) => {
        const link = event.target.closest('a');
        if (link && link.hostname !== window.location.hostname) {
            this.queueRequest({
                name: 'outbound_link_click',
                value: {
                    url: utils.anonymize.url(link.href),
                    text: link.textContent.trim()
                }
            });
        }
    });
}

trackJSErrors() {
    w.addEventListener('error', (event) => {
        if (this.isTrackingAllowed) {
            this.queueRequest({
                name: 'js_error',
                value: {
                    message: event.message,
                    source: event.filename,
                    lineno: event.lineno,
                    colno: event.colno,
                    error: event.error ? event.error.stack : null
                }
            });
            utils.debugLog('JavaScript error tracked:', event);
        }
    });
}

trackViewport() {
    const reportViewport = utils.throttle(() => {
        this.queueRequest({
            name: 'viewport_size',
            value: {
                width: window.innerWidth,
                height: window.innerHeight
            }
        });
    }, 2000);

    window.addEventListener('resize', reportViewport);
    reportViewport();
}

trackTimeOnPage() {
    const startTime = Date.now();
    let lastUpdateTime = startTime;
    let timeSpent = 0;
    let isPageVisible = true;

    utils.debugLog('Tracking time on page');
    const updateTimeOnPage = () => {
        if (isPageVisible) {
            const currentTime = Date.now();
            timeSpent += Math.round((currentTime - lastUpdateTime) / 1000);
            lastUpdateTime = currentTime;
            utils.debugLog('Time on page updated:', timeSpent, 'seconds');
        }
    };

    const sendTimeOnPage = () => {
        updateTimeOnPage();
        this.queueRequest({
            name: 'time_on_page',
            value: { seconds: timeSpent }
        });

        utils.debugLog('Sending time on page:', timeSpent, 'seconds');
        this.processBatchedRequests(true);
    };

    // Update time every 10 seconds
    setInterval(updateTimeOnPage, 10000);


    // Handle visibility changes
    document.addEventListener('visibilitychange', () => {
        isPageVisible = document.visibilityState === 'visible';
        if (!isPageVisible) {
            sendTimeOnPage();
            utils.debugLog('Page is hidden, sending time on page:', timeSpent, 'seconds');
        } else {
            lastUpdateTime = Date.now();
            utils.debugLog('Page is visible, resetting last update time');
        }
    });

    // Send data before page unload
    window.addEventListener('beforeunload', sendTimeOnPage);

    // Handle page hide for mobile browsers
    window.addEventListener('pagehide', sendTimeOnPage);

}

trackPageLoadSpeed() {
    if (window.performance) {
        window.addEventListener('load', () => {
            const perfEntry = performance.getEntriesByType('navigation')[0];
            const loadMetrics = {
                dns: perfEntry.domainLookupEnd - perfEntry.domainLookupStart,
                tcp: perfEntry.connectEnd - perfEntry.connectStart,
                request: perfEntry.responseStart - perfEntry.requestStart,
                response: perfEntry.responseEnd - perfEntry.responseStart,
                dom: perfEntry.domComplete - perfEntry.domInteractive,
                load: perfEntry.loadEventEnd - perfEntry.navigationStart,
                ttfb: perfEntry.responseStart - perfEntry.requestStart,
                fcp: performance.getEntriesByType('paint').find(entry => entry.name === 'first-contentful-paint')?.startTime,
                lcp: this.getLargestContentfulPaint(),
                fid: this.getFirstInputDelay(),
                cls: this.getCumulativeLayoutShift()
            };

            Object.keys(loadMetrics).forEach(key => loadMetrics[key] === undefined && delete loadMetrics[key]);

            this.queueRequest({
                name: 'page_load_speed',
                value: loadMetrics
            });
        });
    }
}

trackFormInteractions() {
        document.addEventListener('submit', (event) => {
            if (event.target.tagName === 'FORM') {
                this.queueRequest({
                    name: 'form_submission',
                    value: {
                        formId: event.target.id || 'unnamed_form',
                        formAction: event.target.action
                    }
                });
            }
        });

        document.addEventListener('focus', (event) => {
            if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA') {
                this.queueRequest({
                    name: 'form_field_focus',
                    value: {
                        fieldName: event.target.name || 'unnamed_field',
                        fieldType: event.target.type
                    }
                });
            }
        }, true);
}

trackScrollDepth() {
    let maxScroll = 0;
    const throttledScroll = utils.throttle(() => {
        const scrollPercentage = Math.round((window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100);
        if (scrollPercentage > maxScroll) {
            maxScroll = scrollPercentage;
            this.queueRequest({
                name: 'scroll_depth',
                value: { depth: maxScroll }
            });
        }
    }, 1000);

    window.addEventListener('scroll', throttledScroll);
}

trackPageVisits() {
    this.pageVisits++;
    this.queueRequest({
        name: 'page_visit',
        value: { count: this.pageVisits }
    });
    utils.debugLog('Page visit tracked. Total visits:', this.pageVisits);
}

trackUserInactivity() {
    const inactivityThreshold = 60000; // 1 minute
    const checkInactivity = () => {
        const now = Date.now();
        if (now - this.lastInteraction > inactivityThreshold) {
            this.queueRequest({
                name: 'user_inactive',
                value: { duration: now - this.lastInteraction }
            });
            utils.debugLog('User inactivity detected:', (now - this.lastInteraction) / 1000, 'seconds');
        }
        this.lastInteraction = now;
    };
    ['mousemove', 'keydown', 'click', 'scroll'].forEach(eventType => {
        document.addEventListener(eventType, () => this.lastInteraction = Date.now());
    });
    setInterval(checkInactivity, inactivityThreshold);
}

trackMouseMovements() {
    const throttledMouseMove = utils.throttle((event) => {
        this.mouseMovements.push({ x: event.clientX, y: event.clientY, timestamp: Date.now() });
        if (this.mouseMovements.length >= 10) {
            this.queueRequest({
                name: 'mouse_movements',
                value: this.mouseMovements
            });
            this.mouseMovements = [];
            utils.debugLog('Mouse movements tracked and sent');
        }
    }, 200);
    document.addEventListener('mousemove', throttledMouseMove);
}

trackCopyPaste() {
    const trackCopyPasteEvent = (eventType) => {
        this.copyPasteCount++;
        this.queueRequest({
            name: 'copy_paste',
            value: { type: eventType, count: this.copyPasteCount }
        });
        utils.debugLog(`${eventType} event tracked. Total copy/paste count:`, this.copyPasteCount);
    };
    document.addEventListener('copy', () => trackCopyPasteEvent('copy'));
    document.addEventListener('paste', () => trackCopyPasteEvent('paste'));
}

trackVideoWatching() {
    document.addEventListener('play', (event) => {
        if (event.target.tagName === 'VIDEO') {
            const videoElement = event.target;
            const updateWatchTime = () => {
                this.videoWatchTime += 1;
                if (this.videoWatchTime % 10 === 0) { // Report every 10 seconds
                    this.queueRequest({
                        name: 'video_watch_time',
                        value: {
                            seconds: this.videoWatchTime,
                            videoSrc: videoElement.src
                        }
                    });
                    utils.debugLog('Video watch time updated:', this.videoWatchTime, 'seconds');
                }
            };
            const intervalId = setInterval(updateWatchTime, 1000);
            videoElement.addEventListener('pause', () => clearInterval(intervalId));
            videoElement.addEventListener('ended', () => clearInterval(intervalId));
        }
    });
}

trackExitRate() {
    let totalPageviews = 0;
    let exitPageviews = 0;

    // Increment total pageviews on page load
    totalPageviews++;
    utils.debugLog('Page loaded, total pageviews:', totalPageviews);

    
    // Track exit on page unload
    window.addEventListener('beforeunload', (event) => {
        utils.debugLog('beforeunload event detected');
        exitPageviews++;
        console.log('exitPageviews', exitPageviews);
        const exitRate = (exitPageviews / totalPageviews) * 100;

        console.log('exitRate', exitRate);
        const exitData = {
            name: 'exit_rate',
            value: {
                exitRate: exitRate.toFixed(2),
                totalPageviews: totalPageviews,
                exitPageviews: exitPageviews,
                url: utils.anonymize.url(w.location.href),
                title: document.title
            }
        };
        
        console.log(exitData);
        // Queue the exit data instead of storing offline
        this.queueRequest(exitData);
        utils.debugLog('Exit rate data queued:', JSON.stringify(exitData, null, 2));

        this.processBatchedRequests(true);
    });

    // Check if there's stored exit rate data and send it
    const storedExitData = localStorage.getItem('rad_monitor_offline_requests');
    if (storedExitData) {
        const offlineRequests = JSON.parse(storedExitData);
        offlineRequests.forEach(request => {
            if (request.events[0].name === 'exit_rate') {
                this.queueRequest(request.events[0]);
            }
        });
        localStorage.removeItem('rad_monitor_offline_requests');
        utils.debugLog('Stored exit rate data sent and cleared');
    }

    // Track navigation within SPA (if applicable)
    const pushState = history.pushState;
    history.pushState = function() {
        pushState.apply(history, arguments);
        totalPageviews++;
        utils.debugLog('pushState called, total pageviews:', totalPageviews);
    };

    window.addEventListener('popstate', () => {
        totalPageviews++;
        utils.debugLog('popstate event, total pageviews:', totalPageviews);
    });
}

trackBounceRate() {
    const bounceThreshold = 30000; // 30 seconds
    let hasInteracted = false;
    let bounceTimeout;

    const interactionEvents = ['click', 'scroll', 'keypress'];
    const markInteraction = () => {
        hasInteracted = true;
        clearTimeout(bounceTimeout);
    };

    interactionEvents.forEach(event => {
        document.addEventListener(event, markInteraction);
    });

    // Set a timeout to check for bounce
    bounceTimeout = setTimeout(() => {
        if (!hasInteracted) {
            const bounceData = {
                name: 'bounce',
                value: { bounced: true }
            };
            this.queueRequest(bounceData);
            utils.debugLog('Bounce detected and queued');
        }
    }, bounceThreshold);

    // Track non-bounce when leaving the page
    window.addEventListener('beforeunload', () => {
        if (hasInteracted || Date.now() - performance.timing.navigationStart > bounceThreshold) {
            const bounceData = {
                name: 'bounce',
                value: { bounced: false }
            };
            this.queueRequest(bounceData);
            utils.debugLog('Non-bounce detected and queued');
        }
    });

    // Check if there's stored bounce data and send it
    const storedBounceData = localStorage.getItem('rad_monitor_offline_requests');
    if (storedBounceData) {
        const offlineRequests = JSON.parse(storedBounceData);
        offlineRequests.forEach(request => {
            if (request.events[0].name === 'bounce') {
                this.queueRequest(request.events[0]);
            }
        });
        localStorage.removeItem('rad_monitor_offline_requests');
        utils.debugLog('Stored bounce data sent and cleared');
    }
}