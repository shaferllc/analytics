const eventListeners = {
    scroll: throttle(() => {
        const scrollDepth = Math.round((w.scrollY / (document.documentElement.scrollHeight - w.innerHeight)) * 100);
        debugLog('Scroll depth reached:', scrollDepth);
        sendRequest({name: 'scroll_depth', value: scrollDepth});
    }, 500),

    beforeunload: () => {
        const timeSpent = Math.round((Date.now() - startTime) / 1000);
        debugLog('Time spent on page:', timeSpent);
        sendRequest({name: 'time_on_page', value: timeSpent});
    },

    click: (e) => {
        const target = e.target.closest('[data-track]');
        if (target) {
            const { tagName, id, className } = target;
            debugLog('Tracked element clicked:', target.getAttribute('data-track'));
            sendRequest({
                name: 'click',
                value: target.getAttribute('data-track'),
                element_type: tagName,
                element_id: id,
                element_class: className
            });
        }
    },

    load: () => {
        if (w.performance && w.performance.getEntriesByType) {
            const perfData = w.performance.getEntriesByType("navigation")[0];
            if (perfData) {
                const pageLoadTime = perfData.loadEventEnd - perfData.startTime;
                debugLog('Page load time:', pageLoadTime);
                sendRequest({
                    name: 'page_load_time',
                    value: Math.round(pageLoadTime),
                    dom_content_loaded: Math.round(perfData.domContentLoadedEventEnd - perfData.startTime),
                    first_paint: Math.round(performance.getEntriesByType('paint')[0].startTime)
                });
            }
        }
    },

    beforeprint: () => {
        debugLog('Print attempt detected');
        sendRequest({name: 'print_attempt', value: 'initiated'});
    },

    blur: (e) => {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            const { name, id, type, value } = e.target;
            debugLog('Input blurred:', name || id);
            sendRequest({
                name: 'input_blur',
                value: name || id,
                input_type: type,
                input_value_length: value.length
            });
        }
    },

    contextmenu: (e) => {
        const { tagName, id, className } = e.target;
        debugLog('Context menu opened');
        sendRequest({
            name: 'context_menu',
            value: 'opened',
            target_element: tagName,
            target_id: id,
            target_class: className
        });
    },

    copy: () => {
        debugLog('Copy event detected');
        const selectedText = w.getSelection().toString();
        sendRequest({
            name: 'copy_event',
            value: 'text_copied',
            text_length: selectedText.length,
            text_snippet: selectedText.substring(0, 50)
        });
    },

    drag: (e) => {
        const { id, tagName, className } = e.target;
        debugLog('Drag started:', id || 'unnamed_element');
        sendRequest({
            name: 'drag_start',
            value: id || 'unnamed_element',
            element_type: tagName,
            element_class: className
        });
    },

    drop: (e) => {
        const { id, tagName, className } = e.target;
        debugLog('Drop event:', id || 'unnamed_element');
        sendRequest({
            name: 'drop_event',
            value: id || 'unnamed_element',
            element_type: tagName,
            element_class: className
        });
    },

    ended: (e) => {
        if (e.target.tagName === 'VIDEO' || e.target.tagName === 'AUDIO') {
            const { src, tagName, duration } = e.target;
            debugLog('Media playback ended:', src);
            sendRequest({
                name: 'media_end',
                value: src,
                media_type: tagName.toLowerCase(),
                media_duration: duration
            });
        }
    },

    error: (e) => {
        const { message, lineno, colno, error } = e;
        debugLog('JavaScript error:', message);
        logError(e, { function: 'error event listener' });
        sendRequest({
            name: 'js_error',
            value: message,
            error_line: lineno,
            error_column: colno,
            error_stack: error ? error.stack : 'N/A'
        });
    },

    focus: (e) => {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            const { name, id, type } = e.target;
            debugLog('Input focused:', name || id);
            sendRequest({
                name: 'input_focus',
                value: name || id,
                input_type: type
            });
        }
    },

    keydown: (e) => {
        if (e.ctrlKey && e.key === 'f') {
            debugLog('Search initiated');
            sendRequest({name: 'search_initiated', value: 'ctrl_f'});
        }
    },

    mousemove: throttle((e) => {
        const { clientX, clientY } = e;
        const { innerWidth, innerHeight } = window;
        
        const xPercentage = Math.round((clientX / innerWidth) * 100);
        const yPercentage = Math.round((clientY / innerHeight) * 100);
        
        debugLog('Mouse moved:', `${xPercentage}%x${yPercentage}%`);
        sendRequest({
            name: 'mouse_movement',
            value: `${xPercentage}x${yPercentage}`,
            viewport_width: innerWidth,
            viewport_height: innerHeight
        });
    }, 1000),

    mouseover: (e) => {
        const target = e.target.closest('[data-hover-track]');
        if (target) {
            const { tagName, id, className } = target;
            debugLog('Hover tracked:', target.getAttribute('data-hover-track'));
            sendRequest({
                name: 'hover',
                value: target.getAttribute('data-hover-track'),
                element_type: tagName,
                element_id: id,
                element_class: className
            });
        }
    },

    offline: () => {
        debugLog('Network status: offline');
        sendRequest({name: 'network_status', value: 'offline'});
    },

    online: () => {
        debugLog('Network status: online');
        sendRequest({name: 'network_status', value: 'online'});
    },

    pause: (e) => {
        if (e.target.tagName === 'VIDEO' || e.target.tagName === 'AUDIO') {
            const { src, tagName, currentTime, duration } = e.target;
            debugLog('Media playback paused:', src);
            sendRequest({
                name: 'media_pause',
                value: src,
                media_type: tagName.toLowerCase(),
                current_time: currentTime,
                media_duration: duration
            });
        }
    },

    play: (e) => {
        if (e.target.tagName === 'VIDEO' || e.target.tagName === 'AUDIO') {
            const { src, tagName, currentTime, duration } = e.target;
            debugLog('Media playback started:', src);
            sendRequest({
                name: 'media_play',
                value: src,
                media_type: tagName.toLowerCase(),
                current_time: currentTime,
                media_duration: duration
            });
        }
    },

    resize: debounce(() => {
        const { innerWidth, innerHeight } = w;
        const { width: screenWidth, height: screenHeight } = screen;
        debugLog('Window resized:', `${innerWidth}x${innerHeight}`);
        sendRequest({
            name: 'window_resize',
            value: `${innerWidth}x${innerHeight}`,
            screen_width: screenWidth,
            screen_height: screenHeight
        });
    }, 500),

    submit: (e) => {
        const { id, action, method, elements } = e.target;
        debugLog('Form submitted:', id || 'unnamed_form');
        sendRequest({
            name: 'form_submission',
            value: id || 'unnamed_form',
            form_action: action,
            form_method: method,
            form_elements: elements.length
        });
    },

    touchend: (e) => {
        debugLog('Touch interaction ended');
        sendRequest({
            name: 'touch_interaction',
            value: 'end',
            touch_points: e.changedTouches.length
        });
    },

    touchstart: (e) => {
        debugLog('Touch interaction started');
        sendRequest({
            name: 'touch_interaction',
            value: 'start',
            touch_points: e.touches.length
        });
    },

    visibilitychange: () => {
        const visibility = document.hidden ? 'hidden' : 'visible';
        debugLog('Visibility changed:', visibility);
        sendRequest({
            name: 'visibility_change',
            value: visibility,
            timestamp: new Date().toISOString()
        });
    },

    wheel: throttle((e) => {
        const scrollDirection = e.deltaY > 0 ? 'scroll_down' : 'scroll_up';
        debugLog('Wheel event:', scrollDirection);
        sendRequest({
            name: 'wheel_event',
            value: scrollDirection,
            delta_y: e.deltaY,
            delta_x: e.deltaX
        });
    }, 1000),

    accessibilityEvent: (e) => {
        const { type, target } = e;
        debugLog('Accessibility event:', type);
        sendRequest({
            name: 'accessibility_event',
            value: type,
            target: target.tagName,
            target_role: target.getAttribute('role') || 'none',
            timestamp: new Date().toISOString()
        });
    },

    focus: (e) => {
        const { target } = e;
        if (target.getAttribute('aria-label') || target.getAttribute('aria-labelledby')) {
            debugLog('Accessible element focused:', target.tagName);
            sendRequest({
                name: 'accessible_focus',
                value: target.tagName,
                aria_label: target.getAttribute('aria-label') || 'none',
                aria_labelledby: target.getAttribute('aria-labelledby') || 'none'
            });
        }
    },

    keydown: (e) => {
        const { key, target } = e;
        if (['Tab', 'Enter', 'Escape', ' '].includes(key)) {
            debugLog('Keyboard navigation:', key);
            sendRequest({
                name: 'keyboard_navigation',
                value: key,
                target: target.tagName,
                target_role: target.getAttribute('role') || 'none'
            });
        }
    },

    voiceOver: (e) => {
        const { action, element } = e.detail;
        debugLog('VoiceOver event detected');
        sendRequest({
            name: 'screen_reader_event',
            value: 'VoiceOver',
            action,
            element
        });
    },

    ariaAttributeChanged: (e) => {
        const { attributeName, target } = e;
        debugLog('ARIA attribute changed:', attributeName);
        sendRequest({
            name: 'aria_attribute_change',
            value: attributeName,
            element: target.tagName,
            new_value: target.getAttribute(attributeName)
        });
    },

    customEvent: (eventName, metadata) => {
        debugLog('Custom event tracked:', eventName, metadata);
        sendRequest({
            name: 'custom_event',
            value: eventName,
            ...metadata
        });
    },

    // Performance Monitoring
    performanceObserver: new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
            if (entry.entryType === 'longtask') {
                debugLog('Long task detected:', entry.duration);
                sendRequest({
                    name: 'long_task',
                    value: Math.round(entry.duration),
                    start_time: Math.round(entry.startTime),
                    end_time: Math.round(entry.startTime + entry.duration)
                });
            } else if (entry.entryType === 'layout-shift') {
                debugLog('Layout shift detected:', entry.value);
                sendRequest({
                    name: 'layout_shift',
                    value: entry.value.toFixed(4),
                    timestamp: new Date(entry.startTime).toISOString()
                });
            } else if (entry.entryType === 'largest-contentful-paint') {
                debugLog('Largest Contentful Paint:', entry.startTime);
                sendRequest({
                    name: 'largest_contentful_paint',
                    value: Math.round(entry.startTime),
                    element: entry.element ? entry.element.tagName : 'unknown'
                });
            }
        }
    })
};

Object.entries(eventListeners).forEach(([eventType, listener]) => {
    if (enabledEvents.includes(eventType)) {
        if (eventType === 'load') {
            w.addEventListener(eventType, listener);
        } else if (eventType === 'performanceObserver') {
            listener.observe({entryTypes: ['longtask', 'layout-shift', 'largest-contentful-paint']});
        } else {
            document.addEventListener(eventType, listener);
        }
    }
});