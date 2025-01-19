trackHeatmapClicks() {
    // Track mouse movements, clicks and hovers
    let mousePositions = [];
    let hoverStartTime = null;
    let currentHoveredElement = null;
    let interactions = [];
    let touchStartTime = null;
    let touchStartPosition = null;
    let scrollStartPosition = null;
    let lastScrollTime = null;
    let lastQueueTime = Date.now();
    const QUEUE_INTERVAL = 10000; // 10 seconds

    const queueInteraction = (type, event, extraData = {}) => {
        const target = event.composedPath ? event.composedPath()[0] : event.target;
        if (!target) return;

        utils.debugLog('Interaction queued:', {type, target, extraData});

        const rect = target instanceof Element ? target.getBoundingClientRect() : { top: 0, left: 0, width: 0, height: 0 };
        const attributes = {};
        const styles = {};

        if (target instanceof Element) {
            // Collect more detailed element attributes
            attributes.role = target.getAttribute('role');
            attributes.ariaLabel = target.getAttribute('aria-label');
            attributes.title = target.getAttribute('title');
            attributes.alt = target.getAttribute('alt');
            attributes.href = target.getAttribute('href');
            attributes.src = target.getAttribute('src');
            attributes.dataAttributes = Object.fromEntries(
                [...target.attributes]
                    .filter(attr => attr.name.startsWith('data-'))
                    .map(attr => [attr.name, attr.value])
            );

            const computedStyle = window.getComputedStyle(target);
            styles.visibility = computedStyle.visibility;
            styles.display = computedStyle.display;
            styles.opacity = computedStyle.opacity;
            styles.zIndex = computedStyle.zIndex;
            styles.position = computedStyle.position;
            styles.pointerEvents = computedStyle.pointerEvents;
            styles.cursor = computedStyle.cursor;
        }

        interactions.push({
            type,
            timestamp: new Date().toISOString(),
            element: {
                tagName: target.tagName,
                id: target.id,
                className: target.className,
                path: utils.getElementPath(target),
                rect: {
                    top: rect.top,
                    left: rect.left,
                    width: rect.width,
                    height: rect.height,
                    bottom: rect.bottom,
                    right: rect.right
                },
                attributes,
                styles,
                isVisible: utils.isElementVisible(target),
                isInteractive: utils.isElementInteractive(target)
            },
            position: {
                pageX: event.pageX,
                pageY: event.pageY,
                clientX: event.clientX,
                clientY: event.clientY,
                scrollX: window.pageXOffset,
                scrollY: window.pageYOffset,
            },
            environment: {
                timeOnPage: Date.now() - performance.timing.navigationStart,
                frameRate: utils.getCurrentFrameRate(),
                isTabFocused: document.hasFocus(),
                tabVisibilityState: document.visibilityState
            },
            session: {
                totalInteractions: interactions.length,
                sessionDuration: utils.getSessionDuration(),
                previousPageUrl: document.referrer,
                navigationtype: performance.navigation?.type
            },
            ...extraData
        });

        utils.debugLog('Interaction data:', interactions[interactions.length - 1]);
    };

    // Track mouse movements with velocity and acceleration
    let lastMouseX = 0;
    let lastMouseY = 0;
    let lastMouseTime = Date.now();
    let lastVelocityX = 0;
    let lastVelocityY = 0;

    // Add visual cursor tracking for debugging
    const cursor = document.createElement('div');
    if (TSMonitor.debugEnabled()) {
        cursor.style.cssText = 'position: fixed; width: 10px; height: 10px; background: red; border-radius: 50%; pointer-events: none; z-index: 9999;';
        document.body.appendChild(cursor);
    }

    document.addEventListener('mousemove', (event) => {
        const currentTime = Date.now();
        const timeDiff = currentTime - lastMouseTime;
        const velocityX = Math.abs(event.pageX - lastMouseX) / timeDiff;
        const velocityY = Math.abs(event.pageY - lastMouseY) / timeDiff;
        const accelerationX = (velocityX - lastVelocityX) / timeDiff;
        const accelerationY = (velocityY - lastVelocityY) / timeDiff;

        if (TSMonitor.debugEnabled()) {
            cursor.style.left = `${event.clientX - 5}px`;
            cursor.style.top = `${event.clientY - 5}px`;
            utils.debugLog('Mouse Move:', {
                x: event.pageX,
                y: event.pageY,
                velocity: {x: velocityX, y: velocityY},
                acceleration: {x: accelerationX, y: accelerationY}
            });
        }

        mousePositions.push({
            x: event.pageX,
            y: event.pageY,
            timestamp: currentTime,
            velocity: {
                x: velocityX,
                y: velocityY,
                total: Math.sqrt(velocityX * velocityX + velocityY * velocityY)
            },
            acceleration: {
                x: accelerationX,
                y: accelerationY,
                total: Math.sqrt(accelerationX * accelerationX + accelerationY * accelerationY)
            }
        });

        // Keep only last 1000 positions to manage memory
        if (mousePositions.length > 1000) {
            mousePositions = mousePositions.slice(-1000);
        }

        window.mouseX = event.pageX;
        window.mouseY = event.pageY;
        lastMouseX = event.pageX;
        lastMouseY = event.pageY;
        lastMouseTime = currentTime;
        lastVelocityX = velocityX;
        lastVelocityY = velocityY;
    });

    // Track clicks with pressure and additional context
    document.addEventListener('click', (event) => {
        utils.debugLog('Click detected:', {x: event.pageX, y: event.pageY});
        const pressure = event.pressure || (event.originalEvent && event.originalEvent.pressure);
        queueInteraction('click', event, {
            pressure: pressure || null,
            button: event.button,
            buttons: event.buttons,
            detail: event.detail,
            isTrusted: event.isTrusted,
            clickDuration: event.timeStamp - (event.target._mouseDownTime || event.timeStamp),
            multipleClicks: event.detail > 1
        });
    });

    // Track mousedown timing
    document.addEventListener('mousedown', (event) => {
        utils.debugLog('Mouse down:', {x: event.pageX, y: event.pageY});
        event.target._mouseDownTime = event.timeStamp;
    });

    // Touch events tracking
    document.addEventListener('touchstart', (event) => {
        utils.debugLog('Touch start:', {
            x: event.touches[0].pageX,
            y: event.touches[0].pageY
        });
        touchStartTime = Date.now();
        touchStartPosition = {
            x: event.touches[0].pageX,
            y: event.touches[0].pageY
        };
        queueInteraction('touch_start', event, {
            touches: event.touches.length,
            touchType: event.touches[0].touchType
        });
    });

    document.addEventListener('touchend', (event) => {
        if (touchStartTime && touchStartPosition) {
            const touchDuration = Date.now() - touchStartTime;
            utils.debugLog('Touch end:', {
                duration: touchDuration,
                startPos: touchStartPosition,
                endPos: {
                    x: event.changedTouches[0].pageX,
                    y: event.changedTouches[0].pageY
                }
            });
            queueInteraction('touch_end', event, {
                duration: touchDuration,
                distance: touchStartPosition ? Math.sqrt(
                    Math.pow(event.changedTouches[0].pageX - touchStartPosition.x, 2) +
                    Math.pow(event.changedTouches[0].pageY - touchStartPosition.y, 2)
                ) : null
            });
        }
    });

    // Track hover enter with additional context
    document.addEventListener('mouseenter', (event) => {
        utils.debugLog('Mouse enter:', {
            element: event.target,
            x: event.pageX,
            y: event.pageY
        });
        currentHoveredElement = event.target;
        hoverStartTime = Date.now();
        queueInteraction('hover_start', event, {
            parentElement: event.target.parentElement ? {
                tagName: event.target.parentElement.tagName,
                id: event.target.parentElement.id,
                className: event.target.parentElement.className
            } : null,
            elementDepth: utils.getElementDepth(event.target)
        });
    }, true);

    // Track hover exit with duration and movement data
    document.addEventListener('mouseleave', (event) => {
        if (currentHoveredElement === event.target && hoverStartTime) {
            const hoverDuration = Date.now() - hoverStartTime;
            utils.debugLog('Mouse leave:', {
                element: event.target,
                duration: hoverDuration,
                x: event.pageX,
                y: event.pageY
            });
            queueInteraction('hover_end', event, {
                duration: hoverDuration,
                distanceMoved: mousePositions
                    .filter(pos => pos.timestamp > hoverStartTime)
                    .reduce((total, pos, i, arr) => {
                        if (i === 0) return 0;
                        const prev = arr[i - 1];
                        return total + Math.sqrt(
                            Math.pow(pos.x - prev.x, 2) +
                            Math.pow(pos.y - prev.y, 2)
                        );
                    }, 0),
                averageVelocity: mousePositions
                    .filter(pos => pos.timestamp > hoverStartTime)
                    .reduce((sum, pos) => sum + pos.velocity.total, 0) / mousePositions.length
            });
            hoverStartTime = null;
            currentHoveredElement = null;
        }
    }, true);

    // Track scroll behavior
    document.addEventListener('scroll', utils.throttle((event) => {
        const currentTime = Date.now();
        const scrollPosition = window.scrollY;

        if (!scrollStartPosition) {
            scrollStartPosition = scrollPosition;
            lastScrollTime = currentTime;
            return;
        }

        utils.debugLog('Scroll:', {
            position: scrollPosition,
            direction: scrollPosition > scrollStartPosition ? 'down' : 'up'
        });

        queueInteraction('scroll', event, {
            startPosition: scrollStartPosition,
            currentPosition: scrollPosition,
            distance: Math.abs(scrollPosition - scrollStartPosition),
            duration: currentTime - lastScrollTime,
            direction: scrollPosition > scrollStartPosition ? 'down' : 'up',
            speed: Math.abs(scrollPosition - scrollStartPosition) / (currentTime - lastScrollTime)
        });

        lastScrollTime = currentTime;
        scrollStartPosition = scrollPosition;
    }, 100));

    // Track long hovers with engagement metrics
    setInterval(() => {
        if (currentHoveredElement && hoverStartTime) {
            const hoverDuration = Date.now() - hoverStartTime;
            utils.debugLog('Hover update:', {
                element: currentHoveredElement,
                duration: hoverDuration,
                x: window.mouseX,
                y: window.mouseY
            });
            queueInteraction('hover_update', {
                target: currentHoveredElement,
                pageX: window.mouseX,
                pageY: window.mouseY
            }, {
                duration: hoverDuration,
                isStatic: mousePositions
                    .slice(-5)
                    .every(pos => Math.abs(pos.x - window.mouseX) < 5 &&
                                Math.abs(pos.y - window.mouseY) < 5),
                engagement: {
                    score: utils.calculateEngagementScore(hoverDuration),
                    metrics: utils.getEngagementMetrics()
                }
            });
        }
    }, 500);

    // Send data periodically to prevent data loss
    const autoSaveInterval = setInterval(() => {
        const currentTime = Date.now();

        // Only queue if we have data and enough time has passed
        if ((interactions.length > 0 || mousePositions.length > 0) &&
            (currentTime - lastQueueTime >= QUEUE_INTERVAL)) {

            utils.debugLog('Queueing heatmap data:', {
                interactions: interactions.length,
                mouseMovements: mousePositions.length
            });

            const heatmapData = {
                type: 'heatmap',
                timestamp: new Date().toISOString(),
                pageUrl: window.location.href,
                viewportSize: {
                    width: window.innerWidth,
                    height: window.innerHeight
                },
                data: {
                    interactions: interactions.splice(0, 100),
                    mouseMovements: mousePositions.splice(0, 100)
                },
                metadata: {
                    userAgent: navigator.userAgent,
                    screenResolution: `${window.screen.width}x${window.screen.height}`,
                    devicePixelRatio: window.devicePixelRatio || 1,
                    sessionId: utils.getSessionId()
                }
            };

            // Queue the request using TSMonitor's existing queue system
            this.queueRequest({
                type: 'event',
                name: 'heatmap_tracking',
                value: heatmapData
            });

            lastQueueTime = currentTime;
        }
    }, Math.min(QUEUE_INTERVAL / 2, 5000)); // Check every 5 seconds or half the queue interval

    // Send data on page unload with final metrics
    window.addEventListener('beforeunload', () => {
        clearInterval(autoSaveInterval);

        if (interactions.length > 0 || mousePositions.length > 0) {
            utils.debugLog('Final heatmap data save:', {
                interactions: interactions.length,
                mouseMovements: mousePositions.length
            });

            const finalHeatmapData = {
                type: 'heatmap',
                timestamp: new Date().toISOString(),
                viewportSize: {
                    width: window.innerWidth,
                    height: window.innerHeight
                },
                data: {
                    interactions,
                    mouseMovements: mousePositions,
                    sessionMetrics: {
                        duration: Date.now() - performance.timing.navigationStart,
                        interactionCount: interactions.length,
                        mouseMovementCount: mousePositions.length,
                        uniqueElementsInteracted: new Set(interactions.map(i => i.element.path)).size,
                        finalEngagementScore: utils.calculateEngagementScore(
                            Date.now() - performance.timing.navigationStart
                        )
                    }
                },
                metadata: {
                    isFinalBatch: true
                }
            };

            // Queue the final request
            this.queueRequest({
                type: 'event',
                name: 'heatmap_tracking',
                value: finalHeatmapData,
                priority: 'high' // Ensure this gets processed before page unload
            });

            // Force process the queue
            this.processBatchedRequests(true);
        }

        // Clean up debug cursor
        if (TSMonitor.debugEnabled() && cursor.parentNode) {
            cursor.parentNode.removeChild(cursor);
        }
    });

    // Add form interaction tracking
    document.addEventListener('focus', (event) => {
        if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA' || event.target.tagName === 'SELECT') {
            queueInteraction('form_focus', event, {
                inputType: event.target.type,
                formId: event.target.form?.id,
                value: event.target.type !== 'password' ? event.target.value : '[REDACTED]'
            });
        }
    }, true);

    // Track rage clicks (multiple rapid clicks in same area)
    let recentClicks = [];
    document.addEventListener('click', (event) => {
        const clickTime = Date.now();
        const clickPosition = { x: event.pageX, y: event.pageY };

        recentClicks.push({ time: clickTime, position: clickPosition });
        recentClicks = recentClicks.filter(click => clickTime - click.time < 2000);

        if (recentClicks.length >= 3) {
            const isRageClick = recentClicks.every(click =>
                Math.abs(click.position.x - clickPosition.x) < 30 &&
                Math.abs(click.position.y - clickPosition.y) < 30
            );

            if (isRageClick) {
                queueInteraction('rage_click', event, {
                    clickCount: recentClicks.length,
                    timespan: clickTime - recentClicks[0].time
                });
            }
        }
    });

    // Track dead clicks (clicks on non-interactive elements)
    document.addEventListener('click', (event) => {
        const target = event.target;
        if (!utils.isElementInteractive(target)) {
            queueInteraction('dead_click', event, {
                expectedInteractive: target.style.cursor === 'pointer'
            });
        }
    });

    // Track text selection
    document.addEventListener('selectionchange', utils.throttle(() => {
        const selection = window.getSelection();
        if (selection.toString().length > 0) {
            const range = selection.getRangeAt(0);
            queueInteraction('text_selection', { target: range.commonAncestorContainer }, {
                selectedText: selection.toString(),
                selectionLength: selection.toString().length,
                containingElement: range.commonAncestorContainer.tagName
            });
        }
    }, 500));

    // Track viewport visibility
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            queueInteraction('visibility_change', { target: entry.target }, {
                isVisible: entry.isIntersecting,
                visibleRatio: entry.intersectionRatio,
                time: Date.now()
            });
        });
    });

    // Observe important elements
    document.querySelectorAll('button, a, form, [role="button"]').forEach(el => observer.observe(el));

    // Clean up
    return {
        cleanup: () => {
            mousePositions = [];
            interactions = [];
            currentHoveredElement = null;
            hoverStartTime = null;
            lastMouseX = 0;
            lastMouseY = 0;
            lastMouseTime = 0;
            clearInterval(autoSaveInterval);
            if (TSMonitor.debugEnabled() && cursor.parentNode) {
                cursor.parentNode.removeChild(cursor);
            }
            utils.debugLog('Cleanup completed');
            observer.disconnect();
            recentClicks = [];
        }
    };
}
