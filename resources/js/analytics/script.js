(function (w) {
    'use strict';

    const config = {
        isDebug: false,
        SESSION_DURATION: 30 * 60 * 1000,
        batchSize: 10,
        batchInterval: 1000,
        errorTrackingEndpoint: 'https://your-error-tracking-service.com/api/errors',
        consentCookieName: 'pa_consent'
    };

    let trackingCode, params = {}, sessionId = null, lastActivity = Date.now();
    const requestQueue = [];
    let isProcessingQueue = false;

    if (new URLSearchParams(w.location.search).get('debug') === 'true') toggleDebug(true);

    const debugLog = (...args) => config.isDebug && console.log('[Analytics Debug]', ...args);

    const generateSessionId = () => 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
        const r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
    
    const getSessionId = () => {
        const now = Date.now();
        if (!sessionId || (now - lastActivity > config.SESSION_DURATION)) {
            sessionId = generateSessionId();
            w.localStorage.setItem('pa_session_id', sessionId);
            w.localStorage.setItem('pa_session_start', now.toString());
            debugLog('New session started:', sessionId);
        } else {
            sessionId = w.localStorage.getItem('pa_session_id');
        }
        lastActivity = now;
        return sessionId;
    };
    
    const toggleDebug = value => {
        config.isDebug = value;
        debugLog('Debug mode ' + (config.isDebug ? 'enabled' : 'disabled'));
    };
    const anonymize = {
        ip: ip => ip.split('.').slice(0, 3).join('.') + '.0',
        userAgent: ua => ua.replace(/\d+/g, 'X'),
        url: url => {
            const parsedUrl = new URL(url);
            parsedUrl.search = '';
            parsedUrl.hash = '';
            return parsedUrl.toString();
        }
    };
    
    const encryptData = data => btoa(JSON.stringify(data)); // Placeholder encryption
        
    const getAuthToken = () => {
        const storedToken = localStorage.getItem('authToken');
        const tokenExpiry = localStorage.getItem('tokenExpiry');
        
        return (storedToken && tokenExpiry && new Date().getTime() < parseInt(tokenExpiry)) 
            ? Promise.resolve(storedToken) 
            : fetchNewAuthToken();
    };
    
    const fetchNewAuthToken = () => {
        if (!trackingCode) {
            debugLog('No tracking code found. Cannot fetch auth token.');
            return Promise.resolve(null);
        }
        const apiKey = trackingCode.getAttribute('data-api-key');
        const tokenEndpoint = `${trackingCode.getAttribute('data-host')}/api/auth/token`;
    
        return fetch(tokenEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-API-Key': apiKey
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Failed to fetch auth token');
            return response.json();
        })
        .then(data => {
            localStorage.setItem('authToken', data.token);
            localStorage.setItem('tokenExpiry', new Date().getTime() + data.expiresIn * 1000);
            return data.token;
        })
        .catch(error => {
            logError(error, { function: 'fetchNewAuthToken' });
            return null;
        });
    };
    
    const logError = (error, context) => {
        console.error('[Analytics Error]', error);
        if (!trackingCode) {
            debugLog('No tracking code found. Error not logged.');
            return;
        }
        queueRequest({
            endpoint: config.errorTrackingEndpoint,
            data: {
                message: error.message,
                stack: error.stack,
                timestamp: new Date().toISOString(),
                context: context || {},
                userAgent: anonymize.userAgent(navigator.userAgent),
                url: anonymize.url(w.location.href)
            },
            method: 'POST'
        });
    };

    const sendEncryptedRequest = (url, data, method = 'POST') => {
        if (!trackingCode) {
            debugLog('No tracking code found. Request not sent.');
            return Promise.resolve();
        }
        return getAuthToken()
            .then(token => {
                if (!token) throw new Error('No auth token available');
                return fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: encryptData(data)
                });
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .catch(error => {
                logError(error, { function: 'sendEncryptedRequest', url: url });
                if (!navigator.onLine) storeOfflineRequest(url, data, method);
            });
    };

    const storeOfflineRequest = (url, data, method) => {
        const request = indexedDB.open('OfflineAnalyticsDB', 1);
        request.onerror = event => console.error('IndexedDB error:', event.target.error);
        request.onsuccess = event => {
            const db = event.target.result;
            const transaction = db.transaction(['offlineRequests'], 'readwrite');
            const objectStore = transaction.objectStore('offlineRequests');
            objectStore.add({ url, data, method, timestamp: new Date().toISOString() })
                .onerror = event => console.error('Error storing offline request:', event.target.error);
        };
        request.onupgradeneeded = event => {
            event.target.result.createObjectStore('offlineRequests', { autoIncrement: true });
        };
    };

    const sendStoredRequests = () => {
        if (!trackingCode) {
            debugLog('No tracking code found. Stored requests not sent.');
            return;
        }
        const request = indexedDB.open('OfflineAnalyticsDB', 1);
        request.onsuccess = event => {
            const db = event.target.result;
            const transaction = db.transaction(['offlineRequests'], 'readwrite');
            const objectStore = transaction.objectStore('offlineRequests');
            objectStore.getAll().onsuccess = event => {
                event.target.result.forEach(storedRequest => {
                    queueRequest(storedRequest);
                    objectStore.delete(storedRequest.id);
                });
            };
        };
    };

    w.addEventListener('online', sendStoredRequests);

    const queueRequest = (requestData) => {
        if (!trackingCode) {
            debugLog('No tracking code found. Request not queued.');
            return;
        }
        requestQueue.push(requestData);
        if (!isProcessingQueue) {
            processBatchedRequests();
        }
    };

    const processBatchedRequests = () => {
        if (!trackingCode || isProcessingQueue || requestQueue.length === 0) return;

        isProcessingQueue = true;
        const batch = requestQueue.splice(0, config.batchSize);

        debugLog('Sending batch of', batch.length, 'requests');

        sendEncryptedRequest(`${trackingCode.getAttribute('data-host')}/api/batch-event`, batch)
            .then(() => debugLog('Batch request sent successfully'))
            .catch(error => {
                logError(error, { function: 'processBatchedRequests', stage: 'fetch' });
                requestQueue.unshift(...batch);
            })
            .finally(() => {
                isProcessingQueue = false;
                if (requestQueue.length > 0) {
                    setTimeout(processBatchedRequests, config.batchInterval);
                }
            });
    };

    const getUserConsent = () => {
        const consent = w.localStorage.getItem(config.consentCookieName);
        return consent === 'true';
    };

    const setUserConsent = (value) => {
        w.localStorage.setItem(config.consentCookieName, value);
    };

    const sendRequest = (event, referrer) => {
        if (!trackingCode) {
            debugLog('No tracking code found. Request not sent.');
            return false;
        }

        if (!getUserConsent()) {
            debugLog('User consent not given. Request not sent.');
            return false;
        }

        if (trackingCode.getAttribute('data-dnt') === 'true' && (navigator.doNotTrack === '1' || w.doNotTrack === '1')) {
            debugLog('Do Not Track is enabled. Request not sent.');
            return false;
        }

        if (!params.referrer) {
            params = {
                referrer: anonymize.url(w.document.referrer),
                page: anonymize.url(w.location.href),
                screen_resolution: `${screen.width}x${screen.height}`,
                language: navigator.language || navigator.userLanguage,
                user_agent: anonymize.userAgent(navigator.userAgent),
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                color_depth: screen.colorDepth,
                device_memory: 'unknown',
                hardware_concurrency: 'unknown',
                connection_type: (navigator.connection && navigator.connection.effectiveType) || 'unknown'
            };
        }

        const requestParams = {
            ...params,
            referrer: referrer ? anonymize.url(referrer) : params.referrer,
            timestamp: new Date().toISOString(),
            event,
            viewport_width: w.innerWidth,
            viewport_height: w.innerHeight,
            page_title: document.title,
            url_path: anonymize.url(w.location.pathname),
            url_query: '',
            session_id: getSessionId(),
            is_new_session: sessionId !== w.localStorage.getItem('pa_session_id')
        };

        if (w.performance && w.performance.getEntriesByType) {
            try {
                const perfEntry = w.performance.getEntriesByType('navigation')[0];
                if (perfEntry) {
                    Object.assign(requestParams, {
                        load_time: Math.round(perfEntry.loadEventEnd - perfEntry.startTime),
                        dom_ready_time: Math.round(perfEntry.domContentLoadedEventEnd - perfEntry.startTime),
                        time_to_first_byte: Math.round(perfEntry.responseStart - perfEntry.requestStart),
                        dns_lookup_time: Math.round(perfEntry.domainLookupEnd - perfEntry.domainLookupStart),
                        tcp_connect_time: Math.round(perfEntry.connectEnd - perfEntry.connectStart)
                    });
                }
            } catch (error) {
                logError(error, { function: 'sendRequest', stage: 'performance metrics' });
            }
        }

        debugLog('Queueing request with params:', requestParams);
        queueRequest({
            endpoint: `${trackingCode.getAttribute('data-host')}/api/event`,
            data: requestParams,
            method: 'POST'
        });
    };

    const throttle = (func, limit) => {
        let lastFunc, lastRan;
        return function() {
            const context = this, args = arguments;
            if (!lastRan) {
                func.apply(context, args);
                lastRan = Date.now();
            } else {
                clearTimeout(lastFunc);
                lastFunc = setTimeout(() => {
                    if ((Date.now() - lastRan) >= limit) {
                        func.apply(context, args);
                        lastRan = Date.now();
                    }
                }, limit - (Date.now() - lastRan));
            }
        };
    };

    const debounce = (func, delay) => {
        let debounceTimer;
        return function() {
            const context = this, args = arguments;
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => func.apply(context, args), delay);
        };
    };

    try {
        debugLog('Initializing analytics');

        trackingCode = document.getElementById('ZwSg9rf6GA');
        if (!trackingCode) {
            debugLog('No tracking code found. Analytics not initialized.');
            return;
        }

        const enabledEvents = (trackingCode.getAttribute('data-events') || 'scroll,click,load,beforeunload').split(',');

        const originalPushState = history.pushState;
        history.pushState = function () {
            const referrer = anonymize.url(w.location.href);
            originalPushState.apply(history, arguments);
            debugLog('Push state detected');
            sendRequest(null, referrer);
        };

        w.onpopstate = () => {
            debugLog('Popstate event detected');
            sendRequest(null);
        };

        w.addEventListener('load', () => {
            const script = document.createElement('script');
            script.src = `${trackingCode.getAttribute('data-host')}/js/analytics-event-listeners.js`;
            script.onload = () => {
                if (typeof initializeEventListeners === 'function') {
                    initializeEventListeners(w, sendRequest, debugLog, throttle, debounce, enabledEvents);
                } else {
                    console.error('initializeEventListeners function not found in the loaded script');
                }
            };
            document.head.appendChild(script);
        });

        const startTime = Date.now();

        w.pa = {
            track: (eventName, eventValue) => {
                if (getUserConsent()) {
                    debugLog('Custom event tracked:', eventName, eventValue);
                    sendRequest({name: eventName, value: eventValue});
                } else {
                    debugLog('User consent not given. Custom event not tracked.');
                }
            },
            toggleDebug: toggleDebug,
            setConsent: (value) => {
                setUserConsent(value);
                debugLog('User consent set to:', value);
            },
            getConsent: getUserConsent
        };

        if (getUserConsent()) {
            debugLog('Sending initial request');
            sendRequest(null);

            sendRequest({
                name: 'browser_info',
                value: {
                    cookiesEnabled: navigator.cookieEnabled,
                    onLine: navigator.onLine,
                    platform: navigator.platform
                }
            });

            if (w.matchMedia && enabledEvents.includes('mediaQuery')) {
                const mediaQueries = {
                    'prefers-reduced-motion': '(prefers-reduced-motion: reduce)',
                    'prefers-dark-mode': '(prefers-color-scheme: dark)'
                };

                Object.entries(mediaQueries).forEach(([preference, query]) => {
                    const mediaQuery = w.matchMedia(query);
                    sendRequest({name: `user_preference_${preference}`, value: mediaQuery.matches});
                    mediaQuery.addEventListener('change', (e) => {
                        debugLog(`${preference} changed:`, e.matches);
                        sendRequest({name: `${preference}_change`, value: e.matches});
                    });
                });
            }

            if (enabledEvents.includes('sessionDuration')) {
                setInterval(() => {
                    const sessionDuration = Math.round((Date.now() - parseInt(w.localStorage.getItem('pa_session_start'))) / 1000);
                    debugLog('Session duration:', sessionDuration);
                    sendRequest({name: 'session_duration', value: sessionDuration});
                }, 60000);
            }

            if (enabledEvents.includes('formAbandonment')) {
                document.querySelectorAll('form').forEach(form => {
                    form.addEventListener('focusout', () => {
                        if (!form.checkValidity()) {
                            debugLog('Form abandoned:', form.id || 'unnamed_form');
                            sendRequest({ name: 'form_abandonment', value: form.id || 'unnamed_form' });
                        }
                    });
                });
            }
        
            ['mousedown', 'keydown', 'touchstart', 'scroll'].forEach(eventType => {
                w.addEventListener(eventType, getSessionId, { passive: true });
            });

            if (enabledEvents.includes('networkInfo')) {
                w.addEventListener('online', () => {
                    debugLog('Network status changed: online');
                    sendRequest({ name: 'network_status', value: 'online' });
                });
                w.addEventListener('offline', () => {
                    debugLog('Network status changed: offline');
                    sendRequest({ name: 'network_status', value: 'offline' });
                });
            }

            if (enabledEvents.includes('pageVisibility')) {
                document.addEventListener('visibilitychange', () => {
                    const visibility = document.hidden ? 'hidden' : 'visible';
                    debugLog('Page visibility changed:', visibility);
                    sendRequest({ name: 'page_visibility', value: visibility });
                });
            }

            if (enabledEvents.includes('deviceOrientation') && w.DeviceOrientationEvent) {
                w.addEventListener('deviceorientation', (event) => {
                    debugLog('Device orientation changed');
                    sendRequest({
                        name: 'device_orientation',
                        value: {
                            alpha: event.alpha,
                            beta: event.beta,
                            gamma: event.gamma
                        }
                    });
                }, { passive: true });
            }

            if (enabledEvents.includes('batteryStatus') && navigator.getBattery) {
                navigator.getBattery().then(battery => {
                    const updateBatteryStatus = () => {
                        debugLog('Battery status updated');
                        sendRequest({
                            name: 'battery_status',
                            value: {
                                level: battery.level,
                                charging: battery.charging
                            }
                        });
                    };

                    battery.addEventListener('chargingchange', updateBatteryStatus);
                    battery.addEventListener('levelchange', updateBatteryStatus);
                    updateBatteryStatus(); // Initial status
                });
            }
        }

    } catch (e) {
        console.error('Analytics error:', e.message);
    }
})(window);
