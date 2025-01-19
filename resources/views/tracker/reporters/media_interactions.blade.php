trackMediaInteractions() {
    const mediaSelector = 'video, audio';
    const mediaEvents = ['play', 'pause', 'ended', 'timeupdate', 'seeking', 'volumechange', 'ratechange'];
    let mediaPlaybackStats = new Map();

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll(mediaSelector).forEach(media => {
            // Initialize stats for this media element
            mediaPlaybackStats.set(media, {
                totalPlayTime: 0,
                playbackStartTime: null,
                pauseCount: 0,
                seekCount: 0,
                completionRate: 0
            });

            mediaEvents.forEach(eventName => {
                media.addEventListener(eventName, () => {
                    const stats = mediaPlaybackStats.get(media);
                    const currentTime = Date.now();

                    // Update media statistics
                    switch(eventName) {
                        case 'play':
                            stats.playbackStartTime = currentTime;
                            break;
                        case 'pause':
                            if (stats.playbackStartTime) {
                                stats.totalPlayTime += currentTime - stats.playbackStartTime;
                                stats.pauseCount++;
                            }
                            break;
                        case 'seeking':
                            stats.seekCount++;
                            break;
                        case 'ended':
                            stats.completionRate = (media.currentTime / media.duration) * 100;
                            break;
                    }

                    const mediaData = {
                        type: 'event',
                        name: 'media_interaction',
                        value: {
                            mediaType: media.tagName.toLowerCase(),
                            mediaId: media.id || 'unnamed_media',
                            action: eventName,
                            currentTime: media.currentTime,
                            duration: media.duration,
                            src: media.currentSrc,
                            volume: media.volume,
                            isMuted: media.muted,
                            playbackRate: media.playbackRate,
                            playbackQuality: media.getVideoPlaybackQuality?.() || null,
                            buffered: Array.from(media.buffered).map(i => ({
                                start: media.buffered.start(i),
                                end: media.buffered.end(i)
                            })),
                            stats: {
                                totalPlayTime: stats.totalPlayTime,
                                pauseCount: stats.pauseCount,
                                seekCount: stats.seekCount,
                                completionRate: stats.completionRate
                            }
                        }
                    };

                    this.queueRequest(mediaData);
                    utils.debugLog('Media interaction tracked:', mediaData);
                });
            });
        });
    });
}
