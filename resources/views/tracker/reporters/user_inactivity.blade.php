trackUserInactivity() {
    const inactivityThreshold = 60000; // 1 minute
    const warningThreshold = 45000; // 45 seconds - warn before marking as inactive
    let hasWarnedInactivity = false;
    let lastActiveTab = true;
    
    const checkInactivity = () => {
        const now = Date.now();
        const timeSinceInteraction = now - this.lastInteraction;

        // Check if user is about to be marked inactive
        if (timeSinceInteraction > warningThreshold && !hasWarnedInactivity) {
            this.queueRequest({
                name: 'user_inactivity_warning',
                value: { 
                    duration: timeSinceInteraction,
                    timeUntilInactive: inactivityThreshold - timeSinceInteraction
                }
            });
            hasWarnedInactivity = true;
            utils.debugLog('User inactivity warning:', timeSinceInteraction / 1000, 'seconds');
        }

        // Check if user is inactive
        if (timeSinceInteraction > inactivityThreshold) {
            this.queueRequest({
                name: 'user_inactive',
                value: { 
                    duration: timeSinceInteraction,
                    tabActive: lastActiveTab,
                    screenLocked: utils.isScreenLocked(),
                }
            });
            utils.debugLog('User inactivity detected:', timeSinceInteraction / 1000, 'seconds');
        }
    };

    // Reset activity state
    const resetActivity = () => {
        this.lastInteraction = Date.now();
        hasWarnedInactivity = false;
    };

    // Track more user interactions
    ['mousemove', 'keydown', 'click', 'scroll', 'touchstart', 'touchmove'].forEach(eventType => {
        document.addEventListener(eventType, resetActivity);
    });

    // Track tab visibility
    document.addEventListener('visibilitychange', () => {
        lastActiveTab = document.visibilityState === 'visible';
        if (lastActiveTab) {
            resetActivity();
        }
    });

    // Check inactivity periodically
    const intervalId = setInterval(checkInactivity, 5000);

    // Cleanup
    return () => {
        clearInterval(intervalId);
        ['mousemove', 'keydown', 'click', 'scroll', 'touchstart', 'touchmove'].forEach(eventType => {
            document.removeEventListener(eventType, resetActivity);
        });
    };
}