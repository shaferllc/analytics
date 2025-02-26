// Event handlers
    const eventHandlers = {
        click: (e) => {
            const target = e.target;
            // Check if the click is within the debugger
            if (target.closest('#ts-monitor-debug') || target.closest('#ts-monitor-debug-toggle')) {
                return; // Don't process clicks within the debugger or its toggle
            }
            utils.debugInfo('Click event detected');
            TSMonitor.instance.queueRequest({
                name: 'click',
                value: {
                    ...(target.tagName && { tagName: target.tagName }),
                    ...(target.id && { id: target.id }),
                    ...(target.className && { className: target.className }),
                    ...(target.innerText && { innerText: target.innerText.substring(0, 20) }),
                    ...(target.href && { href: utils.anonymize.url(target.href) }),
                    ...(target.name && { name: target.name }),
                    ...(target.type && { type: target.type }),
                    ...(target.value && { value: target.value }),
                    ...(target.title && { title: target.title }),
                    ...(target.alt && { alt: target.alt }),
                    ...(target.dataset && { dataset: JSON.stringify(target.dataset) }),
                    ...(target.getAttribute('aria-label') && { ariaLabel: target.getAttribute('aria-label') }),
                    ...(target.getAttribute('role') && { role: target.getAttribute('role') }),
                    path: utils.getDomPath(target),
                    timestamp: new Date().toISOString()
                }
            });
        },
    };
