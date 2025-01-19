trackMouseMovements() {
    const throttledMouseMove = utils.throttle((event) => {
        // Get viewport dimensions
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;

        // Calculate relative position as percentage of viewport
        const relativeX = (event.clientX / viewportWidth * 100).toFixed(2);
        const relativeY = (event.clientY / viewportHeight * 100).toFixed(2);

        // Get element under cursor
        const elementUnderCursor = document.elementFromPoint(event.clientX, event.clientY);
        const elementInfo = elementUnderCursor ? {
            tag: elementUnderCursor.tagName.toLowerCase(),
            id: elementUnderCursor.id || null,
            class: elementUnderCursor.className || null
        } : null;

        // Get scroll position
        const scrollX = window.scrollX;
        const scrollY = window.scrollY;

        this.mouseMovements.push({
            x: event.clientX,
            y: event.clientY,
            relativeX: `${relativeX}%`,
            relativeY: `${relativeY}%`,
            timestamp: Date.now(),
            element: elementInfo,
            scrollPosition: {
                x: scrollX,
                y: scrollY
            }
        });

        if (this.mouseMovements.length >= 10) {
            // Calculate movement patterns
            const patterns = this.mouseMovements.reduce((acc, curr, i, arr) => {
                if (i === 0) return acc;
                const prev = arr[i-1];
                const deltaX = curr.x - prev.x;
                const deltaY = curr.y - prev.y;
                acc.totalDistance += Math.sqrt(deltaX*deltaX + deltaY*deltaY);
                return acc;
            }, {totalDistance: 0});

            this.queueRequest({
                name: 'mouse_movements',
                type: 'event',
                value: {
                    movements: this.mouseMovements,
                    patterns: patterns
                }
            });
            this.mouseMovements = [];
            utils.debugLog('Mouse movements tracked and sent');
        }
    }, 200);

    document.addEventListener('mousemove', throttledMouseMove);
}
