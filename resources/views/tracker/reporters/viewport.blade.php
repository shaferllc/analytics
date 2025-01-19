trackViewport() {
    let lastViewportChange = Date.now();
    let lastDimensions = null;

    const getDimensions = () => ({
        width: window.innerWidth,
        height: window.innerHeight,
        orientation: window.screen.orientation?.type || null,
        zoom: Math.round(window.devicePixelRatio * 100),
        scrollHeight: document.documentElement.scrollHeight,
        scrollWidth: document.documentElement.scrollWidth,
        visualViewport: {
            width: window.visualViewport?.width || window.innerWidth,
            height: window.visualViewport?.height || window.innerHeight,
            scale: window.visualViewport?.scale || 1,
            offsetTop: window.visualViewport?.offsetTop || 0,
            offsetLeft: window.visualViewport?.offsetLeft || 0,
            pageTop: window.visualViewport?.pageTop || 0,
            pageLeft: window.visualViewport?.pageLeft || 0
        },
        screenWidth: window.screen.width,
        screenHeight: window.screen.height,
        colorDepth: window.screen.colorDepth,
        pixelDepth: window.screen.pixelDepth,
        availWidth: window.screen.availWidth,
        availHeight: window.screen.availHeight
    });

    const reportViewport = utils.throttle((event) => {
        const now = Date.now();
        const timeSinceLastChange = now - lastViewportChange;
        const currentDimensions = getDimensions();

        // Only report if dimensions actually changed
        if (lastDimensions && JSON.stringify(currentDimensions) === JSON.stringify(lastDimensions)) {
            utils.debugLog('Viewport unchanged, skipping report');
            return;
        }

        lastViewportChange = now;
        lastDimensions = currentDimensions;

        this.queueRequest({
            type: 'event',
            name: 'viewport_change',
            value: {
                ...currentDimensions,
                timeSinceLastChange,
                isFullscreen: document.fullscreenElement !== null,
                isMobile: /Mobi|Android/i.test(navigator.userAgent),
                isTablet: /Tablet|iPad/i.test(navigator.userAgent),
                isLandscape: window.matchMedia('(orientation: landscape)').matches
            }
        });

        utils.debugLog('Viewport change tracked:', currentDimensions);
    }, 1000); // Reduced throttle time for more responsive tracking

    // Track on resize with passive listener for better performance
    window.addEventListener('resize', reportViewport, { passive: true });

    // Track on orientation change
    window.addEventListener('orientationchange', reportViewport);

    // Track on zoom change
    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', reportViewport, { passive: true });
        window.visualViewport.addEventListener('scroll', reportViewport, { passive: true });
    }

    // Track on fullscreen change
    document.addEventListener('fullscreenchange', reportViewport);
    document.addEventListener('webkitfullscreenchange', reportViewport); // Safari support

    // Track on browser window focus/blur
    window.addEventListener('focus', reportViewport);
    window.addEventListener('blur', reportViewport);

    // Track on page visibility change
    document.addEventListener('visibilitychange', reportViewport);

    // Track on device pixel ratio changes (zoom level)
    window.matchMedia('(resolution)').addListener(reportViewport);

    // Initial report after a small delay to ensure accurate initial values
    setTimeout(reportViewport, 100);

    // Cleanup function
    return () => {
        window.removeEventListener('resize', reportViewport);
        window.removeEventListener('orientationchange', reportViewport);
        window.visualViewport?.removeEventListener('resize', reportViewport);
        window.visualViewport?.removeEventListener('scroll', reportViewport);
        document.removeEventListener('fullscreenchange', reportViewport);
        document.removeEventListener('webkitfullscreenchange', reportViewport);
        window.removeEventListener('focus', reportViewport);
        window.removeEventListener('blur', reportViewport);
        document.removeEventListener('visibilitychange', reportViewport);
        window.matchMedia('(resolution)').removeListener(reportViewport);
    };
}
