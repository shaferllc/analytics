// Event handlers
    const eventHandlers = {
        click: (e) => {
            const target = e.target;
            // Check if the click is within the debugger
            if (target.closest('#rad-monitor-debug') || target.closest('#rad-monitor-debug-toggle')) {
                return; // Don't process clicks within the debugger or its toggle
            }
            utils.debugLog('Click event detected');
            RadMonitor.instance.queueRequest({
                name: 'click',
                value: {
                    tagName: target.tagName,
                    id: target.id,
                    className: target.className,
                    innerText: target.innerText ? target.innerText.substring(0, 20) : '',
                    href: target.href ? utils.anonymize.url(target.href) : ''
                }
            });
        },
        // Add other event handlers here
    };