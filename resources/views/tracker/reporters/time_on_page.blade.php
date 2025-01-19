trackTimeOnPage() {
    if (!window || !document) {
        utils.debugLog('Window or document not available');
        return 0;
    }

    const startTime = Date.now();
    let totalTimeSpent = 0;
    let isVisible = true;
    let lastVisibleTime = startTime;

    // Update time spent when visibility changes
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            // Page is being hidden, add time since last visible
            if (isVisible) {
                const now = Date.now();
                totalTimeSpent += now - lastVisibleTime;
                isVisible = false;
            }
        } else {
            // Page is becoming visible again
            lastVisibleTime = Date.now();
            isVisible = true;
        }
    });

    // Calculate final time before page unload
    const calculateFinalTime = () => {
        if (isVisible) {
            // Add any remaining time if page is still visible
            const now = Date.now();
            totalTimeSpent += now - lastVisibleTime;
        }
        return Math.round(totalTimeSpent / 1000); // Convert to seconds
    };

    // Add unload handlers
    window.addEventListener('beforeunload', calculateFinalTime);
    window.addEventListener('pagehide', calculateFinalTime);

    return calculateFinalTime();
}
