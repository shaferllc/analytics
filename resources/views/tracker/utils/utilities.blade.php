const utils = {

    getDevicePixelRatio: () => {
        // Get the device pixel ratio, accounting for high DPI displays
        const ratio = window.devicePixelRatio ||
                     window.screen.deviceXDPI / window.screen.logicalXDPI ||
                     window.matchMedia('(-webkit-device-pixel-ratio: 2)').matches ? 2 : 1;

        // Round to 2 decimal places for consistency
        return Math.round(ratio * 100) / 100;
    },
    getUniqueId: () => {
        // Generate a random unique ID using timestamp and random number that will be unique until page reload
        const rawId = Date.now().toString(36) + Math.random().toString(36).substring(2) + (window.clientInformation?.userAgentData?.platform || navigator.platform || '').replace(/\s+/g, '');
        const encoder = new TextEncoder();
        const data = encoder.encode(rawId);
        return window.btoa(String.fromCharCode.apply(null, new Uint8Array(data))).replace(/[+/]/g, char => char === '+' ? '-' : '_').replace(/=/g, '');
    },
    getDevice: () => {
        const ua = navigator.userAgent;
        const platform = navigator.platform;

        // Check for mobile devices first
        if (/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(ua)) {
            return 'tablet';
        }
        if (/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(ua)) {
            return 'mobile';
        }

        // Check for gaming consoles
        if (/Xbox|PlayStation|Nintendo|Wii|PLAYSTATION|PS4|PS5|Nintendo Switch/.test(ua)) {
            return 'console';
        }

        // Check for smart TVs
        if (/smart-tv|SmartTV|SMART-TV|TV Safari|WebTV|HbbTV|NetCast|NETTV|AppleTV|boxee|Kylo|Roku|DLNADOC|CE\-HTML/i.test(ua)) {
            return 'tv';
        }

        // Check for wearables
        if (/watch|glass|oculus|vive|hololens/i.test(ua)) {
            return 'wearable';
        }

        // Check for e-readers
        if (/Kindle|Nook|KoboTouch/i.test(ua)) {
            return 'e-reader';
        }

        // Check for bots/crawlers
        if (/bot|crawler|spider|crawling|googlebot|bingbot|yandexbot/i.test(ua)) {
            return 'bot';
        }

        // Check for embedded/IoT devices
        if (/raspberry|arduino|esp8266/i.test(ua)) {
            return 'iot';
        }

        // Default to desktop but check platform for more specifics
        if (/linux/i.test(platform)) return 'desktop-linux';
        if (/mac/i.test(platform)) return 'desktop-mac';
        if (/win/i.test(platform)) return 'desktop-windows';

        return 'desktop-other';
    },
    getCampaign: () => {
        const referrer = document.referrer;
        if (!referrer) return null;

        try {
            const url = new URL(referrer);
            const params = new URLSearchParams(url.search);
            const currentParams = new URLSearchParams(window.location.search);

            // Check current URL UTM parameters first
            const currentUtmCampaign = currentParams.get('utm_campaign');
            if (currentUtmCampaign) return currentUtmCampaign;

            const currentUtmSource = currentParams.get('utm_source');
            if (currentUtmSource) return currentUtmSource;

            const currentUtmMedium = currentParams.get('utm_medium');
            if (currentUtmMedium) return currentUtmMedium;

            // Check referrer UTM parameters
            const utmCampaign = params.get('utm_campaign');
            if (utmCampaign) return utmCampaign;

            const utmSource = params.get('utm_source');
            if (utmSource) return utmSource;

            const utmMedium = params.get('utm_medium');
            if (utmMedium) return utmMedium;

            const utmTerm = params.get('utm_term');
            if (utmTerm) return utmTerm;

            const utmContent = params.get('utm_content');
            if (utmContent) return utmContent;

            // Check for click IDs
            const fbclid = params.get('fbclid') || currentParams.get('fbclid'); // Facebook
            if (fbclid) return 'facebook';

            const gclid = params.get('gclid') || currentParams.get('gclid'); // Google
            if (gclid) return 'google';

            const msclkid = params.get('msclkid') || currentParams.get('msclkid'); // Microsoft
            if (msclkid) return 'microsoft';

            const ttclid = params.get('ttclid') || currentParams.get('ttclid'); // TikTok
            if (ttclid) return 'tiktok';

            const twclid = params.get('twclid') || currentParams.get('twclid'); // Twitter
            if (twclid) return 'twitter';

            const dclid = params.get('dclid') || currentParams.get('dclid'); // DoubleClick
            if (dclid) return 'doubleclick';

            const pinid = params.get('pinid') || currentParams.get('pinid'); // Pinterest
            if (pinid) return 'pinterest';

            const snapid = params.get('snapid') || currentParams.get('snapid'); // Snapchat
            if (snapid) return 'snapchat';

            // Check domain for common ad platforms
            const domain = url.hostname.toLowerCase();
            if (domain.includes('doubleclick')) return 'google-ads';
            if (domain.includes('facebook') || domain.includes('fb.com')) return 'facebook-ads';
            if (domain.includes('linkedin')) return 'linkedin-ads';
            if (domain.includes('twitter')) return 'twitter-ads';
            if (domain.includes('tiktok')) return 'tiktok-ads';
            if (domain.includes('pinterest')) return 'pinterest-ads';
            if (domain.includes('snapchat')) return 'snapchat-ads';
            if (domain.includes('reddit')) return 'reddit-ads';
            if (domain.includes('quora')) return 'quora-ads';
            if (domain.includes('instagram')) return 'instagram-ads';
            if (domain.includes('youtube')) return 'youtube-ads';
            if (domain.includes('taboola')) return 'taboola-ads';
            if (domain.includes('outbrain')) return 'outbrain-ads';
            if (domain.includes('criteo')) return 'criteo-ads';
            if (domain.includes('adroll')) return 'adroll-ads';

            // Check for email marketing platforms
            if (domain.includes('mailchimp')) return 'mailchimp';
            if (domain.includes('sendgrid')) return 'sendgrid';
            if (domain.includes('constantcontact')) return 'constant-contact';
            if (domain.includes('klaviyo')) return 'klaviyo';
            if (domain.includes('hubspot')) return 'hubspot';

            return null;
        } catch (e) {
            return null;
        }
    },
    getSocialNetwork: () => {
        const referrer = document.referrer;
        if (!referrer) return null;

        const socialNetworks = {
            'facebook.': 'Facebook',
            'twitter.': 'Twitter',
            'linkedin.': 'LinkedIn',
            'instagram.': 'Instagram',
            'pinterest.': 'Pinterest',
            'reddit.': 'Reddit',
            'tumblr.': 'Tumblr',
            'youtube.': 'YouTube',
            'tiktok.': 'TikTok',
            'snapchat.': 'Snapchat',
            'whatsapp.': 'WhatsApp',
            'telegram.': 'Telegram',
            'medium.': 'Medium',
            'vk.': 'VKontakte',
            'weibo.': 'Weibo',
            'line.': 'LINE',
            'discord.': 'Discord',
            'quora.': 'Quora',
            'mastodon.': 'Mastodon'
        };

        try {
            const url = new URL(referrer);
            const domain = url.hostname.toLowerCase();
            for (const [key, engine] of Object.entries(socialNetworks)) {
                if (domain.includes(key)) return engine;
            }
            return null;
        } catch (e) {
            return null;
        }
    },
    getSearchEngine: () => {
        const referrer = document.referrer;
        if (!referrer) return 'Direct';

        const searchEngines = {
            'google.': 'Google',
            'bing.': 'Bing',
            'yahoo.': 'Yahoo',
            'duckduckgo.': 'DuckDuckGo',
            'yandex.': 'Yandex',
            'baidu.': 'Baidu',
            'ecosia.': 'Ecosia',
            'qwant.': 'Qwant',
            'startpage.': 'StartPage',
            'brave.': 'Brave Search',
            'seznam.': 'Seznam',
            'naver.': 'Naver',
            'sogou.': 'Sogou',
            'ask.': 'Ask.com',
            'aol.': 'AOL'
        };

        try {
            const url = new URL(referrer);
            const domain = url.hostname.toLowerCase();

            for (const [key, engine] of Object.entries(searchEngines)) {
                if (domain.includes(key)) return engine;
            }

            return null;
        } catch (e) {
            return null;
        }
    },
    getTimezone: () => {
        try {
            // Get timezone using Intl API
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            if (timezone) {
                return timezone;
            }

            // Fallback to offset if timezone not available
            const offset = new Date().getTimezoneOffset();
            const hours = Math.abs(Math.floor(offset / 60));
            const minutes = Math.abs(offset % 60);
            const sign = offset < 0 ? '+' : '-';

            return `UTC${sign}${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
        } catch (e) {
            utils.debugLog('Error getting timezone:', e);
            return 'UTC';
        }
    },
    getContinent: () => {
        const timezone = utils.getTimezone() || 'UTC';
        const continentMap = {
            'America': 'North America',
            'Europe': 'Europe',
            'Asia': 'Asia',
            'Africa': 'Africa',
            'Australia': 'Oceania',
            'Pacific': 'Oceania',
            'Indian': 'Asia',
            'Atlantic': 'Europe'
        };
        const continent = timezone.split('/')[0];
        return continentMap[continent] || 'Unknown';
    },
    getLanguage: () => {
        try {
            // Try navigator.languages first for full list of preferred languages
            if (navigator.languages && navigator.languages.length) {
                return navigator.languages[0];
            }

            // Fall back to navigator.language
            if (navigator.language) {
                return navigator.language;
            }

            // Check HTML lang attribute
            const htmlLang = document.documentElement.lang;
            if (htmlLang) {
                return htmlLang;
            }

            // Last resort - check meta tags
            const metaLang = document.querySelector('meta[http-equiv="content-language"]');
            if (metaLang) {
                return metaLang.content;
            }

            return 'unknown';
        } catch (e) {
            utils.debugLog('Error getting language:', e);
            return 'unknown';
        }
    },
    getResolution: () => {
        const width = window.screen.width || window.innerWidth;
        const height = window.screen.height || window.innerHeight;
        const pixelRatio = window.devicePixelRatio || 1;
        const colorDepth = window.screen.colorDepth || window.screen.pixelDepth || 'unknown';

        return {
            width,
            height,
            ratio: pixelRatio,
            colorDepth,
            formatted: `${width}x${height}@${pixelRatio}x`
        }.formatted;
    },

    getUserId: () => {
        try {
            return TSMonitor.instance.userId;
        } catch (error) {
            utils.debugLog('Failed to get/generate user ID:', error.message);
            return null;
        }
    },



    getOS: () => {
        const ua = navigator.userAgent;
        const platform = navigator.platform;

        // Mobile OS detection with version
        if (/Android/.test(ua)) {
            const version = ua.match(/Android\s([0-9.]+)/);
            return `Android${version ? ' ' + version[1] : ''}`;
        }
        if (/iPhone|iPad|iPod/.test(ua)) {
            const version = ua.match(/OS\s([0-9_]+)/);
            return `iOS${version ? ' ' + version[1].replace(/_/g, '.') : ''}`;
        }

        // Desktop OS detection with version
        if (/Macintosh|Mac OS X/.test(ua)) {
            const version = ua.match(/Mac OS X\s([0-9_]+)/);
            return `macOS${version ? ' ' + version[1].replace(/_/g, '.') : ''}`;
        }
        if (/Windows/.test(ua)) {
            const version = ua.match(/Windows NT\s([0-9.]+)/);
            const versionMap = {
                '10.0': '10/11',
                '6.3': '8.1',
                '6.2': '8',
                '6.1': '7',
                '6.0': 'Vista',
                '5.2': 'XP x64',
                '5.1': 'XP',
            };
            return `Windows${version ? ' ' + (versionMap[version[1]] || version[1]) : ''}`;
        }
        if (/Linux/.test(ua)) {
            const distro = ua.match(/\((.*?)\)/);
            return `Linux${distro ? ' ' + distro[1].split(';')[0] : ''}`;
        }

        // Other OS detection with additional info
        if (/CrOS/.test(ua)) {
            const version = ua.match(/CrOS\s\w*\s([0-9.]+)/);
            return `Chrome OS${version ? ' ' + version[1] : ''}`;
        }
        if (/Firefox/.test(ua)) {
            const version = ua.match(/Firefox\/([0-9.]+)/);
            return `Firefox OS${version ? ' ' + version[1] : ''}`;
        }
        if (/BlackBerry|BB10/.test(ua)) {
            const version = ua.match(/Version\/([0-9.]+)/);
            return `BlackBerry${version ? ' ' + version[1] : ''}`;
        }
        if (/webOS/.test(ua)) {
            const version = ua.match(/webOS\/([0-9.]+)/);
            return `webOS${version ? ' ' + version[1] : ''}`;
        }
        if (/Symbian|SymbOS/.test(ua)) {
            const version = ua.match(/Symbian\/([0-9.]+)/);
            return `Symbian${version ? ' ' + version[1] : ''}`;
        }

        return `Unknown (${platform})`;
    },
    getUserAgent: () => {
        const ua = navigator.userAgent;
        const platform = navigator.platform;
        const vendor = navigator.vendor;
        const language = navigator.language;
        const cookieEnabled = navigator.cookieEnabled;
        const doNotTrack = navigator.doNotTrack;
        const maxTouchPoints = navigator.maxTouchPoints;
        const pdfViewerEnabled = navigator.pdfViewerEnabled;
        const webdriver = navigator.webdriver;

        return {
            userAgent: ua,
            platform: platform,
            vendor: vendor,
            language: language,
            cookieEnabled: cookieEnabled,
            doNotTrack: doNotTrack,
            maxTouchPoints: maxTouchPoints,
            pdfViewerEnabled: pdfViewerEnabled,
            webdriver: webdriver
        };
    },


    getElementZLevel: (element) => {
        if (!element) return 0;

        let zIndex = 0;
        let currentElement = element;

        while (currentElement && currentElement !== document) {
            // Get computed style
            const style = window.getComputedStyle(currentElement);

            // Check if element is positioned
            const position = style.position;
            if (position !== 'static') {
                // Get z-index if it's set
                const elementZIndex = parseInt(style.zIndex);
                if (!isNaN(elementZIndex)) {
                    zIndex = Math.max(zIndex, elementZIndex);
                }
            }

            // Check parent element
            currentElement = currentElement.parentElement;
        }

        return zIndex;
    },


    getCountry: () => {
        try {
            // Try to get country from timezone
            const timezone = utils.getTimezone();
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

    getCity: () => {
        try {
            // Try to get city from timezone
            const timezone = utils.getTimezone();
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
    getLandingPage: () => {
        try {
            // Get the full URL including protocol, hostname, path and query string
            const fullUrl = w.location.href;

            // Get entry page from session storage if exists
            const storedLandingPage = sessionStorage.getItem('landingPage');

            // If this is first page view in session, store and return current URL
            if (!storedLandingPage) {
                // Parse URL to get path and query
                const url = new URL(fullUrl);
                const landingPage = url.pathname + url.search;

                // Store landing page path+query in session storage
                sessionStorage.setItem('landingPage', landingPage);
                return landingPage;
            }

            // Otherwise return stored landing page
            return storedLandingPage;

        } catch (e) {
            utils.debugLog('Error getting landing page:', e);
            // Return current path+query as fallback
            const url = new URL(w.location.href);
            return url.pathname + url.search;
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

    getUrlQuery: () => {
        try {
            const search = w.location.search;
            if (!search) return '';

            const params = new URLSearchParams(search);

            // Remove sensitive parameters
            const sensitiveParams = ['token', 'key', 'password', 'secret', 'auth'];
            sensitiveParams.forEach(param => {
                if (params.has(param)) {
                    params.delete(param);
                }
            });

            return params.toString();
        } catch (e) {
            utils.debugLog('Error getting URL query:', e);
            return '';
        }
    },

    calculateEngagementScore: (duration) => {
        // Base score starts at 0
        let score = 0;

        // Duration thresholds in milliseconds
        const thresholds = {
            minimal: 1000,    // 1 second
            brief: 5000,      // 5 seconds
            moderate: 15000,  // 15 seconds
            extended: 30000   // 30 seconds
        };

        // Add points based on hover duration
        if (duration >= thresholds.extended) {
            score += 100;
        } else if (duration >= thresholds.moderate) {
            score += 75;
        } else if (duration >= thresholds.brief) {
            score += 50;
        } else if (duration >= thresholds.minimal) {
            score += 25;
        }

        // Normalize score between 0 and 1
        return Math.min(Math.max(score / 100, 0), 1);
    },
    getEngagementMetrics: () => {
        // Get scroll depth metrics
        const scrollDepth = {
            maxDepth: Math.max(
                (window.pageYOffset + window.innerHeight) / document.documentElement.scrollHeight * 100,
                0
            ),
            currentDepth: (window.pageYOffset / document.documentElement.scrollHeight) * 100
        };

        // Get time on page metrics
        const timeOnPage = {
            total: Date.now() - performance.timing.navigationStart,
            active: document.visibilityState === 'visible' ?
                Date.now() - performance.timing.navigationStart : 0
        };

        // Get interaction metrics
        const interactions = {
            clicks: window.clickCount || 0,
            scrolls: window.scrollCount || 0,
            keyPresses: window.keypressCount || 0,
            mouseMovements: window.mousemoveCount || 0
        };

        // Get viewport metrics
        const viewport = {
            width: window.innerWidth,
            height: window.innerHeight,
            ratio: window.devicePixelRatio,
            orientation: window.orientation || 'landscape'
        };

        // Calculate engagement score based on multiple factors
        const score = (
            (Math.min(scrollDepth.maxDepth / 100, 1) * 0.3) + // 30% weight for scroll depth
            (Math.min(timeOnPage.total / 60000, 1) * 0.3) + // 30% weight for time on page (max 1 min)
            (Math.min((interactions.clicks + interactions.scrolls) / 10, 1) * 0.4) // 40% weight for interactions
        );

        return {
            scrollDepth,
            timeOnPage,
            interactions,
            viewport,
            score: Math.min(Math.max(score, 0), 1) // Normalize between 0 and 1
        };
    },
    getViewportHeight: () => {
        try {
            // Get viewport height using different browser-supported properties
            return window.innerHeight ||
                   document.documentElement.clientHeight ||
                   document.body.clientHeight ||
                   window.screen.height;
        } catch (e) {
            utils.debugLog('Error getting viewport height:', e);
            return 0;
        }
    },
    getDomPath: (element) => {
        try {
            const path = [];
            while (element && element.nodeType === Node.ELEMENT_NODE) {
                let selector = element.nodeName.toLowerCase();
                if (element.id) {
                    selector += '#' + element.id;
                } else {
                    let sibling = element;
                    let siblingIndex = 1;
                    while (sibling = sibling.previousElementSibling) {
                        if (sibling.nodeName.toLowerCase() === selector) {
                            siblingIndex++;
                        }
                    }
                    if (siblingIndex > 1) {
                        selector += ':nth-of-type(' + siblingIndex + ')';
                    }
                }
                path.unshift(selector);
                element = element.parentNode;
            }
            return path.join(' > ');
        } catch (e) {
            utils.debugLog('Error getting DOM path:', e);
            return null;
        }
    },
    getLoadTime: () => {
        try {
            // Check if Performance API is supported
            if (!window.performance || !window.performance.timing) {
                return null;
            }

            const timing = window.performance.timing;
            const loadTime = timing.loadEventEnd - timing.navigationStart;

            // Return null if timing values are invalid
            if (loadTime < 0) {
                return null;
            }

            // Return load time in milliseconds
            return loadTime;
        } catch (e) {
            utils.debugLog('Error getting load time:', e);
            return null;
        }
    },
    getCharacterSet: () => {
        try {
            // Try to get character set from document characterSet
            if (document.characterSet) {
                return document.characterSet;
            }

            // Fallback to charset meta tag
            const charsetMeta = document.querySelector('meta[charset]');
            if (charsetMeta) {
                return charsetMeta.getAttribute('charset');
            }

            // Fallback to content-type meta tag
            const contentTypeMeta = document.querySelector('meta[http-equiv="Content-Type"]');
            if (contentTypeMeta) {
                const content = contentTypeMeta.getAttribute('content');
                const match = content.match(/charset=([^;]+)/i);
                if (match) {
                    return match[1];
                }
            }

            // Default to UTF-8 if nothing else found
            return 'UTF-8';
        } catch (e) {
            utils.debugLog('Error getting character set:', e);
            return 'UTF-8';
        }
    },

    getViewportWidth: () => {
        try {
            // Get viewport width using different browser-supported properties
            return window.innerWidth ||
                   document.documentElement.clientWidth ||
                   document.body.clientWidth ||
                   window.screen.width;
        } catch (e) {
            utils.debugLog('Error getting viewport width:', e);
            return 0;
        }
    },


    getSessionId: () => {
        const now = Date.now();
        let sessionId;
        let storedSessionId = w.localStorage.getItem('ts_monitor_session_id');
        let storedSessionStart = parseInt(w.localStorage.getItem('ts_monitor_session_start'), 10);

        if (!storedSessionId || isNaN(storedSessionStart) || (now - storedSessionStart > internalConfig.SESSION_DURATION)) {
            sessionId = utils.generateSessionId();
            w.localStorage.setItem('ts_monitor_session_id', sessionId);
            w.localStorage.setItem('ts_monitor_session_start', now.toString());

        } else {
            sessionId = storedSessionId;
        }
        let lastActivity = now;
        return sessionId;
    },

    getConnectionSpeed: () => {
        if (navigator.connection) {
            const connection = navigator.connection;
            return {
                effectiveType: connection.effectiveType,
                downlink: connection.downlink,
                rtt: connection.rtt,
                saveData: connection.saveData
            };
        }
        return null;
    },

    getBatteryStatus: async () => {
        // Try modern Battery API first
        if (navigator.getBattery) {
            try {
                const battery = await navigator.getBattery();
                return {
                    level: battery.level,
                    charging: battery.charging,
                    chargingTime: battery.chargingTime,
                    dischargingTime: battery.dischargingTime
                };
            } catch (e) {
                // Fall through to legacy methods
            }
        }

        // Try legacy battery API
        if (navigator.battery || navigator.webkitBattery || navigator.mozBattery) {
            const battery = navigator.battery || navigator.webkitBattery || navigator.mozBattery;
            return {
                level: battery.level,
                charging: battery.charging,
                chargingTime: battery.chargingTime,
                dischargingTime: battery.dischargingTime
            };
        }

        // Try devicemotion event as fallback for basic power info
        try {
            return new Promise(resolve => {
                const handleMotion = (event) => {
                    window.removeEventListener('devicemotion', handleMotion);
                    // Rough estimate based on motion sensor availability
                    resolve({
                        level: null,
                        charging: null,
                        chargingTime: null,
                        dischargingTime: null,
                        hasPower: !!event.acceleration
                    });
                };
                window.addEventListener('devicemotion', handleMotion, { once: true });
                // Timeout after 1s
                setTimeout(() => {
                    window.removeEventListener('devicemotion', handleMotion);
                    resolve(null);
                }, 1000);
            });
        } catch (e) {
            return null;
        }
    },

    getMemoryUsage: () => {
        if (performance.memory) {
            return {
                jsHeapSizeLimit: performance.memory.jsHeapSizeLimit,
                totalJSHeapSize: performance.memory.totalJSHeapSize,
                usedJSHeapSize: performance.memory.usedJSHeapSize
            };
        }
        return null;
    },

    getNetworkLatency: () => {
        // Use Resource Timing API to get network latency from recent requests
        const resources = performance.getEntriesByType('resource');
        if (resources.length > 0) {
            // Get average TTFB (Time To First Byte) from last 5 requests
            const recentRequests = resources.slice(-5);
            const avgTTFB = recentRequests.reduce((sum, entry) => {
                return sum + (entry.responseStart - entry.requestStart);
            }, 0) / recentRequests.length;

            return Math.round(avgTTFB);
        }

        // Fallback to navigator.connection if available
        if (navigator.connection) {
            return navigator.connection.rtt;
        }

        return null;
    },

    getScrollDirection: (() => {
        let lastScrollTop = window.pageYOffset || document.documentElement.scrollTop;
        let direction = null;

        return () => {
            const currentScrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (currentScrollTop > lastScrollTop) {
                direction = 'down';
            } else if (currentScrollTop < lastScrollTop) {
                direction = 'up';
            }

            lastScrollTop = currentScrollTop;
            return direction;
        };
    })(),

    getScrollSpeed: (() => {
        let lastScrollPosition = window.pageYOffset || document.documentElement.scrollTop;
        let lastScrollTime = Date.now();

        return () => {
            const currentPosition = window.pageYOffset || document.documentElement.scrollTop;
            const currentTime = Date.now();

            const distance = Math.abs(currentPosition - lastScrollPosition);
            const timeDiff = currentTime - lastScrollTime;

            // Calculate speed in pixels per second
            const speed = timeDiff > 0 ? (distance / timeDiff) * 1000 : 0;

            // Update values for next calculation
            lastScrollPosition = currentPosition;
            lastScrollTime = currentTime;

            return {
                speed: Math.round(speed), // pixels per second
                distance: distance, // pixels moved
                time: timeDiff // milliseconds
            };
        };
    })(),

    getDeviceType: () => {
        // Check if navigator.userAgentData is available (modern browsers)
        if (navigator.userAgentData) {
            if (navigator.userAgentData.mobile) {
                return 'mobile';
            }
        }

        // Fallback to user agent string parsing
        const userAgent = navigator.userAgent.toLowerCase();

        // Check for tablets first since they may also match mobile patterns
        if (/(tablet|ipad|playbook|silk)|(android(?!.*mobile))/i.test(userAgent)) {
            return 'tablet';
        }

        // Check for mobile devices
        if (/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(userAgent)) {
            return 'mobile';
        }

        // Check for gaming consoles
        if (/(xbox|playstation|nintendo)/i.test(userAgent)) {
            return 'gaming';
        }

        // Check for smart TVs
        if (/smart-tv|smarttv|tv|webos|netcast|viera|bravia|samsung.*smart.*tv/i.test(userAgent)) {
            return 'tv';
        }

        // Default to desktop
        return 'desktop';
    },
    isScreenLocked: async () => {
        if ('wakeLock' in navigator) {
            try {
                const wakeLockSentinel = await navigator.wakeLock.request('screen');
                const isLocked = wakeLockSentinel.released === false;
                wakeLockSentinel.release();
                return isLocked;
            } catch (e) {
                return false;
            }
        }
        return null;
    },

    getScrollDepth: () => {
        const documentHeight = Math.max(
            document.documentElement.scrollHeight,
            document.documentElement.offsetHeight,
            document.documentElement.clientHeight
        );
        const windowHeight = window.innerHeight;
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        // Calculate scroll percentage
        const scrolled = (scrollTop + windowHeight) / documentHeight * 100;

        return Math.min(Math.round(scrolled), 100);
    },

    getCurrentFPS: (() => {
        let frameCount = 0;
        let lastTime = performance.now();
        let fps = 0;

        const countFrame = () => {
            frameCount++;
            const currentTime = performance.now();
            const elapsedTime = currentTime - lastTime;

            if (elapsedTime >= 1000) {
                fps = Math.round((frameCount * 1000) / elapsedTime);
                frameCount = 0;
                lastTime = currentTime;
            }

            requestAnimationFrame(countFrame);
            return fps;
        };

        requestAnimationFrame(countFrame);
        return () => fps;
    })(),

    getPageLoadMetrics: () => {
        const timing = performance.timing;
        const paint = performance.getEntriesByType('paint');
        const nav = performance.getEntriesByType('navigation')[0];

        return {
            // Basic timing metrics
            dnsLookup: timing.domainLookupEnd - timing.domainLookupStart,
            tcpConnection: timing.connectEnd - timing.connectStart,
            serverResponse: timing.responseStart - timing.requestStart,
            domComplete: timing.domComplete - timing.domLoading,
            pageLoad: timing.loadEventEnd - timing.navigationStart,

            // Additional timing metrics
            ttfb: timing.responseStart - timing.navigationStart,
            domInteractive: timing.domInteractive - timing.navigationStart,
            domContentLoaded: timing.domContentLoadedEventEnd - timing.navigationStart,

            // Paint timing metrics
            firstPaint: paint.find(entry => entry.name === 'first-paint')?.startTime,
            firstContentfulPaint: paint.find(entry => entry.name === 'first-contentful-paint')?.startTime,
            fcp: performance.getEntriesByType('paint').find(entry => entry.name === 'first-contentful-paint')?.startTime,
            lcp: utils.getLargestContentfulPaint(),
            fid: utils.getFirstInputDelay(),
            cls: utils.getCumulativeLayoutShift(),

            // Resource timing
            resourceCount: performance.getEntriesByType('resource').length,
            resourceDuration: nav?.transferSize,
            encodedBodySize: nav?.encodedBodySize,
            decodedBodySize: nav?.decodedBodySize,

            // Navigation type
            navigationType: nav?.type,
            redirectCount: nav?.redirectCount
        };
    },
    detectSuspiciousNavigation: () => {
        const nav = performance.getEntriesByType('navigation')[0];
        const timing = performance.timing;
        const suspicious = {
            isSuspicious: false,
            reasons: []
        };

        // Check for unusually fast page loads (potential caching/prefetching)
        if (timing.loadEventEnd - timing.navigationStart < 100) {
            suspicious.isSuspicious = true;
            suspicious.reasons.push('unusually_fast_load');
        }

        // Check for unusual navigation types
        if (nav?.type && !['navigate', 'reload', 'back_forward'].includes(nav.type)) {
            suspicious.isSuspicious = true;
            suspicious.reasons.push('unusual_navigation_type');
        }

        // Check for high number of redirects
        if (nav?.redirectCount > 3) {
            suspicious.isSuspicious = true;
            suspicious.reasons.push('excessive_redirects');
        }

        // Check for unusual timing patterns
        if (timing.domComplete < timing.domLoading) {
            suspicious.isSuspicious = true;
            suspicious.reasons.push('invalid_dom_timing');
        }

        return suspicious;
    },

    getLargestContentfulPaint() {
        return new Promise(resolve => {
            new PerformanceObserver((entryList) => {
                const entries = entryList.getEntries();
                const lastEntry = entries[entries.length - 1];
                resolve(lastEntry ? lastEntry.startTime : undefined);
            }).observe({ type: 'largest-contentful-paint', buffered: true });
        });
    },

    getFirstInputDelay() {
        return new Promise(resolve => {
            new PerformanceObserver((entryList) => {
                const firstInput = entryList.getEntries()[0];
                resolve(firstInput ? firstInput.processingStart - firstInput.startTime : undefined);
            }).observe({ type: 'first-input', buffered: true });
        });
    },

    getCumulativeLayoutShift() {
        return new Promise(resolve => {
            let cumulativeLayoutShiftScore = 0;
            new PerformanceObserver((entryList) => {
                for (const entry of entryList.getEntries()) {
                    if (!entry.hadRecentInput) {
                        cumulativeLayoutShiftScore += entry.value;
                    }
                }
                resolve(cumulativeLayoutShiftScore);
            }).observe({ type: 'layout-shift', buffered: true });
        });
    },

    getDeviceMemory: () => {
        return navigator.deviceMemory || null;
    },

    getCPUCores: () => {
        return navigator.hardwareConcurrency || null;
    },

    getPreferredColorScheme: () => {
        if (window.matchMedia) {
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                return 'dark';
            }
            if (window.matchMedia('(prefers-color-scheme: light)').matches) {
                return 'light';
            }
        }
        return 'no-preference';
    },

    getReducedMotionPreference: () => {
        if (window.matchMedia) {
            return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        }
        return null;
    },

    getResourceTiming: () => {
        try {
            const resources = performance.getEntriesByType('resource');
            return resources.map(resource => ({
                name: resource.name,
                initiatorType: resource.initiatorType,
                duration: resource.duration,
                transferSize: resource.transferSize,
                decodedBodySize: resource.decodedBodySize,
                encodedBodySize: resource.encodedBodySize,
                startTime: resource.startTime,
                responseEnd: resource.responseEnd,
                fetchStart: resource.fetchStart,
                connectStart: resource.connectStart,
                connectEnd: resource.connectEnd,
                requestStart: resource.requestStart,
                responseStart: resource.responseStart,
                secureConnectionStart: resource.secureConnectionStart,
                redirectStart: resource.redirectStart,
                redirectEnd: resource.redirectEnd,
                domainLookupStart: resource.domainLookupStart,
                domainLookupEnd: resource.domainLookupEnd
            }));
        } catch (e) {
            utils.debugLog('Error getting resource timing:', e);
            return [];
        }
    },

    getCurrentFrameRate: () => {
        return new Promise(resolve => {
            requestAnimationFrame(t1 => {
                requestAnimationFrame(t2 => {
                    resolve(1000 / (t2 - t1));
                });
            });
        });
    },

    getBatteryLevel: async () => {
        if ('getBattery' in navigator) {
            const battery = await navigator.getBattery();
            return battery.level;
        }
        return null;
    },

    getNavigationType: () => {
        try {
            // Check if Navigation Timing API v2 is available
            const navigationEntries = performance.getEntriesByType('navigation');
            if (navigationEntries.length > 0) {
                return navigationEntries[0].type; // 'navigate', 'reload', 'back_forward', 'prerender'
            }

            // Fallback to Navigation Timing API v1
            if (performance.navigation) {
                const navTypes = {
                    0: 'navigate',
                    1: 'reload',
                    2: 'back_forward',
                    255: 'prerender'
                };
                return navTypes[performance.navigation.type] || 'unknown';
            }

            return 'unknown';
        } catch (e) {
            utils.debugLog('Error getting navigation type:', e);
            return 'unknown';
        }
    },
    getPageTitle: () => {
        return document.title || '';
    },

    getPageDescription: () => {
        const metaDesc = document.querySelector('meta[name="description"]');
        return metaDesc ? metaDesc.getAttribute('content') : '';
    },

    getPageKeywords: () => {
        const metaKeywords = document.querySelector('meta[name="keywords"]');
        return metaKeywords ? metaKeywords.getAttribute('content') : '';
    },

    getCanonicalUrl: () => {
        const canonical = document.querySelector('link[rel="canonical"]');
        return canonical ? canonical.href : '';
    },

    getOgMetadata: () => {
        const ogTags = document.querySelectorAll('meta[property^="og:"]');
        const metadata = {};
        ogTags.forEach(tag => {
            const property = tag.getAttribute('property').replace('og:', '');
            metadata[property] = tag.getAttribute('content');
        });
        return metadata;
    },

    getTwitterMetadata: () => {
        const twitterTags = document.querySelectorAll('meta[name^="twitter:"]');
        const metadata = {};
        twitterTags.forEach(tag => {
            const name = tag.getAttribute('name').replace('twitter:', '');
            metadata[name] = tag.getAttribute('content');
        });
        return metadata;
    },

    getStructuredData: () => {
        const ldJsonScripts = document.querySelectorAll('script[type="application/ld+json"]');
        const structuredData = [];
        ldJsonScripts.forEach(script => {
            try {
                structuredData.push(JSON.parse(script.textContent));
            } catch (e) {
                utils.debugLog('Error parsing structured data:', e);
            }
        });
        return structuredData;
    },

    getPageLoadTime: () => {
        if (window.performance && window.performance.timing) {
            const timing = window.performance.timing;
            const loadTime = timing.loadEventEnd - timing.navigationStart;
            return loadTime > 0 ? loadTime : null;
        }
        return null;
    },
    getHreflangTags: () => {
        const hreflangTags = document.querySelectorAll('link[rel="alternate"][hreflang]');
        const hreflangs = {};
        hreflangTags.forEach(tag => {
            const lang = tag.getAttribute('hreflang');
            hreflangs[lang] = tag.href;
        });
        return hreflangs;
    },

    getRobotsMeta: () => {
        const robotsMeta = document.querySelector('meta[name="robots"]');
        return robotsMeta ? robotsMeta.getAttribute('content') : null;
    },

    getLastModified: () => {
        const lastModified = document.lastModified;
        return lastModified ? new Date(lastModified).toISOString() : null;
    },
    getPageDepth: () => {
        // Get current page depth from session storage
        let pageDepth = parseInt(sessionStorage.getItem('pageDepth')) || 0;

        // Increment page depth for this pageview
        pageDepth++;

        // Store updated page depth
        sessionStorage.setItem('pageDepth', pageDepth);

        return pageDepth;
    },

    calculateEngagementScore: (duration, interactions, scrollDepth) => {
        // Implement a more sophisticated engagement scoring algorithm
        const timeWeight = Math.min(duration / 60000, 1); // Cap at 1 minute
        const interactionWeight = Math.min(interactions / 10, 1); // Cap at 10 interactions
        const scrollWeight = Math.min(scrollDepth / 100, 1); // Percentage scrolled

        return (timeWeight + interactionWeight + scrollWeight) / 3;
    },
    getSessionDuration: () => {
        const startTime = sessionStorage.getItem('sessionStartTime');
        if (!startTime) {
            return 0;
        }
        return Date.now() - parseInt(startTime);
    },

    // Page data
    getPageUrl: () => {
        // Get the full URL
        const url = w.location.href;

        // Remove any sensitive parameters
        try {
            const urlObj = new URL(url);
            const params = new URLSearchParams(urlObj.search);

            // List of sensitive parameters to remove
            const sensitiveParams = ['token', 'key', 'password', 'secret', 'auth'];

            // Remove sensitive parameters
            sensitiveParams.forEach(param => {
                if (params.has(param)) {
                    params.delete(param);
                }
            });

            // Reconstruct URL without sensitive params
            urlObj.search = params.toString();

            // Limit URL length to prevent oversized requests
            return urlObj.toString().substring(0, 2000);

        } catch (e) {
            // If URL parsing fails, return original URL
            return url.substring(0, 2000);
        }
    },
    getPageTitle: () => {
        // Try to get the most specific title first
        const metaTitle = document.querySelector('meta[property="og:title"]')?.content ||
                         document.querySelector('meta[name="twitter:title"]')?.content;

        // If meta title exists, combine with document title
        if (metaTitle) {
            return metaTitle !== document.title ? `${metaTitle} | ${document.title}` : document.title;
        }

        // Get first h1 as fallback if different from document title
        const h1Title = document.querySelector('h1')?.textContent?.trim();
        if (h1Title && h1Title !== document.title) {
            return `${h1Title} | ${document.title}`;
        }

        // Default to document title
        return document.title || 'Untitled Page';
    },

    getPagePath: () => {
        try {
            // Get the current URL and create URL object
            const url = new URL(window.location.href);

            // Get pathname and ensure it starts with /
            let path = url.pathname;
            if (!path.startsWith('/')) {
                path = '/' + path;
            }

            // Remove trailing slash except for root path
            if (path !== '/' && path.endsWith('/')) {
                path = path.slice(0, -1);
            }

            // Normalize path by decoding URI components and removing duplicate slashes
            path = decodeURIComponent(path)
                .replace(/\/+/g, '/')
                .toLowerCase();

            // Limit path length to prevent oversized requests
            return path.substring(0, 255);

        } catch (e) {
            // Return root path if URL parsing fails
            return '/';
        }
    },

    getPageQuery: () => {
        try {
            // Get the current URL and create URL object
            const url = new URL(window.location.href);
            const params = new URLSearchParams(url.search);

            // Remove sensitive parameters
            const sensitiveParams = ['token', 'key', 'password', 'secret', 'auth', 'api_key', 'apikey'];
            sensitiveParams.forEach(param => {
                if (params.has(param)) {
                    params.delete(param);
                }
            });

            // Convert to string and limit length
            const queryString = params.toString();
            if (!queryString) {
                return '';
            }

            return '?' + queryString.substring(0, 255);

        } catch (e) {
            return '';
        }
    },

    getPageHash: () => {
        try {
            // Get the current URL hash
            const hash = window.location.hash;

            // Return empty string if no hash
            if (!hash) {
                return '';
            }

            // Normalize hash by decoding URI components and removing duplicate hashes
            const normalizedHash = decodeURIComponent(hash)
                .replace(/#+/g, '#')
                .toLowerCase();

            // Limit hash length and return
            return normalizedHash.substring(0, 255);

        } catch (e) {
            return '';
        }
    },

    getUrlPath: () => {
        // Get the current pathname
        const pathname = w.location.pathname;

        // If we're at the root path ('/'), return the full URL without query params
        if (pathname === '/') {
            const url = new URL(w.location.href);
            return url.origin + url.pathname;
        }

        // Otherwise just return the pathname
        return pathname;
    },


    getReferrer: () => {
        try {
            // Get document referrer
            const referrer = document.referrer;

            // Return null if no referrer
            if (!referrer) {
                return null;
            }

            // Create URL object from referrer
            const url = new URL(referrer);

            // Remove sensitive parameters from query string
            const params = new URLSearchParams(url.search);
            const sensitiveParams = ['token', 'key', 'password', 'secret', 'auth', 'api_key', 'apikey'];
            sensitiveParams.forEach(param => {
                if (params.has(param)) {
                    params.delete(param);
                }
            });

            // Reconstruct URL without sensitive params
            url.search = params.toString();

            // Return normalized referrer URL
            return url.toString().substring(0, 255);

        } catch (e) {
            utils.debugLog('Error getting referrer:', e);
            return null;
        }
    },

    // Browser data
    getBrowserVersion: () => {
        const ua = navigator.userAgent;
        const match = ua.match(/(Opera|OPR|Edge|Chrome|Safari|Firefox|MSIE|Trident)[\s\/](\d+(\.\d+)?)/i) || ua.match(/(Version)[\s\/](\d+(\.\d+)?)/i);
        return match ? match[2] : 'Unknown';
    },

    getRedirectCount: () => {
        try {
            // Use the performance API to get navigation timing data
            const navigation = performance.getEntriesByType('navigation')[0];
            if (navigation && 'redirectCount' in navigation) {
                return navigation.redirectCount;
            }
            // Fallback for older browsers
            return window.performance && window.performance.navigation ?
                window.performance.navigation.redirectCount : 0;
        } catch (e) {
            utils.debugLog('Error getting redirect count:', e);
            return 0;
        }
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


    getBrowserLanguage: () => {
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
    analyzeInteractionPatterns: (interactions) => {
        const patterns = {
            rapidClicks: 0,
            erraticMovements: 0,
            multipleSubmits: 0,
            rageclicks: 0
        };

        // Analyze time between clicks
        if (interactions.clicks) {
            const clickTimes = interactions.clicks.map(click => click.timestamp);
            for (let i = 1; i < clickTimes.length; i++) {
                const timeDiff = clickTimes[i] - clickTimes[i-1];
                if (timeDiff < 200) { // Clicks less than 200ms apart
                    patterns.rapidClicks++;
                }
            }
        }

        // Analyze mouse movements
        if (interactions.movements) {
            let lastX = 0, lastY = 0;
            interactions.movements.forEach(move => {
                const dx = move.x - lastX;
                const dy = move.y - lastY;
                const speed = Math.sqrt(dx*dx + dy*dy);

                if (speed > 100) { // Fast erratic movements
                    patterns.erraticMovements++;
                }

                lastX = move.x;
                lastY = move.y;
            });
        }

        // Analyze form submissions
        if (interactions.submissions) {
            const submissionTimes = interactions.submissions.map(sub => sub.timestamp);
            for (let i = 1; i < submissionTimes.length; i++) {
                if (submissionTimes[i] - submissionTimes[i-1] < 1000) {
                    patterns.multipleSubmits++;
                }
            }
        }

        // Detect rage clicks (multiple clicks in same area)
        if (interactions.clicks) {
            interactions.clicks.forEach((click, i) => {
                if (i === 0) return;
                const prev = interactions.clicks[i-1];
                const distance = Math.sqrt(
                    Math.pow(click.x - prev.x, 2) +
                    Math.pow(click.y - prev.y, 2)
                );
                if (distance < 10 && click.timestamp - prev.timestamp < 500) {
                    patterns.rageclicks++;
                }
            });
        }

        return patterns;
    },
    debugLog: (...args) => {
        if (TSMonitorConfig.debug) {
            console.log('[TSMonitor Analytics Debug]', ...args);
        }
        if (TSMonitorConfig.browserDebug) {
            const debugElement = document.getElementById('ts-monitor-debug-log');
            if (debugElement) {
                const logEntry = document.createElement('div');
                let logText = `[${new Date().toISOString()}] `;
                args.forEach((arg) => {
                    if (typeof arg === 'object') {
                        try {
                            // Handle circular references by using a custom replacer
                            const seen = new WeakSet();
                            logText += JSON.stringify(arg, (key, value) => {
                                if (typeof value === 'object' && value !== null) {
                                    if (seen.has(value)) {
                                        return '[Circular Reference]';
                                    }
                                    seen.add(value);
                                }
                                return value;
                            }, 2);
                        } catch (e) {
                            logText += '[Object with circular reference]';
                        }
                    } else {
                        logText += String(arg);
                    }
                    logText += ' ';
                });
                logEntry.textContent = logText;
                debugElement.appendChild(logEntry);
            }
        }
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
    },
    getElementPath: (element) => {
        if (!element || !element.tagName) return '';

        const path = [];
        let currentElement = element;

        while (currentElement && currentElement.tagName) {
            let elementIdentifier = currentElement.tagName.toLowerCase();

            // Add id if present
            if (currentElement.id) {
                elementIdentifier += `#${currentElement.id}`;
            } else {
                // Add classes if no id
                const classes = Array.from(currentElement.classList).join('.');
                if (classes) {
                    elementIdentifier += `.${classes}`;
                }

                // Add position among siblings if no unique identifier
                if (!currentElement.id && !classes) {
                    const siblings = Array.from(currentElement.parentNode?.children || []);
                    const index = siblings.indexOf(currentElement) + 1;
                    if (siblings.length > 1) {
                        elementIdentifier += `:nth-child(${index})`;
                    }
                }
            }

            path.unshift(elementIdentifier);
            currentElement = currentElement.parentElement;
        }

        return path.join(' > ');
    },
    updateEngagementScore(value) {
        this.engagementScore += value;
        console.log('Engagement score updated:', this.engagementScore);
    },

    isElementVisible: (element) => {
        if (!element || typeof element.getBoundingClientRect !== 'function') return false;

        try {
            // Get element's bounding rect
            const rect = element.getBoundingClientRect();

            // Check if element has size
            if (rect.width === 0 || rect.height === 0) return false;

            // Check if element is in viewport
            if (
                rect.bottom < 0 ||
                rect.right < 0 ||
                rect.top > (window.innerHeight || document.documentElement.clientHeight) ||
                rect.left > (window.innerWidth || document.documentElement.clientWidth)
            ) return false;

            // Check element's computed style
            const style = window.getComputedStyle(element);
            if (style.display === 'none' || style.visibility === 'hidden' || style.opacity === '0') return false;

            // Check if any parent element hides this element
            let parent = element.parentElement;
            while (parent) {
                const parentStyle = window.getComputedStyle(parent);
                if (
                    parentStyle.display === 'none' ||
                    parentStyle.visibility === 'hidden' ||
                    parentStyle.opacity === '0'
                ) return false;
                parent = parent.parentElement;
            }

            return true;
        } catch (e) {
            return false;
        }
    },
    isElementInteractive: (element) => {
        if (!element || !(element instanceof Element)) return false;

        try {
            // Check if element is disabled
            if (element.disabled) return false;

            // Check if element is focusable/clickable
            const interactiveTags = ['A', 'BUTTON', 'INPUT', 'SELECT', 'TEXTAREA'];
            if (interactiveTags.includes(element.tagName)) return true;

            // Check for interactive ARIA roles
            const interactiveRoles = ['button', 'link', 'checkbox', 'radio', 'textbox', 'combobox', 'listbox', 'menuitem'];
            const role = element.getAttribute('role');
            if (role && interactiveRoles.includes(role)) return true;

            // Check if element has click handlers
            const elementStyle = window.getComputedStyle(element);
            if (elementStyle.cursor === 'pointer') return true;

            // Check for tabindex
            const tabIndex = element.getAttribute('tabindex');
            if (tabIndex !== null && tabIndex >= 0) return true;

            // Check if element has event listeners
            const hasEventListeners = element.onclick ||
                                    element.onkeydown ||
                                    element.onkeyup ||
                                    element.onkeypress;
            if (hasEventListeners) return true;

            return false;
        } catch (e) {
            return false;
        }
    },
    getElementDepth: (element) => {
        let depth = 0;
        let currentElement = element;

        while (currentElement && currentElement !== document.documentElement) {
            depth++;
            currentElement = currentElement.parentElement;
        }

        return depth;
    },
};

