trackTabVisibility() {
    let tabSwitches = 0;
    let totalHiddenTime = 0;
    let lastHiddenTimestamp = null;
    let focusLossPatterns = [];
    const patternThreshold = 5000; // 5 seconds

    document.addEventListener('visibilitychange', () => {
        const timestamp = Date.now();

        if (document.hidden) {
            lastHiddenTimestamp = timestamp;
            tabSwitches++;

            // Record pattern if user switches back quickly
            if (focusLossPatterns.length > 0) {
                const lastSwitch = focusLossPatterns[focusLossPatterns.length - 1];
                if (timestamp - lastSwitch.timestamp < patternThreshold) {
                    focusLossPatterns.push({
                        timestamp,
                        type: 'switch_away',
                        timeSinceLastSwitch: timestamp - lastSwitch.timestamp
                    });
                }
            }
        } else {
            if (lastHiddenTimestamp) {
                const hiddenDuration = timestamp - lastHiddenTimestamp;
                totalHiddenTime += hiddenDuration;

                focusLossPatterns.push({
                    timestamp,
                    type: 'return',
                    hiddenDuration
                });

                // Analyze for potential distraction patterns
                if (focusLossPatterns.length >= 3) {
                    const rapidSwitches = focusLossPatterns.filter(p =>
                        p.hiddenDuration && p.hiddenDuration < patternThreshold
                    ).length;

                    if (rapidSwitches >= 3) {
                        this.queueRequest({
                            name: 'distraction_pattern',
                            type: 'event',
                            value: {
                                pattern: 'rapid_switching',
                                switches: rapidSwitches,
                                timeWindow: timestamp - focusLossPatterns[0].timestamp,
                                averageDuration: totalHiddenTime / tabSwitches
                            }
                        });
                        focusLossPatterns = []; // Reset pattern tracking
                    }
                }
            }
        }

        // Queue visibility data periodically
        if (tabSwitches % 5 === 0) {
            this.queueRequest({
                name: 'tab_visibility',
                type: 'event',
                value: {
                    switches: tabSwitches,
                    totalHiddenTime,
                    averageHiddenDuration: totalHiddenTime / tabSwitches,
                    currentState: document.hidden ? 'hidden' : 'visible',
                    timestamp: timestamp,
                    url: window.location.href
                }
            });
            utils.debugLog('Tab visibility tracked:', {
                switches: tabSwitches,
                totalHiddenTime: Math.round(totalHiddenTime / 1000) + 's'
            });
        }
    });
}
