trackCopyPaste() {
    const trackCopyPasteEvent = (eventType, event) => {
        this.copyPasteCount++;

        // Get the target element info
        const target = event.target;
        const elementInfo = {
            tag: target.tagName.toLowerCase(),
            type: target.type || null,
            id: target.id || null,
            class: target.className || null
        };

        // Get selected text if available
        const selectedText = window.getSelection()?.toString() || '';

        // Get surrounding text
        const surroundingText = selectedText ? selectedText.slice(0, 100) + (selectedText.length > 100 ? '...' : '') : '';

        // Get clipboard data when possible
        let clipboardData = null;
        if (event.clipboardData) {
            clipboardData = {
                types: Array.from(event.clipboardData.types),
                length: event.clipboardData.items?.length || 0
            };
        }

        this.queueRequest({
            name: 'copy_paste',
            type: 'event',
            value: {
                type: eventType,
                count: this.copyPasteCount,
                timestamp: Date.now(),
                element: elementInfo,
                selectedTextLength: selectedText.length,
                surroundingText: surroundingText,
                clipboardInfo: clipboardData,
                url: window.location.href,
                title: document.title
            }
        });

        utils.debugInfo(`${eventType} event tracked:`, {
            count: this.copyPasteCount,
            element: elementInfo,
            selectedLength: selectedText.length,
            surroundingText: surroundingText
        });

        // Update engagement score
        utils.updateEngagementScore(0.5);
    };

    // Track copy events
    document.addEventListener('copy', (e) => trackCopyPasteEvent('copy', e));

    // Track cut events
    document.addEventListener('cut', (e) => trackCopyPasteEvent('cut', e));

    // Track paste events
    document.addEventListener('paste', (e) => trackCopyPasteEvent('paste', e));
}
