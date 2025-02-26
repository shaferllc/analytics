trackExitRate() {
    let totalPageviews = 0;
    let exitPageviews = 0;
    let lastPageUrl = '';
    let sessionStartTime = Date.now();

    // Increment total pageviews on page load
    totalPageviews++;
    lastPageUrl = window.location.href;
    utils.debugInfo('Page loaded, total pageviews:', totalPageviews);

    // Track exit on page unload
    window.addEventListener('beforeunload', (event) => {
        utils.debugInfo('beforeunload event detected');
        exitPageviews++;

        const timeOnPage = Date.now() - sessionStartTime;
        const exitRate = (exitPageviews / totalPageviews) * 100;
        const scrollDepth = utils.getScrollDepth();

        const exitData = {
            name: 'exit_rate',
            type: 'event',
            value: {
                exitRate: exitRate.toFixed(2),
                totalPageviews: totalPageviews,
                exitPageviews: exitPageviews,
                timeOnPage: timeOnPage,
                url: utils.anonymize.url(lastPageUrl),
                nextUrl: utils.anonymize.url(document.activeElement?.href || ''),
                isExternalLink: document.activeElement?.href ? !document.activeElement.href.includes(window.location.hostname) : false,
            }
        };

        // Queue the exit data
        this.queueRequest(exitData);
        utils.debugInfo('Exit rate data queued:', exitData);

        // Force process batched requests
        this.processBatchedRequests(true);
    });

    // Check if there's stored exit rate data and send it
    const storedExitData = localStorage.getItem('ts_monitor_offline_requests');
    if (storedExitData) {
        try {
            const offlineRequests = JSON.parse(storedExitData);
            const exitRateRequests = offlineRequests.filter(request =>
                request.events?.[0]?.name === 'exit_rate'
            );

            if (exitRateRequests.length) {
                exitRateRequests.forEach(request => {
                    this.queueRequest(request.events[0]);
                });
                utils.debugInfo(`Processed ${exitRateRequests.length} stored exit rate requests`);

                // Remove processed requests
                localStorage.setItem('ts_monitor_offline_requests',
                    JSON.stringify(offlineRequests.filter(request =>
                        request.events?.[0]?.name !== 'exit_rate'
                    ))
                );
            }
        } catch (error) {
            utils.debugInfo('Error processing stored exit rate data:', error);
        }
    }

    // Track navigation within SPA
    const originalPushState = history.pushState;
    history.pushState = function() {
        originalPushState.apply(history, arguments);
        totalPageviews++;
        lastPageUrl = window.location.href;
        sessionStartTime = Date.now();
        utils.debugInfo('pushState called, total pageviews:', totalPageviews);
    };

    window.addEventListener('popstate', () => {
        totalPageviews++;
        lastPageUrl = window.location.href;
        sessionStartTime = Date.now();
        utils.debugInfo('popstate event, total pageviews:', totalPageviews);
    });
}
