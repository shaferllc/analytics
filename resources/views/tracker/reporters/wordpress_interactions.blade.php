trackWordPressInteractions() {
    // Track Gutenberg editor interactions
    if (document.querySelector('.block-editor-writing-flow')) {
        let blockOperations = {
            created: 0,
            deleted: 0,
            moved: 0,
            edited: 0
        };
        let editingDurations = [];
        let lastEditStart = null;

        // Monitor block creation/deletion
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList') {
                    if (mutation.addedNodes.length) blockOperations.created++;
                    if (mutation.removedNodes.length) blockOperations.deleted++;
                }
            });
        });

        observer.observe(document.querySelector('.block-editor-writing-flow'), {
            childList: true,
            subtree: true
        });

        // Track editing sessions
        document.addEventListener('keydown', () => {
            if (!lastEditStart) {
                lastEditStart = Date.now();
            }
        });

        // Track editing duration when focus is lost
        document.addEventListener('focusout', (e) => {
            if (lastEditStart && e.target.closest('.block-editor-writing-flow')) {
                const duration = Date.now() - lastEditStart;
                editingDurations.push(duration);
                lastEditStart = null;
                blockOperations.edited++;
            }
        });

        // Track block movements
        document.addEventListener('dragend', () => {
            blockOperations.moved++;
        });

        // Report stats every 30 seconds
        setInterval(() => {
            if (Object.values(blockOperations).some(val => val > 0)) {
                const avgEditingDuration = editingDurations.length ?
                    editingDurations.reduce((a, b) => a + b, 0) / editingDurations.length :
                    0;

                this.queueRequest({
                    name: 'wordpress_editor_analytics',
                    type: 'event',
                    value: {
                        blockOperations,
                        averageEditingDuration: Math.round(avgEditingDuration / 1000),
                        totalEditingSessions: editingDurations.length,
                        editorType: 'gutenberg',
                        postType: document.querySelector('[name="post_type"]')?.value || 'unknown',
                        wordCount: document.querySelector('.editor-word-count')?.innerText || 0,
                        timestamp: Date.now()
                    }
                });

                utils.debugInfo('WordPress editor analytics:', {
                    operations: blockOperations,
                    avgEditDuration: Math.round(avgEditingDuration / 1000) + 's'
                });

                // Reset counters
                blockOperations = {created: 0, deleted: 0, moved: 0, edited: 0};
                editingDurations = [];
            }
        }, 30000);

        // Track autosave events
        const originalAutosave = wp?.autosave?.server?.postToServer;
        if (originalAutosave) {
            wp.autosave.server.postToServer = function() {
                const result = originalAutosave.apply(this, arguments);
                this.queueRequest({
                    name: 'wordpress_autosave',
                    type: 'event',
                    value: {
                        timestamp: Date.now(),
                        postId: wp?.data?.select('core/editor')?.getCurrentPostId()
                    }
                });
                return result;
            }.bind(this);
        }
    }
}
