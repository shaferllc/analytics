trackFileDownloads() {
    document.addEventListener('click', (event) => {
        const link = event.target.closest('a');
        if (!link || !link.href) return;

        // Add more common file extensions
        const fileExtensions = /\.(pdf|doc|docx|xls|xlsx|zip|rar|txt|csv|ppt|pptx|mp3|mp4|wav|jpg|jpeg|png|gif)$/i;

        if (link.href.match(fileExtensions)) {
            const fileName = link.href.split('/').pop();
            const fileType = link.href.split('.').pop().toLowerCase();
            const fileSize = link.dataset.fileSize || 'unknown';

            const downloadData = {
                fileName,
                fileType,
                fileUrl: link.href,
                linkText: link.textContent.trim(),
                fileSize,
                timestamp: Date.now(),
                referrer: document.referrer,
                userAgent: navigator.userAgent,
                pageTitle: document.title
            };

            this.queueRequest({
                type: 'event',
                name: 'file_download',
                value: downloadData
            });

            utils.debugLog('File download tracked:', downloadData);

            // Track download success/failure
            const downloadTimeout = setTimeout(() => {
                utils.debugLog('File download timeout or failed:', fileName);
            }, 5000);

            link.addEventListener('click', () => {
                clearTimeout(downloadTimeout);
            }, { once: true });
        }
    });
}
