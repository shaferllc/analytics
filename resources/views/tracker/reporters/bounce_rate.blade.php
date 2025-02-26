trackBounceRate() {
    const bounceThreshold = 30000; // 30 seconds
    let hasInteracted = false;
    let bounceTimeout;
    let interactionCount = 0;
    let timeOnPage = 0;
    let lastInteractionTime = performance.now();

    const interactionEvents = ['click', 'scroll', 'keypress', 'mousemove', 'touchstart'];
    const markInteraction = () => {
        hasInteracted = true;
        interactionCount++;
        const now = performance.now();
        timeOnPage = now - lastInteractionTime;
        lastInteractionTime = now;
        clearTimeout(bounceTimeout);
    };

    interactionEvents.forEach(event => {
        document.addEventListener(event, markInteraction);
    });

    // Set a timeout to check for bounce
    bounceTimeout = setTimeout(() => {
        if (!hasInteracted) {
            const bounceData = {
                type: 'event',
                name: 'bounce',
                value: {
                    bounced: true,
                    timeOnPage,
                    interactionCount,
                    entryPage: window.location.pathname,
                    referrer: document.referrer,
                }
            };
            this.queueRequest(bounceData);
            utils.debugInfo('Bounce detected and queued', bounceData.value);
        }
    }, bounceThreshold);

    // Track non-bounce when leaving the page
    window.addEventListener('beforeunload', () => {
        if (hasInteracted || Date.now() - performance.timing.navigationStart > bounceThreshold) {
            const bounceData = {
                type: 'event',
                name: 'bounce',
                value: {
                    bounced: false,
                    timeOnPage,
                    interactionCount,
                    entryPage: window.location.pathname,
                    exitPage: window.location.pathname,
                    referrer: document.referrer,

                    interactions: {
                        clicks: document.querySelectorAll('[data-analytics-clicked="true"]').length,
                        scrollDepth: utils.getScrollDepth(),
                        formInteractions: document.querySelectorAll('input:focus, textarea:focus').length
                    }
                }
            };
            this.queueRequest(bounceData);
            utils.debugInfo('Non-bounce detected and queued', bounceData.value);
        }
    });

    // Check if there's stored bounce data and send it
    const storedBounceData = localStorage.getItem('ts_monitor_offline_requests');
    if (storedBounceData) {
        const offlineRequests = JSON.parse(storedBounceData);
        offlineRequests.forEach(request => {
            if (request.events[0].name === 'bounce') {
                this.queueRequest(request.events[0]);
            }
        });
        localStorage.removeItem('ts_monitor_offline_requests');
        utils.debugInfo('Stored bounce data sent and cleared');
    }
}
