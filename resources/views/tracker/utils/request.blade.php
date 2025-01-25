queueRequest(requestData) {
    try {
        // Add timestamp to deduplication key with more precise timing
        const requestKey = JSON.stringify({
           type: requestData.type,
           data: requestData.data,
           name: requestData.name,
           // Use more precise timestamp for deduplication (100ms window)
           timestamp: Math.floor(Date.now() / 100) * 100
       });

        // Check for bot/crawler indicators
        const isBot = (
            // Check common bot user agents
            /bot|crawler|spider|crawling|googlebot|bingbot|yandexbot|slurp|baidu|teoma|ahrefs|semrush/i.test(navigator.userAgent) ||
            !navigator.webdriver === undefined ||
            /PhantomJS|HeadlessChrome|Nightmare|Puppeteer/i.test(navigator.userAgent) ||
            navigator.webdriver ||
            navigator.userAgent.includes("HeadlessChrome") ||
            !navigator.languages ||
            navigator.languages.length === 0 ||
            !window.history ||
            !window.localStorage ||
            !window.sessionStorage ||
            (window.screen && (window.screen.width === 0 || window.screen.height === 0)) ||
            !window.devicePixelRatio ||
            !window.innerWidth ||
            !window.innerHeight ||
            window._phantom ||
            window.callPhantom ||
            window.__nightmare ||
            window.domAutomation ||
            window.webdriver ||
            typeof navigator.plugins === 'undefined' ||
            navigator.plugins.length === 0 ||
            /amazonaws|googleusercontent|azure|digitalocean/i.test(document.referrer) ||
            performance && performance.timing && performance.timing.navigationStart === 0 ||
            navigator.hardwareConcurrency === undefined ||
            navigator.deviceMemory === undefined ||
            !window.indexedDB ||
            !window.requestAnimationFrame ||
            !window.matchMedia ||
            performance.now() === 0 ||
            !navigator.onLine ||
            !window.fetch ||
            !window.XMLHttpRequest ||
            !window.WebGLRenderingContext ||
            !window.CanvasRenderingContext2D ||
            (!window.AudioContext && !window.webkitAudioContext) ||
            (!('ontouchstart' in window) && !window.TouchEvent) ||
            !window.MouseEvent ||
            !window.KeyboardEvent
        );

        if (isBot) {
            utils.debugLog('Bot/suspicious client detected, skipping request:', requestData);
            return;
        }

        // Initialize sent requests Set if not exists
        if (!this.sentRequests) {
            this.sentRequests = new Set();
        }

        // Check if this exact request was already sent
       // if (this.sentRequests.has(requestKey)) {
       //     utils.debugLog('Duplicate request detected, skipping:', requestData);
       //     return;
       // }

        // Add to sent requests tracking
        this.sentRequests.add(requestKey);

        // Clean up old entries (optional)
        if (this.sentRequests.size > 1000) {
            const entries = Array.from(this.sentRequests);
            entries.slice(0, 500).forEach(entry => this.sentRequests.delete(entry));
        }

        const timestamp = Math.floor(Date.now() / 1000);

        const request = {
            ...requestData,
            page: w.location.href,
            session_id: utils.getSessionId(),
            timestamp: timestamp,
        };

        // Send request immediately
        if (requestData.sendImmediately) {
            this.sendRequest([request]).catch(error => {
                utils.debugLog('Immediate request failed, queueing instead:', error.message);
                this.requestQueue.push(request);
            });
        } else {
            this.requestQueue.push(request);
        }
    } catch (error) {
        utils.debugLog('Request queueing failed:', error.message);
    }
}

startBatchTimer() {
    try {
        if (this.batchTimer) {
            clearInterval(this.batchTimer);
        }

        // Add initial delay for first batch
        setTimeout(() => {
            utils.debugLog('Starting batch timer');
            this.processBatchedRequests();
            this.batchTimer = setInterval(() => {
                this.processBatchedRequests();
            }, internalConfig.batchInterval);
        }, 2000); // 2 second initial delay

        this.updateCountdownTimer(internalConfig.batchInterval);
    } catch (error) {
        utils.debugLog('Failed to start batch timer:', error.message);
    }
}

updateCountdownTimer(remainingTime) {
    try {
        if (typeof remainingTime !== 'number' || remainingTime < 0) {
            utils.debugLog('Invalid remaining time value');
            return;
        }

        if (this.countdownTimer) {
            clearInterval(this.countdownTimer);
        }

        this.countdownTimer = setInterval(() => {
            remainingTime -= 1000;
            if (remainingTime <= 0) {
                clearInterval(this.countdownTimer);
            } else {
                utils.debugLog(`Time until next batch send: ${remainingTime / 1000} seconds`);
            }
        }, 1000);
    } catch (error) {
        utils.debugLog('Countdown timer error:', error.message);
    }
}

processBatchedRequests(isUnloading = false) {
    try {
        utils.debugLog('Processing batched requests', {
            queue: this.requestQueue,
            isProcessing: this.isProcessingQueue,
            isUnloading
        });

        // Reset stale processing state
        if (this.isProcessingQueue &&
            this.processingQueueStartTime &&
            (Date.now() - this.processingQueueStartTime > 30000)) {
            utils.debugLog('Processing queue timeout - resetting state');
            this.isProcessingQueue = false;
            this.processingQueueStartTime = null;
        }

        if (this.isProcessingQueue) {
            utils.debugLog('Queue is already being processed, skipping');
            return;
        }

        if (!this.requestQueue.length) {
            utils.debugLog('Request queue is empty, skipping');
            return;
        }

        this.isProcessingQueue = true;
        this.processingQueueStartTime = Date.now();

        // Create a copy of the queue and clear it
        const batch = [...this.requestQueue];
        this.requestQueue = [];

        if (!batch.length) {
            this.resetProcessingState();
            utils.debugLog('Batch is empty after queue splice');
            return;
        }

        utils.debugLog('Sending batch of', batch.length, 'requests');

        const isActuallyUnloading = isUnloading && (
            document.visibilityState === 'hidden' ||
            document.hidden ||
            !navigator.onLine
        );

        if (isActuallyUnloading) {
            utils.debugLog('Page unloading - storing batch offline');
            this.storeOfflineRequest(config.host, batch, 'POST');
            this.resetProcessingState();
            return;
        }

        setTimeout(async () => {
            utils.debugLog('Attempting to send batch request', {
                batchSize: batch.length,
                firstEvent: batch[0],
                lastEvent: batch[batch.length - 1]
            });

            // Log the state before sending
            utils.debugLog('Current state:', {
                isProcessingQueue: this.isProcessingQueue,
                processingQueueStartTime: this.processingQueueStartTime,
                requestQueueLength: this.requestQueue.length,
                sendRequestFailures: this.sendRequestFailures
            });

            try {
                const response = await this.sendRequest(batch);
                utils.debugLog('Batch sent successfully', {
                    response: response,
                    timestamp: new Date().toISOString()
                });
                if (!isUnloading) {
                    this.updateCountdownTimer(internalConfig.batchInterval);
                }
                this.resetProcessingState();
            } catch (error) {
                // Detailed error logging
                utils.debugLog('Batch send error details:', {
                    error: error,
                    errorType: error?.constructor?.name,
                    errorMessage: error?.message,
                    errorStack: error?.stack,
                    timestamp: new Date().toISOString()
                });

                console.error('Full error object:', error);
                this.sendRequestFailures++;

                // Log the batch that failed
                utils.debugLog('Failed batch details:', {
                    batchSize: batch.length,
                    batchContents: batch
                });

                // Add retry count and requeue with exponential backoff
                batch.forEach(request => {
                    request.retryCount = (request.retryCount || 0) + 1;
                    utils.debugLog('Processing failed request:', {
                        request: request,
                        currentRetryCount: request.retryCount
                    });

                    if (request.retryCount <= 3) {
                        const backoffDelay = Math.pow(2, request.retryCount - 1) * 1000;
                        setTimeout(() => {
                            utils.debugLog('Retrying request:', {
                                request: request,
                                retryCount: request.retryCount,
                                backoffDelay: backoffDelay
                            });
                            this.requestQueue.unshift(request);
                        }, backoffDelay);
                        utils.debugLog(`Request scheduled for retry ${request.retryCount} in ${backoffDelay}ms`);
                    } else {
                        utils.debugLog('Request failed after maximum retries:', {
                            request: request,
                            finalRetryCount: request.retryCount
                        });
                        this.storeOfflineRequest(config.host, [request], 'POST');
                    }
                });

                this.resetProcessingState();
            }
        }, 0);
    } catch (error) {
        utils.debugLog('Failed to process batch:', error.message);
        this.resetProcessingState();
    }
}

async sendRequest(events, method = 'POST') {
    try {
        if (!config.siteId) {
            utils.debugLog('Missing required site ID');
            return Promise.reject();
        }

        const page = utils.getPageUrl();
        const session_id = utils.getSessionId();
        const title = utils.getPageTitle();
        const path = utils.getPagePath();
        const charset = utils.getCharacterSet();
        const request_id = utils.getUniqueId();
        const site_id = config.siteId;
        const payload = {
            site_id: site_id,
            page: page,
            session_id: session_id,
            title: title,
            path: path,
            charset: charset,
            request_id: request_id,
            events: events
        };

        // Try fetch first
        try {
            const response = await this.sendWithFetch(payload, method);
            // Clear sent events after successful send
            this.clearSentEvents(events);
            return response;
        } catch (fetchError) {
            utils.debugLog('Fetch failed, falling back to XMLHttpRequest:', fetchError);
            const response = await this.sendWithXHR(payload, method);
            // Clear sent events after successful send
            this.clearSentEvents(events);
            return response;
        }

    } catch (error) {
        console.error('Request failed:', error);
        this.sendRequestFailures++;
        throw error;
    }
}

// Add these new helper methods
async sendWithFetch(payload, method) {
    const response = await fetch(`${config.host}/api/v1/event`, {
        method,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-Token': this.csrfToken || ''
        },
        body: JSON.stringify(payload),
        keepalive: true,
        credentials: 'include'
    });

    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }

    this.updateSuccessMetrics(payload.events.length);
    return await response.json();
}

sendWithXHR(payload, method) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open(method, `${config.host}/api/v1/event`, true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-CSRF-Token', this.csrfToken || '');
        xhr.withCredentials = true;

        xhr.onload = () => {
            if (xhr.status >= 200 && xhr.status < 300) {
                this.updateSuccessMetrics(payload.events.length);
                resolve(JSON.parse(xhr.responseText));
            } else {
                reject(new Error(`HTTP error! status: ${xhr.status}`));
            }
        };

        xhr.onerror = () => {
            reject(new Error('Network request failed'));
        };

        xhr.send(JSON.stringify(payload));
    });
}

updateSuccessMetrics(eventsCount) {
    this.sendRequestFailures = 0;
    this.requestsSent++;
    this.eventsTracked += eventsCount;
    utils.debugLog('Request successful');
}

storeOfflineRequest(url, events, method) {
    try {
        // Validate inputs
        if (!url) {
            utils.debugLog('Missing required URL parameter');
            return;
        }
        // if (!events) {
        //     utils.debugLog('Missing required events parameter');
        //     return;
        // }
        if (!method) {
            utils.debugLog('Missing required method parameter');
            return;
        }

        if (!Array.isArray(events)) {
            utils.debugLog('Events must be an array for offline storage');
            return;
        }

        const offlineRequests = JSON.parse(localStorage.getItem('ts_monitor_offline_requests') || '[]');
        const request = { url, events, method, timestamp: Date.now() };
        offlineRequests.push(request);

        try {
            localStorage.setItem('ts_monitor_offline_requests', JSON.stringify(offlineRequests));
        } catch (storageError) {
            utils.debugLog(`Failed to store offline request: ${storageError.message}`);
            return;
        }

        utils.debugLog('Stored offline request:', request);
    } catch (error) {
        utils.debugLog('Failed to store offline request:', error.message);
    }
}

async sendOfflineRequests() {
    try {
        let offlineRequests;
        try {
            offlineRequests = JSON.parse(localStorage.getItem('ts_monitor_offline_requests') || '[]');
        } catch (parseError) {
            utils.debugLog(`Failed to parse offline requests: ${parseError.message}`);
            return;
        }

        if (!offlineRequests.length) {
            utils.debugLog('No offline requests to process');
            return;
        }

        // Validate stored requests
        const validRequests = offlineRequests.filter(request => {
            return request && request.url && Array.isArray(request.events) && request.method;
        });

        if (validRequests.length !== offlineRequests.length) {
            utils.debugLog(`${offlineRequests.length - validRequests.length} invalid requests will be skipped`);
        }

        utils.debugLog('Processing offline requests:', validRequests);

        try {
            await Promise.all(
                validRequests.map(request => this.sendRequest(request.events, request.method))
            );
            localStorage.removeItem('ts_monitor_offline_requests');
            utils.debugLog('Offline requests processed successfully');
        } catch (error) {
            if (error && error.message) {
                utils.debugLog('Failed to process offline requests:', error.message);
            } else {
                utils.debugLog('Failed to process offline requests:', error);
            }
            throw error;
        }
    } catch (error) {
        utils.debugLog('Failed to process offline requests:', error.message);
        throw error;
    }
}

resetProcessingState() {
    try {
        this.isProcessingQueue = false;
        this.processingQueueStartTime = null;
    } catch (error) {
        utils.debugLog('Failed to reset processing state:', error.message);
    }
}

cleanup() {
    if (this.batchTimer) {
        clearInterval(this.batchTimer);
    }
    if (this.countdownTimer) {
        clearInterval(this.countdownTimer);
    }
    this.requestQueue = [];
    this.sentRequests.clear();
}

// Add new helper method to clear sent events
clearSentEvents(events) {
    try {
        events.forEach(event => {
            const eventKey = JSON.stringify({
                type: event.type,
                data: event.data,
                timestamp: Math.floor(event.timestamp / 100) * 100
            });
            this.sentRequests.delete(eventKey);
        });
        utils.debugLog('Cleared sent events from tracking');
    } catch (error) {
        utils.debugLog('Failed to clear sent events:', error.message);
    }
}
