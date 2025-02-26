trackEngagement() {
    // Initialize engagement tracking state
    let pageLoadTime = Date.now();
    let lastInteractionTime = Date.now();
    let mouseMovements = [];
    let formInteractions = 0;
    let mediaPlays = 0;
    let copyPasteCount = 0;
    let scrollCount = 0;
    let clickCount = 0;
    let keypressCount = 0;

    // Track clicks
    document.addEventListener('click', (e) => {
        clickCount++;
        utils.updateEngagementScore(1);
        lastInteractionTime = Date.now();
    });

    // Track scrolling behavior
    let lastScrollPos = window.scrollY;
    document.addEventListener('scroll', utils.throttle(() => {
        scrollCount++;
        utils.updateEngagementScore(0.5);
        lastInteractionTime = Date.now();
    }, 1000));

    // Track keyboard activity
    document.addEventListener('keypress', utils.throttle(() => {
        keypressCount++;
        utils.updateEngagementScore(0.3);
        lastInteractionTime = Date.now();
    }, 1000));

    // Track mouse movements
    document.addEventListener('mousemove', utils.throttle((e) => {
        utils.updateEngagementScore(0.1);
        mouseMovements.push({
            x: e.clientX,
            y: e.clientY,
            timestamp: Date.now()
        });
        lastInteractionTime = Date.now();

        if (mouseMovements.length >= 10) {
            mouseMovements = [];
        }
    }, 1000));

    // Track form interactions
    document.addEventListener('focus', (e) => {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            formInteractions++;
            utils.updateEngagementScore(0.8);
            lastInteractionTime = Date.now();
        }
    }, true);

    // Track media interactions
    document.addEventListener('play', (e) => {
        if (e.target.tagName === 'VIDEO' || e.target.tagName === 'AUDIO') {
            mediaPlays++;
            utils.updateEngagementScore(2);
            lastInteractionTime = Date.now();
        }
    });

    // Track copy/paste
    ['copy', 'paste'].forEach(action => {
        document.addEventListener(action, () => {
            copyPasteCount++;
            utils.updateEngagementScore(0.5);
            lastInteractionTime = Date.now();
        });
    });

    // Report all engagement metrics every 10 seconds
    setInterval(() => {
        const timeOnPage = (Date.now() - pageLoadTime) / 1000;
        const timeSinceLastInteraction = (Date.now() - lastInteractionTime) / 1000;

        const engagementMetrics = {
            score: TSMonitor.instance.engagementScore,
            timeOnPage,
            timeSinceLastInteraction,
            interactions: {
                clicks: clickCount,
                scrolls: scrollCount,
                keyPresses: keypressCount,
                formInteractions,
                mediaPlays,
                copyPaste: copyPasteCount,
                mouseMovements: mouseMovements.length
            },
            scrollDepth: utils.getScrollDepth(),
            activeElements: {
                forms: document.querySelectorAll('input:focus, textarea:focus').length,
                media: Array.from(document.querySelectorAll('video, audio')).filter(el => !el.paused).length
            },
            timestamp: new Date().toISOString()
        };

        if (TSMonitor.instance.engagementScore > 0) {
            this.queueRequest({
                name: 'engagement_metrics',
                value: engagementMetrics
            });

            utils.debugInfo('Engagement metrics reported:', engagementMetrics);
            TSMonitor.instance.engagementScore = 0;
        }
    }, 10000);
}
