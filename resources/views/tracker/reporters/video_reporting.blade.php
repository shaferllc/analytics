trackVideoWatching() {
    document.addEventListener('play', (event) => {
        if (event.target.tagName === 'VIDEO') {
            const videoElement = event.target;
            const updateWatchTime = () => {
                this.videoWatchTime += 1;
                if (this.videoWatchTime % 10 === 0) { // Report every 10 seconds
                    this.queueRequest({
                        name: 'video_watch_time',
                        type: 'event',
                        value: {
                            seconds: this.videoWatchTime,
                            videoSrc: videoElement.src
                        }
                    });
                    utils.debugLog('Video watch time updated:', this.videoWatchTime, 'seconds');
                }
            };
            const intervalId = setInterval(updateWatchTime, 1000);
            videoElement.addEventListener('pause', () => clearInterval(intervalId));
            videoElement.addEventListener('ended', () => clearInterval(intervalId));
        }
    });
}

trackMediaErrors() {
    document.addEventListener('error', (event) => {
        if (event.target.tagName === 'VIDEO' || event.target.tagName === 'AUDIO') {
            const mediaElement = event.target;
            this.queueRequest({
                type: 'event',
                name: 'media_error',
                value: {
                    src: mediaElement.src,
                    error: {
                        code: mediaElement.error.code,
                        message: mediaElement.error.message
                    },
                    timestamp: new Date().toISOString(),
                    url: window.location.href,
                    userAgent: navigator.userAgent,
                    // Additional context
                    pageState: {
                        readyState: document.readyState,
                        documentMode: document.documentMode,
                        compatMode: document.compatMode,
                        lastModified: document.lastModified
                    }
                }
            });
            utils.debugLog('Media error tracked:', mediaElement.error);
        }
    }, true);
}
