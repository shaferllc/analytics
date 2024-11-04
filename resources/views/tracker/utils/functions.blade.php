


collectHeatmapData() {
    document.addEventListener('click', (event) => {
        this.queueRequest({
            name: 'heatmap_click',
            value: {
                tagName: event.target.tagName,
                id: event.target.id,
                className: event.target.className,
                innerText: event.target.innerText,
                href: event.target.href ? utils.anonymize.url(event.target.href) : '',
                x: Math.round(event.pageX / window.innerWidth * 100),
                y: Math.round(event.pageY / window.innerHeight * 100),
                screenWidth: Math.round(window.innerWidth / 100) * 100,
                screenHeight: Math.round(window.innerHeight / 100) * 100,
                timestamp: new Date().toISOString(),
                path: utils.anonymize.url(window.location.pathname),
                elementWidth: event.target.offsetWidth,
                elementHeight: event.target.offsetHeight,
                viewportX: event.clientX,
                viewportY: event.clientY,
                scrollX: window.pageXOffset,
                scrollY: window.pageYOffset,
                userAgent: utils.anonymize.userAgent(navigator.userAgent),
                devicePixelRatio: window.devicePixelRatio || 1
            }
        });
    });
    utils.debugLog('Heatmap data collection initialized');
}

getLargestContentfulPaint() {
    return new Promise(resolve => {
        new PerformanceObserver((entryList) => {
            const entries = entryList.getEntries();
            const lastEntry = entries[entries.length - 1];
            resolve(lastEntry ? lastEntry.startTime : undefined);
        }).observe({ type: 'largest-contentful-paint', buffered: true });
    });
}

getFirstInputDelay() {
    return new Promise(resolve => {
        new PerformanceObserver((entryList) => {
            const firstInput = entryList.getEntries()[0];
            resolve(firstInput ? firstInput.processingStart - firstInput.startTime : undefined);
        }).observe({ type: 'first-input', buffered: true });
    });
}

getCumulativeLayoutShift() {
    return new Promise(resolve => {
        let cumulativeLayoutShiftScore = 0;
        new PerformanceObserver((entryList) => {
            for (const entry of entryList.getEntries()) {
                if (!entry.hadRecentInput) {
                    cumulativeLayoutShiftScore += entry.value;
                }
            }
            resolve(cumulativeLayoutShiftScore);
        }).observe({ type: 'layout-shift', buffered: true });
    });
}

getCsrfToken() {
    return document.querySelector(`meta[name="${config.csrfTokenName}"]`)?.getAttribute('content');
}