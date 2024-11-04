const utils = {

    initializeEventListeners(enabledEvents) {
        const trimmedEvents = new Set(enabledEvents.map(event => event.trim()));
        utils.debugLog('Enabled events:', [...trimmedEvents]);

        Object.entries(eventHandlers).forEach(([eventType, listener]) => {
            if (trimmedEvents.has(eventType)) {
                utils.debugLog('Initializing event listener:', eventType);
                document.addEventListener(eventType, utils.throttle(listener, 1000));
            }
        });
    },

    getOS: () => {
        const ua = navigator.userAgent;
        if (/Android/.test(ua)) return 'Android';
        if (/iPhone|iPad|iPod/.test(ua)) return 'iOS'; 
        if (/Macintosh|Mac OS X/.test(ua)) return 'macOS';
        if (/Windows/.test(ua)) return 'Windows';
        if (/Linux/.test(ua)) return 'Linux';
        if (/CrOS/.test(ua)) return 'Chrome OS';
        if (/Firefox/.test(ua)) return 'Firefox OS';
        if (/BlackBerry|BB10/.test(ua)) return 'BlackBerry';
        if (/webOS/.test(ua)) return 'webOS';
        if (/Symbian|SymbOS/.test(ua)) return 'Symbian';
        return 'Unknown';
    },
    getUserAgent: () => {
        return navigator.userAgent;
    },
    getBrowserVersion: () => {
        const ua = navigator.userAgent;
        const match = ua.match(/(Opera|OPR|Edge|Chrome|Safari|Firefox|MSIE|Trident)[\s\/](\d+(\.\d+)?)/i) || ua.match(/(Version)[\s\/](\d+(\.\d+)?)/i);
        return match ? match[2] : 'Unknown';
    },

    getBrowser: () => {
        const ua = navigator.userAgent;
        if (/Opera|OPR/.test(ua)) return 'Opera';
        if (/Edg/.test(ua)) return 'Edge';
        if (/Chrome/.test(ua)) return 'Chrome';
        if (/Safari/.test(ua)) return 'Safari';
        if (/Firefox/.test(ua)) return 'Firefox';
        if (/MSIE|Trident/.test(ua)) return 'Internet Explorer';
        return 'Unknown';
    },
    updateEngagementScore(value) {
        this.engagementScore += value;
    },

    debugLog: (...args) => {
        if (internalConfig.isDebug) {
            console.log('[Rad Monitor Analytics Debug]', ...args);
        }
        if (internalConfig.isBrowserDebug) {
            const debugElement = document.getElementById('rad-monitor-debug');
            if (debugElement) {
                const logEntry = document.createElement('div');
                logEntry.textContent = `[${new Date().toISOString()}] ${args.join(' ')}`;
                debugElement.appendChild(logEntry);
            }
        }
    },
    getCountry: () => {
        try {
            // Try to get country from timezone
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            if (timezone) {
                const region = timezone.split('/')[0];
                if (region === 'America') return 'US';
                if (region === 'Europe') return 'EU';
                if (region === 'Asia') return 'AS';
            }

            // Try to get country from language
            const language = navigator.language || navigator.userLanguage;
            if (language) {
                const country = language.split('-')[1];
                if (country) return country.toUpperCase();
            }

            // Fallback to US if no other info available
            return 'US';
        } catch (e) {
            utils.debugLog('Error getting country:', e);
            return 'US';
        }
    },
    getLanguage: () => {
        try {
            // Try navigator.languages first for full list of preferred languages
            if (navigator.languages && navigator.languages.length) {
                return navigator.languages[0];
            }

            // Fall back to navigator.language or navigator.userLanguage
            if (navigator.language) {
                return navigator.language;
            }

            if (navigator.userLanguage) {
                return navigator.userLanguage;
            }

            // Try browserLanguage and systemLanguage as last resorts
            if (navigator.browserLanguage) {
                return navigator.browserLanguage;
            }

            if (navigator.systemLanguage) {
                return navigator.systemLanguage;
            }

            // Check HTML lang attribute
            const htmlLang = document.documentElement.lang;
            if (htmlLang) {
                return htmlLang;
            }

            return 'unknown';
        } catch (e) {
            utils.debugLog('Error getting language:', e);
            return 'unknown';
        }
    },
    getCity: () => {
        try {
            // Try to get city from timezone
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            if (timezone) {
                const city = timezone.split('/')[1];
                if (city) {
                    return city.replace(/_/g, ' ');
                }
            }
            return null;
        } catch (e) {
            utils.debugLog('Error getting city:', e);
            return null;
        }
    },
    getIpAddress: () => {
        try {
            // Try WebRTC first
            if (window.RTCPeerConnection) {
                const pc = new RTCPeerConnection({iceServers: []});
                pc.createDataChannel('');
                pc.createOffer()
                    .then(pc.setLocalDescription.bind(pc))
                    .catch(() => {});
                pc.onicecandidate = (ice) => {
                    if (ice && ice.candidate && ice.candidate.candidate) {
                        const ipMatch = /([0-9]{1,3}(\.[0-9]{1,3}){3}|[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7})/.exec(ice.candidate.candidate);
                        if (ipMatch) {
                            return ipMatch[1];
                        }
                    }
                };
            }

            // Fallback to navigator.connection if available
            if (window.navigator.connection && window.navigator.connection.remoteAddress) {
                return window.navigator.connection.remoteAddress;
            }

            // Try navigator.userAgent for additional info
            if (window.navigator.userAgent) {
                const uaMatch = /\b(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(?:\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)){3}\b/.exec(window.navigator.userAgent);
                if (uaMatch) {
                    return uaMatch[0];
                }
            }

            return null;
        } catch (e) {
            utils.debugLog('Error getting IP address:', e);
            return null;
        }
    },
    getLargestContentfulPaint: () => {
        return new Promise(resolve => {
            new PerformanceObserver((entryList) => {
                const entries = entryList.getEntries();
                const lastEntry = entries[entries.length - 1];
                resolve(lastEntry ? lastEntry.startTime : undefined);
            }).observe({ type: 'largest-contentful-paint', buffered: true });
        });
    },

    debounce: (func, delay) => {
        let debounceTimer;
        return function() {
            const context = this, args = arguments;
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => func.apply(context, args), delay);
        };
    },

    throttle: (func, limit) => {
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
    },


    generateSessionId: () => {
        const array = new Uint8Array(16);
        crypto.getRandomValues(array);
        return Array.from(array, byte => byte.toString(16).padStart(2, '0')).join('');
    },

    getSessionId: () => {
        const now = Date.now();
        let sessionId;
        let storedSessionId = w.localStorage.getItem('rad_monitor_session_id');
        let storedSessionStart = parseInt(w.localStorage.getItem('rad_monitor_session_start'), 10);

        if (!storedSessionId || isNaN(storedSessionStart) || (now - storedSessionStart > internalConfig.SESSION_DURATION)) {
            sessionId = utils.generateSessionId();
            w.localStorage.setItem('rad_monitor_session_id', sessionId);
            w.localStorage.setItem('rad_monitor_session_start', now.toString());
            
        } else {
            sessionId = storedSessionId;
        }
        let lastActivity = now;
        return sessionId;
    },

    anonymize: {
        ip: ip => ip ? ip.split('.').slice(0, 2).join('.') + '.x.x' : '',
        userAgent: ua => ua.replace(/\d+/g, 'X'),
        url: url => {
            try {
                const urlObj = new URL(url);
                urlObj.search = '';
                urlObj.hash = '';
                return urlObj.toString();
            } catch (error) {
                utils.debugLog('Invalid URL:', url);
                return url;
            }
        }
    },

    hashUserId: (id) => {
        // Implement a secure hashing function here
        return btoa(id);
    },

    sanitizeEventData: (data) => {
        const sanitized = { ...data };
        delete sanitized.email;
        delete sanitized.phone;
        delete sanitized.address;
        return sanitized;
    }
};
