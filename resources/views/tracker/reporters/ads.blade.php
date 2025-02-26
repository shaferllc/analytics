trackAdClicks() {
    // Add redirect detection before the existing click handler
    this.setupRedirectDetection();

    document.addEventListener('click', (event) => {
        const adElement = event.target.closest('[data-ad], [class*="ad-"], [id*="ad-"], [class*="advertisement"], .sponsored-content, [class*="google-ad"], [id*="google-ad"], [class*="fb-ad"], [id*="fb-ad"], [class*="facebook-ad"], [id*="facebook-ad"], [data-ad-slot], [data-ad-client], [data-ad-layout], [data-ad-format], .adsbygoogle, .fb-page-plugin, [class*="adsense"], [id*="adsense"], [class*="banner-ad"], [id*="banner-ad"], [class*="sponsored"], [id*="sponsored"], [class*="promotion"], [id*="promotion"], [class*="advertorial"], [id*="advertorial"], [class*="paid-content"], [id*="paid-content"], [class*="partner-content"], [id*="partner-content"], [class*="native-ad"], [id*="native-ad"], [class*="promoted"], [id*="promoted"], [class*="taboola"], [id*="taboola"], [class*="outbrain"], [id*="outbrain"], [class*="medianet"], [id*="medianet"]');

        if (adElement) {
            // Add security checks
            const securityAnalysis = this.analyzeAdSecurity(adElement);

            const adData = {
                name: 'ad_click',
                type: 'event',
                value: {
                    // Basic ad info
                    adId: adElement.id || null,
                    adClass: adElement.className || null,
                    adType: adElement.dataset.adType || 'unknown',
                    adNetwork: this.detectAdNetwork(adElement),
                    adFormat: adElement.dataset.adFormat || null,
                    adSize: {
                        width: adElement.offsetWidth,
                        height: adElement.offsetHeight
                    },

                    // Additional ad identifiers
                    adIdentifiers: {
                        dataAttributes: this.getDataAttributes(adElement),
                        scriptSources: this.getAdScriptSources(adElement),
                        iframeSources: this.getAdIframeSources(adElement),
                        networkCookies: this.getAdNetworkCookies(),
                        globalVariables: this.getAdGlobalVariables(),
                        adTags: this.getAdTags(adElement)
                    },

                    // Click data
                    clickPosition: {
                        x: event.clientX,
                        y: event.clientY,
                        relativeX: event.offsetX,
                        relativeY: event.offsetY
                    },

                    // Ad location
                    adLocation: {
                        href: adElement.href || null,
                        pathname: window.location.pathname,
                        timestamp: Date.now(),
                        url: window.location.href,
                        referrer: document.referrer
                    },

                    // Viewport & positioning
                    viewportData: {
                        scrollY: window.scrollY,
                        scrollX: window.scrollX,
                        viewportHeight: window.innerHeight,
                        viewportWidth: window.innerWidth,
                        documentHeight: document.documentElement.scrollHeight,
                        documentWidth: document.documentElement.scrollWidth,
                        adVisiblePercentage: this.calculateVisiblePercentage(adElement)
                    },

                    // Ad content
                    content: {
                        text: adElement.textContent?.trim() || null,
                        imageUrl: this.getAdImageUrl(adElement),
                        altText: this.getAdImageAlt(adElement),
                        targetUrl: adElement.href || adElement.dataset.href || null
                    },

                    // Additional metadata
                    metadata: {
                        adSlot: adElement.dataset.adSlot || null,
                        adClient: adElement.dataset.adClient || null,
                        adLayout: adElement.dataset.adLayout || null,
                        campaign: adElement.dataset.campaign || null,
                        creativeId: adElement.dataset.creativeId || null,
                        placement: adElement.dataset.placement || null,
                        timeOnPage: (Date.now() - window.performance.timing.navigationStart) / 1000
                    },

                    // Add security analysis data
                    security: securityAnalysis,
                }
            };

            // If suspicious activity detected, optionally prevent the click
            if (securityAnalysis.threatLevel === 'high') {
                event.preventDefault();
                event.stopPropagation();
                console.warn('Potentially malicious ad blocked:', securityAnalysis);
            }

            this.queueRequest(adData);
            utils.debbugInfo('Ad click tracked:', adData.value);
        }
    });

    // Track ad impressions on scroll with enhanced data
    const observeAds = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const adElement = entry.target;

                this.queueRequest({
                    name: 'ad_impression',
                    type: 'event',
                    value: {
                        // Basic ad info
                        adId: adElement.id || null,
                        adClass: adElement.className || null,
                        adType: adElement.dataset.adType || 'unknown',
                        adNetwork: this.detectAdNetwork(adElement),
                        adFormat: adElement.dataset.adFormat || null,

                        // Additional ad identifiers
                        adIdentifiers: {
                            dataAttributes: this.getDataAttributes(adElement),
                            scriptSources: this.getAdScriptSources(adElement),
                            iframeSources: this.getAdIframeSources(adElement),
                            networkCookies: this.getAdNetworkCookies(),
                            globalVariables: this.getAdGlobalVariables(),
                            adTags: this.getAdTags(adElement)
                        },

                        // Timing data
                        visibleTime: Date.now(),
                        timeToVisible: (Date.now() - window.performance.timing.navigationStart) / 1000,

                        // Viewport & positioning
                        viewportPosition: entry.boundingClientRect.toJSON(),
                        intersectionRatio: entry.intersectionRatio,
                        viewportData: {
                            scrollY: window.scrollY,
                            scrollX: window.scrollX,
                            viewportHeight: window.innerHeight,
                            viewportWidth: window.innerWidth
                        },

                        // Ad dimensions
                        dimensions: {
                            width: adElement.offsetWidth,
                            height: adElement.offsetHeight,
                            naturalWidth: this.getAdImageNaturalSize(adElement)?.width || null,
                            naturalHeight: this.getAdImageNaturalSize(adElement)?.height || null
                        },

                        // Performance metrics
                        performance: {
                            loadTime: this.getAdLoadTime(adElement),
                            renderTime: this.getAdRenderTime(adElement),
                            resourceTiming: this.getResourceTiming(adElement)
                        }
                    }
                });

                observeAds.unobserve(adElement); // Only track first impression
            }
        });
    }, {
        threshold: [0, 0.25, 0.5, 0.75, 1], // Track multiple visibility thresholds
        rootMargin: '50px' // Start tracking slightly before ad comes into view
    });

    // Start observing all ad elements
    document.querySelectorAll('[data-ad], [class*="ad-"], [id*="ad-"], [class*="advertisement"], .sponsored-content, [class*="google-ad"], [id*="google-ad"], [class*="fb-ad"], [id*="fb-ad"], [class*="facebook-ad"], [id*="facebook-ad"]')
        .forEach(ad => observeAds.observe(ad));
}

// New helper methods to get additional ad data
getDataAttributes(element) {
    const dataAttrs = {};
    for (const attr of element.attributes) {
        if (attr.name.startsWith('data-')) {
            dataAttrs[attr.name] = attr.value;
        }
    }
    return dataAttrs;
}

getAdScriptSources(element) {
    const scripts = Array.from(element.getElementsByTagName('script'));
    return scripts.map(script => ({
        src: script.src,
        type: script.type,
        async: script.async,
        defer: script.defer,
        content: script.innerHTML.substring(0, 200) // First 200 chars of inline scripts
    }));
}

getAdIframeSources(element) {
    const iframes = Array.from(element.getElementsByTagName('iframe'));
    return iframes.map(iframe => {
        const iframeData = {
            src: iframe.src,
            name: iframe.name,
            id: iframe.id,
            width: iframe.width,
            height: iframe.height,
            loading: iframe.loading,
            sandbox: iframe.sandbox.value || null,
            allow: iframe.allow || null,
            referrerPolicy: iframe.referrerPolicy || null,
            contentData: this.getIframeContentData(iframe)
        };

        // Add computed styles
        const styles = window.getComputedStyle(iframe);
        iframeData.styles = {
            position: styles.position,
            display: styles.display,
            visibility: styles.visibility,
            zIndex: styles.zIndex,
            opacity: styles.opacity
        };

        // Add positioning data
        const rect = iframe.getBoundingClientRect();
        iframeData.position = {
            top: rect.top,
            left: rect.left,
            bottom: rect.bottom,
            right: rect.right,
            inViewport: this.isElementInViewport(iframe)
        };

        return iframeData;
    });
}

getIframeContentData(iframe) {
    try {
        // Only attempt to access same-origin iframes
        if (!this.isSameOrigin(iframe.src)) {
            return {
                accessible: false,
                reason: 'cross-origin'
            };
        }

        const iframeDoc = iframe.contentDocument || iframe.contentWindow?.document;
        if (!iframeDoc) {
            return {
                accessible: false,
                reason: 'no-document'
            };
        }

        return {
            accessible: true,
            title: iframeDoc.title,
            meta: this.getIframeMetaTags(iframeDoc),
            scripts: this.getIframeScripts(iframeDoc),
            links: this.getIframeLinks(iframeDoc),
            adElements: this.getIframeAdElements(iframeDoc),
            dimensions: {
                scrollWidth: iframeDoc.documentElement.scrollWidth,
                scrollHeight: iframeDoc.documentElement.scrollHeight
            }
        };
    } catch (error) {
        return {
            accessible: false,
            reason: 'security-error',
            error: error.message
        };
    }
}

// Helper methods for iframe content analysis
isSameOrigin(url) {
    try {
        const currentOrigin = window.location.origin;
        const iframeOrigin = new URL(url).origin;
        return currentOrigin === iframeOrigin;
    } catch {
        return false;
    }
}

getIframeMetaTags(doc) {
    return Array.from(doc.getElementsByTagName('meta'))
        .map(meta => ({
            name: meta.getAttribute('name'),
            content: meta.getAttribute('content'),
            property: meta.getAttribute('property')
        }))
        .filter(meta => meta.name || meta.content || meta.property);
}

getIframeScripts(doc) {
    return Array.from(doc.getElementsByTagName('script'))
        .map(script => ({
            src: script.src,
            type: script.type,
            async: script.async,
            defer: script.defer,
            id: script.id,
            textLength: script.text?.length || 0
        }));
}

getIframeLinks(doc) {
    return Array.from(doc.getElementsByTagName('a'))
        .map(link => ({
            href: link.href,
            target: link.target,
            rel: link.rel,
            text: link.textContent?.trim()
        }));
}

getIframeAdElements(doc) {
    // Common ad-related selectors
    const adSelectors = [
        '[data-ad]',
        '[class*="ad-"]',
        '[id*="ad-"]',
        '[class*="advertisement"]',
        '.sponsored-content',
        '[class*="google-ad"]',
        '[class*="adsense"]',
        '[class*="banner-ad"]'
    ].join(',');

    return Array.from(doc.querySelectorAll(adSelectors))
        .map(element => ({
            tagName: element.tagName,
            id: element.id,
            classes: Array.from(element.classList),
            dataAttributes: this.getDataAttributes(element),
            dimensions: {
                width: element.offsetWidth,
                height: element.offsetHeight
            }
        }));
}

isElementInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

getAdNetworkCookies() {
    const adCookies = {};
    document.cookie.split(';').forEach(cookie => {
        const [name, value] = cookie.split('=').map(c => c.trim());
        if (name.toLowerCase().includes('ad') ||
            name.toLowerCase().includes('sponsor') ||
            name.toLowerCase().includes('campaign')) {
            adCookies[name] = value;
        }
    });
    return adCookies;
}

getAdGlobalVariables() {
    const adVars = {};
    const patterns = [
        /^google/i, /^ad/i, /^gtag/i, /^ga/i, /^fbq/i,
        /^taboola/i, /^outbrain/i, /^criteo/i
    ];

    for (const key in window) {
        if (patterns.some(pattern => pattern.test(key))) {
            try {
                adVars[key] = typeof window[key];
            } catch (e) {
                adVars[key] = 'access-restricted';
            }
        }
    }
    return adVars;
}

getAdTags(element) {
    return {
        tagName: element.tagName.toLowerCase(),
        childTags: Array.from(element.children).map(child => child.tagName.toLowerCase()),
        parentTag: element.parentElement?.tagName.toLowerCase() || null,
        siblingTags: Array.from(element.parentElement?.children || [])
            .filter(sibling => sibling !== element)
            .map(sibling => sibling.tagName.toLowerCase())
    };
}

getResourceTiming(adElement) {
    // Get all resource timing entries
    const resources = performance.getEntriesByType('resource');

    // Try to find entries related to this ad element
    const adUrl = adElement.src || adElement.querySelector('img')?.src;
    const adEntries = resources.filter(entry => {
        const url = entry.name.toLowerCase();
        return (adUrl && url.includes(adUrl)) ||
               url.includes('ad') ||
               url.includes('sponsor') ||
               url.includes('promo') ||
               url.includes('banner');
    });

    if (adEntries.length === 0) {
        return null;
    }

    // Return timing data for the most relevant entry
    const entry = adEntries[0];
    return {
        startTime: entry.startTime,
        duration: entry.duration,
        initiatorType: entry.initiatorType,
        transferSize: entry.transferSize,
        encodedBodySize: entry.encodedBodySize,
        decodedBodySize: entry.decodedBodySize,
        responseEnd: entry.responseEnd,
        domainLookupTime: entry.domainLookupEnd - entry.domainLookupStart,
        connectTime: entry.connectEnd - entry.connectStart,
        requestTime: entry.responseStart - entry.requestStart,
        responseTime: entry.responseEnd - entry.responseStart
    };
}

getAdRenderTime(adElement) {
    // Try to get paint timing entries
    const paintEntries = performance.getEntriesByType('paint');
    const firstPaint = paintEntries.find(entry => entry.name === 'first-paint');
    const firstContentfulPaint = paintEntries.find(entry => entry.name === 'first-contentful-paint');

    // Check if the ad element was part of first contentful paint
    if (firstContentfulPaint && this.elementContainsFirstContentfulPaint(adElement)) {
        return firstContentfulPaint.startTime;
    }

    // Check for element timing API if available
    if ('elementTiming' in adElement) {
        return adElement.elementTiming;
    }

    // Fallback: estimate based on DOM ready and load events
    const navigationEntry = performance.getEntriesByType('navigation')[0];
    const domContentLoaded = navigationEntry.domContentLoadedEventEnd;
    const loadComplete = navigationEntry.loadEventEnd;

    // Return the later of DOM content loaded or load complete
    return Math.max(domContentLoaded, loadComplete);
}

elementContainsFirstContentfulPaint(element) {
    // Get the first contentful paint entry
    const fcpEntry = performance.getEntriesByType('paint')
        .find(entry => entry.name === 'first-contentful-paint');

    if (!fcpEntry) {
        return false;
    }

    // Check if element was present at FCP time
    const elementRect = element.getBoundingClientRect();
    const isVisible = elementRect.width > 0 &&
                     elementRect.height > 0 &&
                     element.style.display !== 'none' &&
                     element.style.visibility !== 'hidden';

    // Check if element was loaded before FCP
    const elementResources = performance.getEntriesByType('resource')
        .filter(entry => entry.name === (element.src || element.querySelector('img')?.src));

    const wasLoadedBeforeFCP = elementResources.some(entry =>
        entry.responseEnd <= fcpEntry.startTime
    );

    return isVisible && wasLoadedBeforeFCP;
}

getAdLoadTime(adElement) {
    // Try to get performance timing for ad-specific resources
    const adUrl = adElement.src || adElement.querySelector('img')?.src;
    if (adUrl) {
        const entries = performance.getEntriesByName(adUrl);
        if (entries.length > 0) {
            return entries[0].duration;
        }
    }

    // Check for ad-specific markers in performance timeline
    const adEntries = performance.getEntriesByType('resource').filter(entry => {
        const url = entry.name.toLowerCase();
        return url.includes('ad') ||
               url.includes('sponsor') ||
               url.includes('promo') ||
               url.includes('banner');
    });

    if (adEntries.length > 0) {
        // Return the longest duration as worst-case load time
        return Math.max(...adEntries.map(entry => entry.duration));
    }

    // Fallback: return time since navigation start
    return performance.now();
}

getAdImageNaturalSize(adElement) {
    // Check for direct image ads
    if (adElement.tagName === 'IMG') {
        return {
            width: adElement.naturalWidth,
            height: adElement.naturalHeight
        };
    }

    // Look for images within the ad container
    const adImage = adElement.querySelector('img');
    if (adImage) {
        return {
            width: adImage.naturalWidth,
            height: adImage.naturalHeight
        };
    }

    // If no images found, return null
    return null;
}

detectAdNetwork(adElement) {
    // Check for common ad network identifiers in classes, IDs, and data attributes
    const elementString = `${adElement.className} ${adElement.id} ${Object.keys(adElement.dataset).join(' ')}`.toLowerCase();

    // Define patterns for major ad networks
    const networks = {
        'google': /(google|doubleclick|adsense|adx)/,
        'facebook': /(facebook|fb[-_]?ad)/,
        'amazon': /(amazon|amzn)/,
        'taboola': /taboola/,
        'outbrain': /outbrain/,
        'criteo': /criteo/,
        'medianet': /media\.net/,
        'adroll': /adroll/,
        'rubicon': /rubicon/,
        'openx': /openx/,
        'pubmatic': /pubmatic/,
        'appnexus': /appnexus/,
        'index': /index(exchange)?/,
        'sovrn': /sovrn/,
        'sharethrough': /sharethrough/,
        'triplelift': /triplelift/,
        'teads': /teads/,
        'verizon': /(verizon|oath|aol|yahoo)/,
        'magnite': /magnite/,
        'spotx': /spotx/,
        'yieldmo': /yieldmo/,
        'undertone': /undertone/,
        'connatix': /connatix/,
        'nativo': /nativo/,
        'adform': /adform/,
        'smartadserver': /smartadserver/,
        'smaato': /smaato/,
        'inmobi': /inmobi/,
        'unity': /unity(ads)?/,
        'admob': /admob/,
        'adcolony': /adcolony/,
        'mopub': /mopub/,
        'chartboost': /chartboost/,
        'ironsource': /ironsource/,
        'applovin': /applovin/,
        'fyber': /fyber/,
        'adthrive': /adthrive/,
        'mediavine': /mediavine/,
        'ezoic': /ezoic/,
        'adbutler': /adbutler/,
        'adsterra': /adsterra/,
        'propellerads': /propellerads/,
        'revcontent': /revcontent/,
        'mgid': /mgid/,
        'adcash': /adcash/,
        'exoclick': /exoclick/,
        'adnow': /adnow/,
        'bidvertiser': /bidvertiser/,
        'popads': /popads/,
        'adtech': /adtech/,
        'zedo': /zedo/,
        'adspirit': /adspirit/,
        'adglare': /adglare/,
        'adpushup': /adpushup/,
        'adnxs': /adnxs/,
        'adition': /adition/,
        'plista': /plista/
    };

    // Check for network matches
    for (const [network, pattern] of Object.entries(networks)) {
        if (pattern.test(elementString)) {
            return network;
        }
    }

    // Check for ad-specific src attributes in nested elements
    const adSources = adElement.querySelectorAll('img, iframe, script');
    for (const source of adSources) {
        const src = (source.src || '').toLowerCase();

        if (src.includes('googlesyndication') || src.includes('doubleclick')) return 'google';
        if (src.includes('facebook')) return 'facebook';
        if (src.includes('amazon-adsystem')) return 'amazon';
        if (src.includes('taboola')) return 'taboola';
        if (src.includes('outbrain')) return 'outbrain';
        if (src.includes('criteo')) return 'criteo';
        if (src.includes('media.net')) return 'medianet';
        if (src.includes('pubmatic')) return 'pubmatic';
        if (src.includes('appnexus')) return 'appnexus';
        if (src.includes('indexexchange')) return 'index';
        if (src.includes('sovrn')) return 'sovrn';
        if (src.includes('sharethrough')) return 'sharethrough';
        if (src.includes('triplelift')) return 'triplelift';
        if (src.includes('teads')) return 'teads';
        if (src.includes('verizonmedia') || src.includes('oath') || src.includes('aol') || src.includes('yahoo')) return 'verizon';
        if (src.includes('magnite')) return 'magnite';
        if (src.includes('spotx')) return 'spotx';
        if (src.includes('yieldmo')) return 'yieldmo';
        if (src.includes('undertone')) return 'undertone';
        if (src.includes('connatix')) return 'connatix';
        if (src.includes('nativo')) return 'nativo';
        if (src.includes('adform')) return 'adform';
        if (src.includes('smartadserver')) return 'smartadserver';
        if (src.includes('smaato')) return 'smaato';
        if (src.includes('inmobi')) return 'inmobi';
        if (src.includes('unity')) return 'unity';
        if (src.includes('admob')) return 'admob';
        if (src.includes('adcolony')) return 'adcolony';
        if (src.includes('mopub')) return 'mopub';
        if (src.includes('chartboost')) return 'chartboost';
        if (src.includes('ironsource')) return 'ironsource';
        if (src.includes('applovin')) return 'applovin';
        if (src.includes('fyber')) return 'fyber';
        if (src.includes('adthrive')) return 'adthrive';
        if (src.includes('mediavine')) return 'mediavine';
        if (src.includes('ezoic')) return 'ezoic';
        if (src.includes('adbutler')) return 'adbutler';
        if (src.includes('adsterra')) return 'adsterra';
        if (src.includes('propellerads')) return 'propellerads';
        if (src.includes('revcontent')) return 'revcontent';
        if (src.includes('mgid')) return 'mgid';
        if (src.includes('adcash')) return 'adcash';
        if (src.includes('exoclick')) return 'exoclick';
        if (src.includes('adnow')) return 'adnow';
        if (src.includes('bidvertiser')) return 'bidvertiser';
        if (src.includes('popads')) return 'popads';
        if (src.includes('adtech')) return 'adtech';
        if (src.includes('zedo')) return 'zedo';
        if (src.includes('adspirit')) return 'adspirit';
        if (src.includes('adglare')) return 'adglare';
        if (src.includes('adpushup')) return 'adpushup';
        if (src.includes('adnxs')) return 'adnxs';
        if (src.includes('adition')) return 'adition';
        if (src.includes('plista')) return 'plista';
    }

    // Check for common ad network global variables
    if (window.google_ad_client) return 'google';
    if (window.fbq) return 'facebook';
    if (window._taboola) return 'taboola';
    if (window.outbrain) return 'outbrain';
    if (window.criteo) return 'criteo';

    // If no network detected, try to get additional identifiers
    const adIdentifiers = {
        dataAttributes: this.getDataAttributes(adElement),
        scriptSources: this.getAdScriptSources(adElement),
        iframeSources: this.getAdIframeSources(adElement),
        networkCookies: this.getAdNetworkCookies(),
        globalVariables: this.getAdGlobalVariables()
    };

    // Log the additional identifiers for debugging
    utils.debugInfo('Additional ad identifiers:', adIdentifiers);

    return 'unknown';
}

setupRedirectDetection() {
    // Monitor history changes
    window.addEventListener('beforeunload', (event) => {
        const redirectData = this.getRedirectData();
        if (redirectData.suspicious) {
            event.preventDefault();
            event.returnValue = 'This page is attempting to redirect you. Are you sure you want to leave?';
        }
    });

    // Monitor location changes
    let originalPushState = history.pushState;
    let originalReplaceState = history.replaceState;

    history.pushState = function() {
        const args = arguments;
        utils.detectSuspiciousNavigation('pushState', args[2]);
        return originalPushState.apply(history, args);
    };

    history.replaceState = function() {
        const args = arguments;
        utils.detectSuspiciousNavigation('replaceState', args[2]);
        return originalReplaceState.apply(history, args);
    };
}

analyzeAdSecurity(adElement) {
    return {
        threatLevel: this.determineThreatLevel(adElement),
        suspiciousPatterns: this.detectSuspiciousPatterns(adElement),
        redirectAttempts: this.getRedirectData(),
        maliciousScripts: this.detectMaliciousScripts(adElement),
        popupBehavior: this.detectPopupBehavior(adElement),
        knownBadActors: this.checkAgainstBadActorList(adElement),
        securityViolations: this.checkSecurityViolations(adElement)
    };
}
checkSecurityViolations(adElement) {
    const violations = {
        csp: [],
        xss: [],
        iframe: [],
        permissions: []
    };

    // Check for CSP violations
    const scripts = Array.from(adElement.getElementsByTagName('script'));
    scripts.forEach(script => {
        if (!script.hasAttribute('nonce') && !script.hasAttribute('integrity')) {
            violations.csp.push({
                type: 'missing-security-attributes',
                element: script.outerHTML
            });
        }

        if (script.innerHTML.includes('unsafe-inline') || script.innerHTML.includes('unsafe-eval')) {
            violations.csp.push({
                type: 'unsafe-directives',
                element: script.outerHTML
            });
        }
    });

    // Check for potential XSS vulnerabilities
    const innerHTML = adElement.innerHTML;
    const xssPatterns = [
        /<script[^>]*>[\s\S]*?<\/script>/gi,
        /javascript:/gi,
        /data:/gi,
        /vbscript:/gi,
        /on\w+\s*=/gi
    ];

    xssPatterns.forEach(pattern => {
        const matches = innerHTML.match(pattern);
        if (matches) {
            violations.xss.push({
                type: 'potential-xss',
                pattern: pattern.toString(),
                matches: matches
            });
        }
    });

    // Check iframe security
    const iframes = Array.from(adElement.getElementsByTagName('iframe'));
    iframes.forEach(iframe => {
        if (!iframe.hasAttribute('sandbox')) {
            violations.iframe.push({
                type: 'missing-sandbox',
                element: iframe.outerHTML
            });
        }

        if (!iframe.hasAttribute('referrerpolicy')) {
            violations.iframe.push({
                type: 'missing-referrer-policy',
                element: iframe.outerHTML
            });
        }
    });

    // Check for suspicious permission requests
    const permissionAPIs = [
        'geolocation',
        'notifications',
        'microphone',
        'camera',
        'clipboard-read',
        'clipboard-write'
    ];

    permissionAPIs.forEach(permission => {
        if (innerHTML.includes(`navigator.permissions.query`) &&
            innerHTML.includes(permission)) {
            violations.permissions.push({
                type: 'suspicious-permission-request',
                permission: permission
            });
        }
    });

    return violations;
}

checkAgainstBadActorList(adElement) {
    const knownBadActors = {
        domains: [
            'malicious-ads.com',
            'ad-scams.net',
            'fake-ads.org',
            'malware-ads.com'
        ],
        patterns: [
            /eval\(.*base64/i,
            /document\.write\(unescape/i,
            /\.replace\(\/[^]+\/g/,
            /\\x[0-9a-f]{2}/i
        ],
        signatures: [
            'suspicious-ad-network',
            'malvertising-campaign',
            'crypto-miner',
            'redirect-chain'
        ]
    };

    const results = {
        matches: [],
        threatScore: 0,
        details: {}
    };

    // Check domain matches
    const links = Array.from(adElement.getElementsByTagName('a'));
    const iframes = Array.from(adElement.getElementsByTagName('iframe'));
    const scripts = Array.from(adElement.getElementsByTagName('script'));

    // Check URLs against known bad domains
    [...links, ...iframes, ...scripts].forEach(element => {
        const url = element.src || element.href || '';
        knownBadActors.domains.forEach(domain => {
            if (url.includes(domain)) {
                results.matches.push({
                    type: 'domain',
                    value: domain,
                    element: element.tagName
                });
                results.threatScore += 25;
            }
        });
    });

    // Check for malicious code patterns
    scripts.forEach(script => {
        const content = script.textContent || '';
        knownBadActors.patterns.forEach(pattern => {
            if (pattern.test(content)) {
                results.matches.push({
                    type: 'pattern',
                    value: pattern.toString(),
                    element: 'script'
                });
                results.threatScore += 20;
            }
        });
    });

    // Check for known malicious signatures in attributes
    const allElements = adElement.getElementsByTagName('*');
    Array.from(allElements).forEach(element => {
        const attributes = Array.from(element.attributes);
        attributes.forEach(attr => {
            knownBadActors.signatures.forEach(signature => {
                if (attr.value.includes(signature)) {
                    results.matches.push({
                        type: 'signature',
                        value: signature,
                        element: element.tagName,
                        attribute: attr.name
                    });
                    results.threatScore += 15;
                }
            });
        });
    });

    results.details = {
        totalMatches: results.matches.length,
        threatLevel: results.threatScore > 50 ? 'high' :
                    results.threatScore > 25 ? 'medium' : 'low',
        timestamp: new Date().toISOString()
    };

    return results;
}

detectPopupBehavior(adElement) {
    const scripts = Array.from(adElement.getElementsByTagName('script'));
    return scripts.some(script => {
        const content = script.textContent || '';
        // Check for direct window.open calls and variations
        return /window\.open\(/.test(content) ||
               /window\.showModalDialog\(/.test(content) ||
               /window\.showModelessDialog\(/.test(content) ||
               /window\.createPopup\(/.test(content) ||
               /\.focus\(\)/.test(content) ||
               /\.blur\(\)/.test(content) ||
               /\.moveTo\(/.test(content) ||
               /\.resizeTo\(/.test(content) ||
               // Check for common popup library calls
               /\.modal\(/.test(content) ||
               /\.popup\(/.test(content) ||
               /\.dialog\(/.test(content) ||
               // Check for inline event handlers that might trigger popups
               /onclick=["']window\.open/.test(content);
    });
}

getRedirectData() {
    const redirectData = {
        suspicious: false,
        attempts: [],
        patterns: []
    };

    // Check for common redirect patterns
    const suspiciousPatterns = [
        /window\.location\s*=/,
        /location\.href\s*=/,
        /location\.replace\s*\(/,
        /window\.navigate\s*\(/,
        /document\.location\s*=/,
        /(setTimeout|setInterval).*location\./
    ];

    // Scan all scripts in the document
    const scripts = document.getElementsByTagName('script');
    for (const script of scripts) {
        const content = script.textContent || '';

        // Check for suspicious redirect patterns
        for (const pattern of suspiciousPatterns) {
            if (pattern.test(content)) {
                redirectData.suspicious = true;
                redirectData.patterns.push({
                    pattern: pattern.toString(),
                    source: script.src || 'inline script'
                });
            }
        }
    }

    // Check for rapid navigation attempts
    const navigationAttempts = this.navigationHistory || [];
    if (navigationAttempts.length > 3) {
        const recentAttempts = navigationAttempts.slice(-3);
        const timespan = recentAttempts[2].timestamp - recentAttempts[0].timestamp;

        if (timespan < 1000) { // 3 attempts within 1 second
            redirectData.suspicious = true;
            redirectData.attempts = recentAttempts;
        }
    }

    return redirectData;
}

determineThreatLevel(adElement) {
    let score = 0;
    const patterns = this.detectSuspiciousPatterns(adElement);

    // Increment score based on suspicious patterns
    if (patterns.hasObfuscatedCode) score += 3;
    if (patterns.hasEvalCode) score += 2;
    if (patterns.hasSuspiciousRedirects) score += 3;
    if (patterns.hasPopupTriggers) score += 2;
    if (patterns.hasEncryptedParams) score += 1;

    // Check for known malicious domains
    if (this.checkAgainstBadActorList(adElement).isKnownBadActor) score += 5;

    // Return threat level based on score
    if (score >= 5) return 'high';
    if (score >= 3) return 'medium';
    return 'low';
}

detectSuspiciousPatterns(adElement) {
    return {
        hasObfuscatedCode: this.checkForObfuscatedCode(adElement),
        hasEvalCode: this.checkForEvalUsage(adElement),
        hasSuspiciousRedirects: this.checkForSuspiciousRedirects(adElement),
        hasPopupTriggers: this.checkForPopupTriggers(adElement),
        hasEncryptedParams: this.checkForEncryptedParameters(adElement)
    };
}
checkForEncryptedParameters(adElement) {
    const scripts = Array.from(adElement.getElementsByTagName('script'));
    const links = Array.from(adElement.getElementsByTagName('a'));

    // Check scripts for encrypted parameters
    const hasEncryptedScriptParams = scripts.some(script => {
        const content = script.textContent || '';
        return /[?&][^=&]+=[a-zA-Z0-9+/]{40,}(?:[=]{0,2})/.test(content) || // Base64
               /%[0-9A-F]{2}/.test(content) || // URL encoded
               /\\x[0-9A-F]{2}/.test(content) || // Hex encoded
               /\\u[0-9A-F]{4}/.test(content); // Unicode encoded
    });

    // Check links for encrypted parameters
    const hasEncryptedLinkParams = links.some(link => {
        const href = link.href || '';
        return /[?&][^=&]+=[a-zA-Z0-9+/]{40,}(?:[=]{0,2})/.test(href) || // Base64
               /%[0-9A-F]{2}/.test(href) || // URL encoded
               /\\x[0-9A-F]{2}/.test(href) || // Hex encoded
               /\\u[0-9A-F]{4}/.test(href); // Unicode encoded
    });

    return hasEncryptedScriptParams || hasEncryptedLinkParams;
}

checkForPopupTriggers(adElement) {
    const scripts = Array.from(adElement.getElementsByTagName('script'));
    return scripts.some(script => {
        const content = script.textContent || '';
        // Check for various popup and window manipulation patterns
        return /window\.open\(/.test(content) ||
               /window\.showModalDialog\(/.test(content) ||
               /window\.showModelessDialog\(/.test(content) ||
               /window\.createPopup\(/.test(content) ||
               /\.focus\(\)/.test(content) ||
               /\.blur\(\)/.test(content) ||
               /\.moveTo\(/.test(content) ||
               /\.resizeTo\(/.test(content) ||
               // Check for common popup library calls
               /\.modal\(/.test(content) ||
               /\.popup\(/.test(content) ||
               /\.dialog\(/.test(content) ||
               // Check for inline event handlers that might trigger popups
               /onclick=["']window\.open/.test(content);
    });
}

checkForEvalUsage(adElement) {
    const scripts = Array.from(adElement.getElementsByTagName('script'));
    return scripts.some(script => {
        const content = script.textContent || '';
        // Check for direct eval calls and other dangerous execution patterns
        return /eval\(/.test(content) ||
               /new Function\(/.test(content) ||
               /setTimeout\(['"](.*?)['"]\)/.test(content) || // String-based setTimeout
               /setInterval\(['"](.*?)['"]\)/.test(content) || // String-based setInterval
               /Function\(.*?\)/.test(content); // Function constructor
    });
}

checkForObfuscatedCode(adElement) {
    const scripts = Array.from(adElement.getElementsByTagName('script'));
    return scripts.some(script => {
        const content = script.textContent || '';
        // Check for common obfuscation patterns
        return /eval\(function\(p,a,c,k,e,d\)/.test(content) || // Packed code
               /[\u200c-\u200f]/.test(content) || // Zero-width characters
               /(atob|btoa)\(/.test(content) || // Base64 encoding
               /String\.fromCharCode/.test(content); // Character code conversion
    });
}

checkForSuspiciousRedirects(adElement) {
    const href = adElement.href || '';
    return {
        hasMultipleRedirects: href.includes('redirect') || href.includes('goto'),
        hasCloakedUrl: this.checkForUrlCloaking(href),
        hasTrackingChain: href.includes('?url=') || href.includes('&url='),
        isShortened: this.isUrlShortened(href)
    };
}

detectMaliciousScripts(adElement) {
    const scripts = Array.from(adElement.getElementsByTagName('script'));
    return scripts.map(script => {
        const content = script.textContent || '';
        return {
            hasEval: /eval\(/.test(content),
            hasDynamicExecution: /new Function\(/.test(content),
            modifiesDOM: /document\.write/.test(content),
            modifiesLocation: /location\.(href|replace|assign)/.test(content),
            opensPopups: /window\.open/.test(content),
            accessesLocalStorage: /localStorage/.test(content),
            hasSuspiciousAPICalls: this.checkForSuspiciousAPICalls(content)
        };
    });
}

checkForSuspiciousAPICalls(scriptContent) {
    const suspiciousPatterns = [
        'navigator.userAgent',
        'document.cookie',
        'window.history',
        'window.name',
        'window.opener',
        'window.parent',
        'window.top',
        'XMLHttpRequest',
        'fetch(',
        'WebSocket',
        'ServiceWorker',
        'Notification',
        'geolocation',
        'camera',
        'microphone'
    ];

    return suspiciousPatterns.filter(pattern =>
        scriptContent.includes(pattern)
    );
}
