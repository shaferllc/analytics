trackUserFrustration() {
    let rageclicks = 0;
    let lastClickTime = 0;
    let lastClickX = 0;
    let lastClickY = 0;
    let consecutiveEscapes = 0;
    let lastEscapeTime = 0;
    let formBackspaces = 0;
    let lastBackspaceTime = 0;

    // Track rapid repeated clicks in same area (rage clicks)
    document.addEventListener('click', (event) => {
        const currentTime = Date.now();
        const timeDiff = currentTime - lastClickTime;
        const distanceX = Math.abs(event.clientX - lastClickX);
        const distanceY = Math.abs(event.clientY - lastClickY);

        // If clicks are within 500ms and 10px of each other
        if (timeDiff < 500 && distanceX < 10 && distanceY < 10) {
            rageclicks++;
            if (rageclicks >= 3) {
                this.queueRequest({
                    type: 'event',
                    name: 'user_frustration',
                    value: {
                        type: 'rage_click',
                        clicks: rageclicks,
                        element: event.target.tagName,
                        elementId: event.target.id || null,
                        elementClass: event.target.className || null,
                        coordinates: {x: event.clientX, y: event.clientY},
                        elementPath: utils.getElementPath(event.target),
                        elementText: event.target.textContent?.trim().substring(0, 100) || null,
                    }
                });
                rageclicks = 0;
            }
        } else {
            rageclicks = 1;
        }

        lastClickTime = currentTime;
        lastClickX = event.clientX;
        lastClickY = event.clientY;
    });

    // Track rapid back/forward navigation (confusion)
    let navigationCount = 0;
    let lastNavTime = 0;
    let navigationHistory = [];

    window.addEventListener('popstate', () => {
        const currentTime = Date.now();
        const currentUrl = window.location.href;

        navigationHistory.push({
            url: currentUrl,
            timestamp: currentTime
        });

        if (navigationHistory.length > 5) {
            navigationHistory.shift();
        }

        if (currentTime - lastNavTime < 1000) {
            navigationCount++;
            if (navigationCount >= 2) {
                this.queueRequest({
                    type: 'event',
                    name: 'user_frustration',
                    value: {
                        type: 'rapid_navigation',
                        count: navigationCount,
                        timeWindow: currentTime - lastNavTime,
                        navigationPattern: navigationHistory,
                    }
                });
            }
        } else {
            navigationCount = 1;
        }
        lastNavTime = currentTime;
    });

    // Track chaotic mouse movements
    let mousePoints = [];
    let isTracking = false;
    let trackingTimeout;
    let lastMouseSpeed = 0;

    document.addEventListener('mousemove', utils.throttle((event) => {
        const currentTime = Date.now();
        mousePoints.push({
            x: event.clientX,
            y: event.clientY,
            time: currentTime,
            target: event.target.tagName
        });

        if (mousePoints.length > 20) {
            mousePoints.shift();
        }

        if (!isTracking) {
            isTracking = true;
            trackingTimeout = setTimeout(() => {
                analyzeMouseMovement();
                isTracking = false;
                mousePoints = [];
            }, 1000);
        }
    }, 50));

    const analyzeMouseMovement = () => {
        if (mousePoints.length < 10) return;

        let totalDistance = 0;
        let totalTime = mousePoints[mousePoints.length - 1].time - mousePoints[0].time;
        let directionChanges = 0;
        let acceleration = 0;
        let hoveredElements = new Set();

        for (let i = 1; i < mousePoints.length; i++) {
            const dx = mousePoints[i].x - mousePoints[i-1].x;
            const dy = mousePoints[i].y - mousePoints[i-1].y;
            const distance = Math.sqrt(dx*dx + dy*dy);
            totalDistance += distance;

            hoveredElements.add(mousePoints[i].target);

            if (i > 1) {
                const prevDx = mousePoints[i-1].x - mousePoints[i-2].x;
                const prevDy = mousePoints[i-1].y - mousePoints[i-2].y;
                const prevDistance = Math.sqrt(prevDx*prevDx + prevDy*prevDy);

                acceleration = Math.abs(distance - prevDistance);

                if (Math.sign(dx) !== Math.sign(prevDx) || Math.sign(dy) !== Math.sign(prevDy)) {
                    directionChanges++;
                }
            }
        }

        const speed = totalDistance / totalTime;
        const speedChange = Math.abs(speed - lastMouseSpeed);
        lastMouseSpeed = speed;

        if ((speed > 2 && directionChanges > 5) || (speedChange > 1 && acceleration > 20)) {
            this.queueRequest({
                type: 'event',
                name: 'user_frustration',
                value: {
                    type: 'chaotic_mouse',
                    speed: speed,
                    directionChanges: directionChanges,
                    distance: totalDistance,
                    duration: totalTime,
                    acceleration: acceleration,
                    speedChange: speedChange,
                    hoveredElements: Array.from(hoveredElements),
                }
            });
        }
    };

    // Track rapid escape key presses
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            const currentTime = Date.now();
            if (currentTime - lastEscapeTime < 1000) {
                consecutiveEscapes++;
                if (consecutiveEscapes >= 2) {
                    this.queueRequest({
                        type: 'event',
                        name: 'user_frustration',
                        value: {
                            type: 'escape_spam',
                            count: consecutiveEscapes,
                            activeElement: document.activeElement.tagName,
                        }
                    });
                }
            } else {
                consecutiveEscapes = 1;
            }
            lastEscapeTime = currentTime;
        }
    });

    // Track rapid backspaces in form fields
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Backspace' &&
            (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA')) {
            const currentTime = Date.now();
            if (currentTime - lastBackspaceTime < 500) {
                formBackspaces++;
                if (formBackspaces >= 5) {
                    this.queueRequest({
                        type: 'event',
                        name: 'user_frustration',
                        value: {
                            type: 'form_frustration',
                            backspaceCount: formBackspaces,
                            fieldType: event.target.type,
                            fieldName: event.target.name || event.target.id,
                            timestamp: new Date().toISOString()
                        }
                    });
                    formBackspaces = 0;
                }
            } else {
                formBackspaces = 1;
            }
            lastBackspaceTime = currentTime;
        }
    });
}
