queueRequest(requestData) {
    try {
        utils.debugLog('Queueing request:', JSON.stringify(requestData, null, 2));
        if (!this.isTrackingAllowed) {
            utils.debugLog('Tracking not allowed - request rejected');
            return;
        }

        // Add timestamp if missing
        if (!requestData.timestamp) {
            requestData.timestamp = new Date().toISOString();
            utils.debugLog('Added missing timestamp:', requestData.timestamp);
        }

        // Validate request data
        if (!requestData || typeof requestData !== 'object') {
            utils.debugLog('Invalid request data format - must be an object');
            return;
        }

        // Required fields validation
        const requiredFields = ['name'];
        const missingFields = requiredFields.filter(field => !requestData[field]);
        if (missingFields.length) {
            utils.debugLog(`Missing required fields: ${missingFields.join(', ')}`);
            return;
        }

        // Validate event type
        // const validEventTypes = ['pageview', 'click', 'scroll', 'engagement'];
        // if (!validEventTypes.includes(requestData.type)) {
        //     utils.debugLog(`Invalid event type: ${requestData.type}. Must be one of: ${validEventTypes.join(', ')}`);
        //     return;
        // }

        // Validate timestamp
        if (isNaN(new Date(requestData.timestamp).getTime())) {
            utils.debugLog(`Invalid timestamp format: ${requestData.timestamp}`);
            return;
        }

        this.requestQueue.push({
            ...requestData,
            page: w.location.href,
            user: this.userId ? utils.hashUserId(this.userId) : null,
            session: utils.getSessionId(),
            retryCount: 0
        });
    } catch (error) {
        utils.debugLog('Request queueing failed:', error.message);
    }
}

startBatchTimer() {
    try {
        if (this.batchTimer) {
            clearInterval(this.batchTimer);
        }
        this.batchTimer = setInterval(() => {
            this.processBatchedRequests();
        }, internalConfig.batchInterval);
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
            isTrackingAllowed: this.isTrackingAllowed,
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

        if (!this.isTrackingAllowed) {
            utils.debugLog('Tracking is not allowed, skipping');
            return;
        }

        this.isProcessingQueue = true;
        this.processingQueueStartTime = Date.now();
        const batch = this.requestQueue.splice(0);

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

        setTimeout(() => {
            this.sendRequest(batch)
                .then(() => {
                    utils.debugLog('Batch sent successfully');
                    this.requestQueue = [];
                    this.resetProcessingState();
                    if (!isUnloading) {
                        this.updateCountdownTimer(internalConfig.batchInterval);
                    }
                })
                .catch(error => {
                    this.sendRequestFailures++;
                    utils.debugLog('Batch send error:', error.message);
                    
                    // Add retry count and requeue with exponential backoff
                    batch.forEach(request => {
                        request.retryCount = (request.retryCount || 0) + 1;
                        if (request.retryCount <= 3) {
                            const backoffDelay = Math.pow(2, request.retryCount - 1) * 1000;
                            setTimeout(() => {
                                this.requestQueue.unshift(request);
                            }, backoffDelay);
                            utils.debugLog(`Request scheduled for retry ${request.retryCount} in ${backoffDelay}ms`);
                        } else {
                            utils.debugLog('Request failed after maximum retries:', request);
                            this.storeOfflineRequest(config.host, [request], 'POST');
                        }
                    });
                    
                    this.resetProcessingState();
                });
        }, 0);
    } catch (error) {
        utils.debugLog('Failed to process batch:', error.message);
        this.resetProcessingState();
    }
}

sendRequest(events, method = 'POST') {
    try {
        // Validate events array
        if (!Array.isArray(events)) {
            utils.debugLog('Events must be an array');
            return Promise.reject();
        }

        if (!this.isTrackingAllowed) {
            utils.debugLog('Tracking is not allowed');
            return Promise.reject();
        }

        if (!events.length) {
            utils.debugLog('Events array is empty');
            return Promise.reject();
        }

        // Validate payload data
        if (!config.websiteId) {
            utils.debugLog('Missing required website ID');
            return Promise.reject();
        }

        const payload = {
            domain: w.location.hostname,
            websiteId: config.websiteId,
            ...requestParams,
            events
        };

        utils.debugLog('Sending request:', { url: config.host, method, payload });

        return fetch(`${config.host}/api/v1/event`, {
            method,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json', 
                'X-CSRF-Token': this.csrfToken || ''
            },
            body: JSON.stringify(payload),
            keepalive: true,
            credentials: 'include'
        })
        .then(response => {
            if (!response.ok) {
                utils.debugLog(`HTTP error! status: ${response.status}`);
                return Promise.reject();
            }
            this.sendRequestFailures = 0;
            this.requestsSent++;
            this.eventsTracked += events.length;
            utils.debugLog('Request successful');
            return response.json();
        })
        .catch(error => {
            this.sendRequestFailures++;
            utils.debugLog('Request failed:', error.message);
            if (!navigator.onLine) {
                this.storeOfflineRequest(config.host, events, method);
            }
            return Promise.reject();
        });
    } catch (error) {
        utils.debugLog('Send request failed:', error.message);
        return Promise.reject();
    }
}

storeOfflineRequest(url, events, method) {
    try {
        // Validate inputs
        if (!url) {
            utils.debugLog('Missing required URL parameter');
            return;
        }
        if (!events) {
            utils.debugLog('Missing required events parameter');
            return;
        }
        if (!method) {
            utils.debugLog('Missing required method parameter');
            return;
        }

        if (!Array.isArray(events)) {
            utils.debugLog('Events must be an array for offline storage');
            return;
        }

        const offlineRequests = JSON.parse(localStorage.getItem('rad_monitor_offline_requests') || '[]');
        const request = { url, events, method, timestamp: Date.now() };
        offlineRequests.push(request);
        
        try {
            localStorage.setItem('rad_monitor_offline_requests', JSON.stringify(offlineRequests));
        } catch (storageError) {
            utils.debugLog(`Failed to store offline request: ${storageError.message}`);
            return;
        }
        
        utils.debugLog('Stored offline request:', request);
    } catch (error) {
        utils.debugLog('Failed to store offline request:', error.message);
    }
}

sendOfflineRequests() {
    try {
        let offlineRequests;
        try {
            offlineRequests = JSON.parse(localStorage.getItem('rad_monitor_offline_requests') || '[]');
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

        return Promise.all(
            validRequests.map(request => this.sendRequest(request.events, request.method))
        )
        .then(() => {
            localStorage.removeItem('rad_monitor_offline_requests');
            utils.debugLog('Offline requests processed successfully');
        })
        .catch(error => {
            utils.debugLog('Offline request processing failed:', error.message);
        });
    } catch (error) {
        utils.debugLog('Failed to process offline requests:', error.message);
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
