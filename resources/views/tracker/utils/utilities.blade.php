const utils = {
    engagementScore: 0, // Initialize the score
    LOG_LEVELS: {
        INFO: 'info',
        WARNING: 'warning',
        ERROR: 'error',
        DEBUG: 'debug'
    },
    bots: [
        'whatsapp', 'telegrambot', 'discordbot', 'skypebot',
        'slackbot', 'vkshare', 'pinterestbot', 'tumblrbot',
        'redditbot', 'instagrambot', 'snapchatbot', 'wechatbot',
        'scrapybot', 'scraperapi', 'zalo', 'viber',
        'outbrain', 'quora', 'embedly', 'yahoo', 'baidu',
        'bot', 'crawler', 'spider', 'slurp', 'googlebot', 'bingbot',
        'yandex', 'baidu', 'duckduckbot', 'yahoo', 'baiduspider',
        'facebookexternalhit', 'twitterbot', 'rogerbot', 'linkedinbot',
        'embedly', 'quora link preview', 'showyoubot', 'outbrain',
        'pinterest', 'slackbot', 'vkShare', 'W3C_Validator',
        'sogou', 'exabot', 'dotbot', 'mail.ru', 'yeti',
        'seznambot', 'coccocbot', 'archive.org', 'uptimerobot',
        'cloudflare', 'cloudfront', 'akamai', 'fastly',
        'puppeteer', 'cypress', 'playwright', 'webdriver',
        'nightwatch', 'casperjs', 'zombie', 'nightmare',
        'phantomjs', 'headless', 'selenium', 'chrome-lighthouse',
        'ahrefsbot', 'semrushbot', 'proximic', 'feedfetcher',
        'mediapartners-google', 'applebot', 'pingdom'
    ],
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

            // Check for click IDs and tracking parameters
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

            const igid = params.get('igid') || currentParams.get('igid'); // Instagram
            if (igid) return 'instagram';

            const liid = params.get('liid') || currentParams.get('liid'); // LinkedIn
            if (liid) return 'linkedin';

            const rdid = params.get('rdid') || currentParams.get('rdid'); // Reddit
            if (rdid) return 'reddit';

            const waid = params.get('waid') || currentParams.get('waid'); // WhatsApp
            if (waid) return 'whatsapp';

            const vkid = params.get('vkid') || currentParams.get('vkid'); // VKontakte
            if (vkid) return 'vkontakte';

            // Additional tracking parameters
            const source = params.get('source') || currentParams.get('source');
            if (source) return source;

            const ref = params.get('ref') || currentParams.get('ref');
            if (ref) return ref;

            const affiliate = params.get('affiliate') || currentParams.get('affiliate');
            if (affiliate) return `affiliate-${affiliate}`;

            const partner = params.get('partner') || currentParams.get('partner');
            if (partner) return `partner-${partner}`;

            // Check domain for common ad platforms and marketing tools
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
            if (domain.includes('mediamath')) return 'mediamath-ads';
            if (domain.includes('appnexus')) return 'appnexus-ads';
            if (domain.includes('thetradedesk')) return 'tradedesk-ads';
            if (domain.includes('amazon-adsystem')) return 'amazon-ads';

            // Email marketing platforms
            if (domain.includes('mailchimp')) return 'mailchimp';
            if (domain.includes('sendgrid')) return 'sendgrid';
            if (domain.includes('constantcontact')) return 'constant-contact';
            if (domain.includes('klaviyo')) return 'klaviyo';
            if (domain.includes('hubspot')) return 'hubspot';
            if (domain.includes('marketo')) return 'marketo';
            if (domain.includes('salesforce')) return 'salesforce';
            if (domain.includes('pardot')) return 'pardot';
            if (domain.includes('mailerlite')) return 'mailerlite';
            if (domain.includes('activecampaign')) return 'activecampaign';
            if (domain.includes('drip')) return 'drip';
            if (domain.includes('convertkit')) return 'convertkit';
            if (domain.includes('aweber')) return 'aweber';
            if (domain.includes('getresponse')) return 'getresponse';
            if (domain.includes('sendinblue')) return 'sendinblue';
            if (domain.includes('omnisend')) return 'omnisend';

            // Check for custom campaign parameters
            const customCampaign = params.get('campaign') || currentParams.get('campaign');
            if (customCampaign) return customCampaign;

            const customMedium = params.get('medium') || currentParams.get('medium');
            if (customMedium) return customMedium;

            return null;
        } catch (e) {
            return null;
        }
    },
    getSocialNetwork: () => {
        // Try multiple methods to detect social network source

        // 1. Check referrer URL
        const referrer = document.referrer;
        if (referrer) {
            const socialNetworks = {
                'facebook.': 'Facebook',
                'fb.com': 'Facebook',
                'messenger.com': 'Facebook Messenger',
                'twitter.': 'Twitter',
                'x.com': 'Twitter',
                't.co': 'Twitter',
                'linkedin.': 'LinkedIn',
                'lnkd.in': 'LinkedIn',
                'instagram.': 'Instagram',
                'pinterest.': 'Pinterest',
                'pin.it': 'Pinterest',
                'reddit.': 'Reddit',
                'tumblr.': 'Tumblr',
                'youtube.': 'YouTube',
                'youtu.be': 'YouTube',
                'tiktok.': 'TikTok',
                'vm.tiktok.': 'TikTok',
                'snapchat.': 'Snapchat',
                'whatsapp.': 'WhatsApp',
                'wa.me': 'WhatsApp',
                'telegram.': 'Telegram',
                't.me': 'Telegram',
                'medium.': 'Medium',
                'vk.': 'VKontakte',
                'weibo.': 'Weibo',
                'line.': 'LINE',
                'line.me': 'LINE',
                'discord.': 'Discord',
                'quora.': 'Quora',
                'mastodon.': 'Mastodon',
                'threads.net': 'Threads',
                'behance.net': 'Behance',
                'dribbble.com': 'Dribbble',
                'flickr.com': 'Flickr',
                'vimeo.com': 'Vimeo',
                'twitch.tv': 'Twitch',
                'meetup.com': 'Meetup',
                'slideshare.net': 'SlideShare',
                'deviantart.com': 'DeviantArt',
                'github.com': 'GitHub',
                'gitlab.com': 'GitLab',
                'bitbucket.org': 'Bitbucket',
                'stackoverflow.com': 'Stack Overflow'
            };

            try {
                const url = new URL(referrer);
                const domain = url.hostname.toLowerCase();
                for (const [key, network] of Object.entries(socialNetworks)) {
                    if (domain.includes(key)) return network;
                }
            } catch (e) {
                // Continue to other detection methods
            }
        }

        // 2. Check URL parameters
        try {
            const urlParams = new URLSearchParams(window.location.search);
            const utmSource = urlParams.get('utm_source')?.toLowerCase();
            if (utmSource) {
                if (utmSource.includes('facebook')) return 'Facebook';
                if (utmSource.includes('twitter')) return 'Twitter';
                if (utmSource.includes('linkedin')) return 'LinkedIn';
                if (utmSource.includes('instagram')) return 'Instagram';
                if (utmSource.includes('pinterest')) return 'Pinterest';
                if (utmSource.includes('reddit')) return 'Reddit';
                if (utmSource.includes('quora')) return 'Quora';
                if (utmSource.includes('youtube')) return 'YouTube';
                if (utmSource.includes('tiktok')) return 'TikTok';
                if (utmSource.includes('snapchat')) return 'Snapchat';
                if (utmSource.includes('whatsapp')) return 'WhatsApp';
                // Add more UTM source checks
            }
        } catch (e) {
            // Continue to other detection methods
        }

        // 3. Check for social network APIs/SDKs
        if (typeof FB !== 'undefined') return 'Facebook';
        if (typeof twttr !== 'undefined') return 'Twitter';
        if (typeof IN !== 'undefined') return 'LinkedIn';
        if (typeof PinUtils !== 'undefined') return 'Pinterest';

        // 4. Check for social sharing buttons/widgets
        const socialElements = document.querySelectorAll(
            '[class*="facebook"], [class*="twitter"], [class*="linkedin"], ' +
            '[class*="instagram"], [class*="pinterest"], [class*="reddit"], ' +
            '[data-social], .social-share, .share-button'
        );
        if (socialElements.length > 0) {
            // Analyze classes to determine network
            for (const element of socialElements) {
                const classList = element.className;
                if (classList.includes('facebook')) return 'Facebook';
                if (classList.includes('twitter')) return 'Twitter';
                if (classList.includes('linkedin')) return 'LinkedIn';
                if (classList.includes('instagram')) return 'Instagram';
                if (classList.includes('pinterest')) return 'Pinterest';
                if (classList.includes('reddit')) return 'Reddit';
                if (classList.includes('quora')) return 'Quora';
                if (classList.includes('youtube')) return 'YouTube';
                if (classList.includes('tiktok')) return 'TikTok';
                // Add more class checks
            }
        }

        return null;
    },
    getSearchEngine: () => {
        // Check URL parameters first
        const urlParams = new URLSearchParams(window.location.search);
        const source = urlParams.get('source')?.toLowerCase();
        if (source?.includes('search')) {
            return source.charAt(0).toUpperCase() + source.slice(1);
        }

        // Check referrer
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
            'aol.': 'AOL',
            'search.': 'Generic Search',
            'searchencrypt.': 'SearchEncrypt',
            'metager.': 'MetaGer',
            'gibiru.': 'Gibiru',
            'swisscows.': 'Swisscows',
            'mojeek.': 'Mojeek',
            'presearch.': 'Presearch',
            'oscobo.': 'Oscobo',
            'peekier.': 'Peekier',
            'searx.': 'SearX',
            'boardreader.': 'BoardReader',
            'yep.': 'YEP',
            'searchalot.': 'Searchalot',
            'info.': 'Info.com',
            'lycos.': 'Lycos',
            'webcrawler.': 'WebCrawler',
            'dogpile.': 'Dogpile',
            'excite.': 'Excite',
            'hotbot.': 'HotBot',
            'search.brave.': 'Brave',
            'neeva.': 'Neeva',
            'you.': 'You.com',
            'kagi.': 'Kagi',
            'phind.': 'Phind',
            'perplexity.': 'Perplexity'
        };

        try {
            const url = new URL(referrer);
            const domain = url.hostname.toLowerCase();
            const path = url.pathname.toLowerCase();
            const params = url.searchParams;

            // Check domain matches
            for (const [key, engine] of Object.entries(searchEngines)) {
                if (domain.includes(key)) return engine;
            }

            // Check for search parameters
            const searchParams = ['q', 'query', 'search', 'text', 'keyword', 'p', 'wd', 'searchfor'];
            for (const param of searchParams) {
                if (params.has(param)) {
                    // Try to determine engine from domain
                    const domainParts = domain.split('.');
                    const possibleEngine = domainParts[domainParts.length - 2];
                    if (possibleEngine) {
                        return possibleEngine.charAt(0).toUpperCase() + possibleEngine.slice(1);
                    }
                    return 'Unknown Search Engine';
                }
            }

            // Check for search in path
            if (path.includes('search') || path.includes('find') || path.includes('lookup')) {
                return 'Unknown Search Engine';
            }

            // Check meta tags
            const metaTags = document.getElementsByTagName('meta');
            for (const tag of metaTags) {
                const content = tag.getAttribute('content')?.toLowerCase();
                if (content?.includes('search')) {
                    return 'Meta Search Engine';
                }
            }

            return null;

        } catch (e) {
            utils.debugError('Error determining search engine:', e);
            return null;
        }
    },
    getTimezone: () => {
        try {
            // Try Intl API first
            if (Intl?.DateTimeFormat) {
                const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                if (timezone) {
                    return timezone;
                }
            }

            // Try getting timezone from browser
            if (navigator.userAgent) {
                const match = navigator.userAgent.match(/\((.*?)\)/);
                if (match && match[1].includes('GMT')) {
                    return match[1].split(' ').find(part => part.includes('GMT'));
                }
            }

            // Try getting from Date object
            const date = new Date();
            if (date.toString) {
                const tzMatch = date.toString().match(/\((.*?)\)/);
                if (tzMatch) {
                    return tzMatch[1];
                }
            }

            // Try getting from toLocaleString
            const locale = date.toLocaleString('en', { timeZoneName: 'long' });
            if (locale) {
                const tzPart = locale.split(',').pop();
                if (tzPart) {
                    return tzPart.trim();
                }
            }

            // Fallback to offset calculation
            const offset = date.getTimezoneOffset();
            const hours = Math.abs(Math.floor(offset / 60));
            const minutes = Math.abs(offset % 60);
            const sign = offset < 0 ? '+' : '-';

            return `UTC${sign}${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;

        } catch (e) {
            utils.debugError('Error getting timezone:', e);
            return 'Unknown';
        }
    },

    getResolution: () => {
        const width = window.screen.width || window.innerWidth;
        const height = window.screen.height || window.innerHeight;
        const pixelRatio = window.devicePixelRatio || 1;
        const colorDepth = window.screen.colorDepth || window.screen.pixelDepth || 'Unknown';

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
            utils.debugError('Failed to get/generate user ID:', error.message);
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


    getIpAddress: () => {
        let ipAddress = '';
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'https://api.ipify.org?format=json', false); // Synchronous request
        xhr.onload = () => {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                ipAddress = response.ip;
            }
        };
        xhr.send();
        return ipAddress;
    },

    getCountry: () => {
        try {
            // Create a mapping of timezone regions to country codes
            const regionToCountry = {
                'America': 'US',
                'Europe': 'EU',
                'Asia': 'AS',
                'Africa': 'AF',
                'Oceania': 'OC',
                'Antarctica': 'AN',
                'Atlantic': 'AT',
                'Indian': 'IN',
                'Pacific': 'PAC',
                'Arctic': 'AR',
                'Southern': 'SO',
                'Northern': 'NO',
                'Western': 'WE',
                'Eastern': 'EA',
                'Central': 'CE'
            };

            // Try to get country from timezone
            const timezone = utils.getTimezone();
            if (timezone) {
                const [region] = timezone.split('/');
                if (region && regionToCountry[region]) {
                    return regionToCountry[region];
                }
            }

            // Try to get country from language with fallback
            const language = navigator.language || navigator.userLanguage || 'en-US';
            const [, countryCode] = language.split('-');

            // Validate country code format (2 uppercase letters)
            if (countryCode && /^[A-Z]{2}$/.test(countryCode)) {
                return countryCode;
            }

            // Fallback to US if no valid info available
            return 'US';
        } catch (e) {
            utils.debugError('Error getting country:', e);
            return 'Unknown';
        }
    },

    getCity: () => {
        try {
            // First try to get city from timezone
            const timezone = utils.getTimezone();
            if (timezone) {
                const city = timezone.split('/')[1];
                if (city) {
                    return city.replace(/_/g, ' ');
                }
            }

            // Fallback to Unknown since we can't use async APIs
            return 'Unknown';

        } catch (e) {
            utils.debugError('Error getting city:', e);
            return 'Unknown';
        }
    },

    getContinent: () => {
        const getContinentFromTimezone = (timezone = 'UTC') => {
            const [region] = timezone.split('/');
            const CONTINENT_MAPPING = new Map([
                ['America', 'North America'],
                ['Europe', 'Europe'],
                ['Asia', 'Asia'],
                ['Africa', 'Africa'],
                ['Australia', 'Oceania'],
                ['Pacific', 'Oceania'],
                ['Indian', 'Asia'],
                ['Atlantic', 'Europe'],
                ['Arctic', 'Europe'],
                ['Southern', 'Antarctica']
            ]);
            return CONTINENT_MAPPING.get(region) || 'Unknown';
        };

        try {
            // 1. First try using timezone
            const timezone = utils.getTimezone();
            if (timezone) {
                return getContinentFromTimezone(timezone);
            }

            // 2. Try IP-based lookup
            const response = fetch('https://ipapi.co/continent_code/');
            const continentCode = response.text();
            const CONTINENT_CODES = {
                'NA': 'North America',
                'SA': 'South America',
                'EU': 'Europe',
                'AS': 'Asia',
                'AF': 'Africa',
                'OC': 'Oceania',
                'AN': 'Antarctica'
            };
            if (CONTINENT_CODES[continentCode]) {
                return CONTINENT_CODES[continentCode];
            }

            return 'Unknown';
        } catch (error) {
            utils.debugError('Error determining continent:', error);
            return 'Unknown';
        }
    },

    getLanguage: () => {
        try {
            // Try browser's preferred languages first (most accurate)
            if (navigator.languages && navigator.languages.length > 0) {
                return navigator.languages[0];
            }

            // Try standard browser language properties
            if (navigator.language) {
                return navigator.language;
            }

            if (navigator.userLanguage) {
                return navigator.userLanguage;
            }

            // Try legacy browser language properties
            if (navigator.browserLanguage) {
                return navigator.browserLanguage;
            }

            if (navigator.systemLanguage) {
                return navigator.systemLanguage;
            }

            // Try HTML lang attribute
            const htmlLang = document.documentElement.lang;
            if (htmlLang) {
                return htmlLang;
            }

            // Try Content-Language meta tag
            const metaLang = document.querySelector('meta[http-equiv="content-language"]')?.content;
            if (metaLang) {
                return metaLang;
            }

            // Try Accept-Language cookie
            const langMatch = document.cookie.match(/X-Language=([^;]+)/);
            if (langMatch) {
                return langMatch[1];
            }

            return 'Unknown';
        } catch (e) {
            utils.debugError('Error getting language:', e);
            return 'Unknown';
        }
    },

    getRegion: () => {
        try {
            // Try to get region from browser's locale
            const locale = navigator.language || navigator.userLanguage;
            if (locale && locale.includes('-')) {
                return locale.split('-')[1].toUpperCase();
            }

            // Try to get region from timezone
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            if (timezone && timezone.includes('/')) {
                const parts = timezone.split('/');
                if (parts.length > 1) {
                    return parts[1].replace(/_/g, ' ');
                }
            }

            // Try to get region from geolocation API
            if (navigator.geolocation) {
                return new Promise((resolve) => {
                    navigator.geolocation.getCurrentPosition(
                        position => {
                            const latitude = position.coords.latitude;
                            const longitude = position.coords.longitude;
                            // Resolve with coordinates if region cannot be determined
                            resolve(`${latitude},${longitude}`);
                        },
                        () => resolve('Unknown')
                    );
                });
            }

            return 'Unknown';
        } catch (e) {
            utils.debugError('Error getting region:', e);
            return 'Unknown';
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
            utils.debugError('Error getting landing page:', e);
            // Return current path+query as fallback
            const url = new URL(w.location.href);
            return url.pathname + url.search;
        }
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
            const sensitiveParams = [
                'token', 'key', 'password', 'secret', 'auth',
                'apikey', 'api_key', 'access_token', 'refresh_token',
                'client_secret', 'private_key', 'session',
                'csrf', 'xsrf', '_token', 'credentials'
            ];

            sensitiveParams.forEach(param => {
                if (params.has(param)) {
                    params.delete(param);
                }
                // Also check for params containing these sensitive terms
                params.forEach((value, key) => {
                    if (key.toLowerCase().includes(param)) {
                        params.delete(key);
                    }
                });
            });

            // Remove any params that look like they contain sensitive data
            params.forEach((value, key) => {
                // Remove long random-looking strings that could be tokens
                if (value.length > 32 && /^[A-Za-z0-9+/=_-]+$/.test(value)) {
                    params.delete(key);
                }
                // Remove anything that looks like a JWT
                if (/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/.test(value)) {
                    params.delete(key);
                }
            });

            return params.toString();
        } catch (e) {
            utils.debugError('Error getting URL query:', e);
            return '';
        }
    },



    getViewportHeight: () => {
        try {
            // Get viewport height using different browser-supported properties
            return window.innerHeight ||
                   document.documentElement.clientHeight ||
                   document.body.clientHeight ||
                   window.screen.height;
        } catch (e) {
            utils.debugError('Error getting viewport height:', e);
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
            utils.debugError('Error getting DOM path:', e);
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
            utils.debugError('Error getting load time:', e);
            return null;
        }
    },
    getCharacterSet: () => {
        try {
            // Try to get character set from document characterSet
            if (document.characterSet) {
                return document.characterSet.toString();
            }

            // Try charset meta tag
            const charsetMeta = document.querySelector('meta[charset]');
            if (charsetMeta) {
                const charset = charsetMeta.getAttribute('charset');
                if (charset) {
                    return charset.toString();
                }
            }

            // Try content-type meta tag
            const contentTypeMeta = document.querySelector('meta[http-equiv="Content-Type"]');
            if (contentTypeMeta) {
                const content = contentTypeMeta.getAttribute('content');
                if (content) {
                    const match = content.match(/charset=([^;]+)/i);
                    if (match && match[1]) {
                        return match[1].toString();
                    }
                }
            }

            // Try document inputEncoding
            if (document.inputEncoding) {
                return document.inputEncoding.toString();
            }

            // Try document charset (legacy)
            if (document.charset) {
                return document.charset.toString();
            }

            // Default to UTF-8 if nothing else found
            return 'UTF-8';
        } catch (e) {
            utils.debugError('Error getting character set:', e);
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
            utils.debugError('Error getting viewport width:', e);
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
            // Add this line to set sessionStartTime in sessionStorage
            w.sessionStorage.setItem('sessionStartTime', now.toString());
        } else {
            sessionId = storedSessionId;
            // Add this line to ensure sessionStartTime exists even for existing sessions
            if (!w.sessionStorage.getItem('sessionStartTime')) {
                w.sessionStorage.setItem('sessionStartTime', storedSessionStart.toString());
            }
        }
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
        const ua = navigator.userAgent.toLowerCase();

        // Use modern User-Agent Client Hints API if available
        if (navigator.userAgentData) {
            const { mobile, platform } = navigator.userAgentData;
            if (mobile) {
                return /(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(ua) ? 'Tablet' : 'Mobile';
            }
        }

        // Check for bots/crawlers first
        if (utils.bots.some(bot => ua.includes(bot.toLowerCase()))) {
            return 'Bot/Crawler';
        }

        // Check for tablets - expanded patterns
        if (/(tablet|ipad|playbook|silk)|(android(?!.*mobi))|\b(kindle|tablet|playbook|surface|nexus 7|nexus 9|nexus 10|xoom|sch-i800|galaxy.*tab|sm-t|gt-p|gt-n|asus.*pad|transformer|tf101|tf201|tf300|tf700|slider|n-06d|n-08d|htc.*flyer|dell.*streak|ideos.*s7|d-01g|d-01j|dell.*venue|venue|xperia.*tablet|shield.*tablet|odys|hudl|nook|playstation.*portable|kobo|tolino|samsung.*tab|lenovo.*tab|lg.*pad|lg.*g.*pad|asus.*p00|huawei.*mediapad|nexus.*tablet|pixel.*c|sm-t|kf[0-9]{2}|shw-m\d{3}|me[0-9]{3}|kfjwi|kfsowi|kfthwi|kfapwi|kfarwi|kfaswi|kfsawi|kfapwa|kfapwi)\b/i.test(ua)) {
            return 'Tablet';
        }

        // Check for mobile devices - expanded patterns
        if (/(mobi|phone|ipod|blackberry|opera mini|fennec|minimo|symbian|psp|nintendo ds|archos|skyfire|puffin|blazer|mobile|iphone|android.*mobile|windows.*phone|bb\d{1,2}c|meego|webos|palm|windows ce|opera mobi|opera mini|iris|3g_t|windows ce|mmp\/|j2me\/|smartphone|iemobile|sprint|lge|vodafone|up.browser|up.link|docomo|kddi|softbank|willcom|htc|samsung|nokia|lg|sonyericsson|mot|webos|hiptop|avantgo|plucker|xiino|novarra|alcatel|amoi|au-mic|audiovox|benq|bird|blackberry|cdm|ddipocket|docomo|dopod|genius|haier|huawei|i-mobile|micromax|motorola|nexian|palm|panasonic|philips|sagem|sanyo|sch|sec|sendo|sgh|sharp|siemens|sie-|softbank|tmobile|utec|utstar|vertu|virgin|vk-v|wellcom|zte)/i.test(ua)) {
            return 'Mobile';
        }

        // Check for smart TVs - expanded patterns
        if (/(smart-tv|smarttv|tv safari|webos.+tv|netcast|viera|bravia|samsung.*tv|toshiba.*tv|hbbtv|apple tv|chromecast|roku|fire tv|android tv|lg.*tv|philips.*tv|panasonic.*tv|sharp.*tv|sony.*tv)/i.test(ua)) {
            return 'Smart TV';
        }

        // Check for wearables - expanded patterns
        if (/(watch|glass|oculus|vive|hololens|gear vr|daydream|cardboard|fitbit|garmin|pebble|smartwatch|apple watch|galaxy watch|mi band|honor band|amazfit|huawei band|oneplus band)/i.test(ua)) {
            return 'Wearable';
        }

        // Check for desktop operating systems - expanded patterns
        if (/(windows nt|win64|win32|macintosh|mac os x|linux|ubuntu|debian|fedora|red hat|suse|gentoo|arch|slackware|mint|elementary os|manjaro|centos)/i.test(ua)) {
            return 'Desktop';
        }

        // Check for Chrome OS - expanded patterns
        if (/(cros|chromium os|chromebook|chrome os)/i.test(ua)) {
            return 'Desktop';
        }

        // Check for Unix/BSD systems - expanded patterns
        if (/(freebsd|openbsd|netbsd|dragonfly|sunos|solaris|aix|hp-ux|irix|unix|bsd|gnu)/i.test(ua)) {
            return 'Desktop';
        }

        // Check for desktop browsers with no mobile/tablet indicators
        if (/(firefox|chrome|safari|opera|edge|trident|msie|brave|vivaldi|seamonkey|palemoon|maxthon|comodo dragon)/i.test(ua) &&
            !/(mobile|tablet|android|iphone|ipad|ipod|windows phone|blackberry|bb|playbook|silk)/i.test(ua)) {
            return 'Desktop';
        }

        // Additional checks for screen size if available
        if (window.screen) {
            const minDimension = Math.min(window.screen.width, window.screen.height);
            if (minDimension >= 1024) {
                return 'Desktop';
            } else if (minDimension >= 600) {
                return 'Tablet';
            } else if (minDimension > 0) {
                return 'Mobile';
            }
        }

        return 'Unknown';
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
            utils.debugError('Error getting resource timing:', e);
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


    getNavigationType: () => {
        try {
            // Check if Navigation Timing API v2 is available
            const navigationEntries = performance.getEntriesByType('navigation');
            if (navigationEntries.length > 0) {
                const entry = navigationEntries[0];
                return {
                    type: entry.type, // 'navigate', 'reload', 'back_forward', 'prerender'
                    redirectCount: entry.redirectCount,
                    unloadEventStart: entry.unloadEventStart,
                    unloadEventEnd: entry.unloadEventEnd,
                    domInteractive: entry.domInteractive,
                    domContentLoadedEventStart: entry.domContentLoadedEventStart,
                    domContentLoadedEventEnd: entry.domContentLoadedEventEnd,
                    domComplete: entry.domComplete,
                    loadEventStart: entry.loadEventStart,
                    loadEventEnd: entry.loadEventEnd,
                    duration: entry.duration,
                    transferSize: entry.transferSize,
                    decodedBodySize: entry.decodedBodySize,
                    encodedBodySize: entry.encodedBodySize
                };
            }

            // Fallback to Navigation Timing API v1
            if (performance.navigation) {
                const timing = performance.timing;
                const navTypes = {
                    0: 'navigate',
                    1: 'reload',
                    2: 'back_forward',
                    255: 'prerender'
                };
                return {
                    type: navTypes[performance.navigation.type] || 'Unknown',
                    redirectCount: performance.navigation.redirectCount,
                    unloadEventStart: timing.unloadEventStart,
                    unloadEventEnd: timing.unloadEventEnd,
                    domInteractive: timing.domInteractive,
                    domContentLoadedEventStart: timing.domContentLoadedEventStart,
                    domContentLoadedEventEnd: timing.domContentLoadedEventEnd,
                    domComplete: timing.domComplete,
                    loadEventStart: timing.loadEventStart,
                    loadEventEnd: timing.loadEventEnd,
                    duration: timing.loadEventEnd - timing.navigationStart,
                    transferSize: null,
                    decodedBodySize: null,
                    encodedBodySize: null
                };
            }

            return {
                type: 'Unknown',
                redirectCount: null,
                unloadEventStart: null,
                unloadEventEnd: null,
                domInteractive: null,
                domContentLoadedEventStart: null,
                domContentLoadedEventEnd: null,
                domComplete: null,
                loadEventStart: null,
                loadEventEnd: null,
                duration: null,
                transferSize: null,
                decodedBodySize: null,
                encodedBodySize: null
            };
        } catch (e) {
            utils.debugError('Error getting navigation type:', e);
            return {
                type: 'Unknown',
                error: e.message
            };
        }
    },
    getPageDescription: () => {
        const metaDesc = document.querySelector('meta[name="description"]');
        return metaDesc ? metaDesc.getAttribute('content') : '';
    },

    getPageKeywords: () => {
        try {
            // Try to get keywords meta tag
            const metaKeywords = document.querySelector('meta[name="keywords"]');
            if (metaKeywords) {
                const keywords = metaKeywords.getAttribute('content');
                return keywords ? keywords.trim() : '';
            }

            // Fallback to article:tag meta tags
            const articleTags = Array.from(document.querySelectorAll('meta[property="article:tag"]'));
            if (articleTags.length) {
                return articleTags.map(tag => tag.getAttribute('content')).join(',');
            }

            // Last resort - try to extract keywords from headings
            const headings = Array.from(document.querySelectorAll('h1, h2'));
            const words = headings
                .map(h => h.textContent.toLowerCase())
                .join(' ')
                .split(/\W+/)
                .filter(word => word.length > 3)
                .slice(0, 10)
                .join(',');

            return words || '';

        } catch (error) {
            utils.debugError('Error getting page keywords:', error);
            return '';
        }
    },

    getCanonicalUrl: () => {
        const canonical = document.querySelector('link[rel="canonical"]');
        const currentUrl = window.location.href;
        return canonical ? canonical.href : currentUrl;
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
                utils.debugError('Error parsing structured data:', e);
            }
        });
        return structuredData;
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
        // Try multiple meta tag variations for robots directives
        const robotsSelectors = [
            'meta[name="robots"]',
            'meta[name="ROBOTS"]',
            'meta[name="googlebot"]',
            'meta[name="bingbot"]'
        ];

        // Combine all robot directives
        const robotDirectives = [];
        robotsSelectors.forEach(selector => {
            const meta = document.querySelector(selector);
            if (meta) {
                const content = meta.getAttribute('content');
                if (content) {
                    robotDirectives.push(content.toLowerCase());
                }
            }
        });

        // Return combined unique directives or null if none found
        return robotDirectives.length > 0 ? [...new Set(robotDirectives)].join(', ') : null;
    },

    getLastModified: () => {
        const lastModified = document.lastModified;
        return lastModified ? new Date(lastModified).toISOString() : null;
    },
    getPageDepth: (maxDepthLimit = 100) => {
        const calculateDepth = (element, currentDepth = 0) => {
            // Return current depth if we hit the max depth limit
            if (!element || currentDepth >= maxDepthLimit) return currentDepth;

            let deepestDepth = currentDepth;
            for (const child of element.children) {
                const childDepth = calculateDepth(child, currentDepth + 1);
                deepestDepth = Math.max(deepestDepth, childDepth);
            }

            return deepestDepth;
        };

        // Calculate the maximum DOM depth starting from the body element
        const maxDOMDepth = calculateDepth(document.body);

        // Calculate scroll depth as percentage
        const scrollDepth = Math.round((window.scrollY + window.innerHeight) / document.documentElement.scrollHeight * 100);

        // Return both metrics
        return {
            domDepth: maxDOMDepth,
            scrollDepth: scrollDepth
        };
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

            // Strip all query parameters
            path = path.split('?')[0];

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
            utils.debugError('Error getting referrer:', e);
            return null;
        }
    },

    getDeviceBrand: () => {
        // Try modern client hints API first
        if (navigator.userAgentData) {
            return navigator.userAgentData.platform || 'Unknown';
        }

        const ua = navigator.userAgent;
        let match;

        // Match common device brand patterns
        if ((match = ua.match(/iPhone|iPad|iPod/i))) {
            return 'Apple';
        } else if ((match = ua.match(/Samsung|Galaxy/i))) {
            return 'Samsung';
        } else if ((match = ua.match(/Huawei/i))) {
            return 'Huawei';
        } else if ((match = ua.match(/Xiaomi|Redmi/i))) {
            return 'Xiaomi';
        } else if ((match = ua.match(/OPPO/i))) {
            return 'OPPO';
        } else if ((match = ua.match(/vivo/i))) {
            return 'Vivo';
        } else if ((match = ua.match(/OnePlus/i))) {
            return 'OnePlus';
        } else if ((match = ua.match(/Google|Pixel/i))) {
            return 'Google';
        } else if ((match = ua.match(/LG/i))) {
            return 'LG';
        } else if ((match = ua.match(/Sony/i))) {
            return 'Sony';
        } else if ((match = ua.match(/Nokia/i))) {
            return 'Nokia';
        } else if ((match = ua.match(/Motorola|Moto/i))) {
            return 'Motorola';
        }

        return 'Unknown';
    },
    getDeviceModel: () => {
        // Try modern client hints API first
        if (navigator.userAgentData) {
            return navigator.userAgentData.model || 'Unknown';
        }

        const ua = navigator.userAgent;
        let match;

        // Match common device model patterns
        if ((match = ua.match(/iPhone\s*(\d+,\d+|\d+)?/i))) {
            return `iPhone ${match[1] || ''}`.trim();
        } else if ((match = ua.match(/iPad\s*(\d+,\d+|\d+)?/i))) {
            return `iPad ${match[1] || ''}`.trim();
        } else if ((match = ua.match(/Galaxy\s+([^;\)]+)/i))) {
            return `Galaxy ${match[1]}`;
        } else if ((match = ua.match(/Pixel\s+(\d+[a-z]*)/i))) {
            return `Pixel ${match[1]}`;
        } else if ((match = ua.match(/Huawei\s+([^;\)]+)/i))) {
            return `Huawei ${match[1]}`;
        } else if ((match = ua.match(/Redmi\s+([^;\)]+)/i))) {
            return `Redmi ${match[1]}`;
        } else if ((match = ua.match(/OPPO\s+([^;\)]+)/i))) {
            return `OPPO ${match[1]}`;
        } else if ((match = ua.match(/OnePlus\s+([^;\)]+)/i))) {
            return `OnePlus ${match[1]}`;
        }

        return 'Unknown';
    },
    getOSName: () => {
        // Try modern client hints API first
        if (navigator.userAgentData) {
            return navigator.userAgentData.platform || 'Unknown';
        }

        const ua = navigator.userAgent;
        let match;

        // Match common OS patterns
        if ((match = ua.match(/Windows NT/i))) {
            return 'Windows';
        } else if ((match = ua.match(/Mac OS X/i))) {
            return 'macOS';
        } else if ((match = ua.match(/iPhone|iPad|iPod/i))) {
            return 'iOS';
        } else if ((match = ua.match(/Android/i))) {
            return 'Android';
        } else if ((match = ua.match(/Linux/i))) {
            return 'Linux';
        } else if ((match = ua.match(/CrOS/i))) {
            return 'Chrome OS';
        } else if ((match = ua.match(/Firefox OS/i))) {
            return 'Firefox OS';
        } else if ((match = ua.match(/BlackBerry|BB10/i))) {
            return 'BlackBerry';
        } else if ((match = ua.match(/Windows Phone/i))) {
            return 'Windows Phone';
        }

        return 'Unknown';
    },

    getOSVersion: () => {
        // Try modern client hints API first
        if (navigator.userAgentData) {
            return navigator.userAgentData.platform || 'Unknown';
        }

        const ua = navigator.userAgent;
        let match;

        // Match Windows version
        if ((match = ua.match(/Windows NT (\d+\.\d+)/i))) {
            const versions = {
                '10.0': '10/11',
                '6.3': '8.1',
                '6.2': '8',
                '6.1': '7',
                '6.0': 'Vista',
                '5.2': 'XP x64',
                '5.1': 'XP',
                '5.0': '2000'
            };
            return versions[match[1]] || match[1];
        }

        // Enhanced macOS version detection
        if ((match = ua.match(/Mac OS X (\d+[._]\d+[._]?\d*)|Mac OS X|macOS (\d+[._]\d+[._]?\d*)|macOS/i))) {
            // Handle different formats of macOS version strings
            let version = match[1] || match[2];

            // If no version found but macOS is detected
            if (!version && (ua.includes('Mac OS X') || ua.includes('macOS'))) {
                // Try alternate version patterns
                const altMatch = ua.match(/Version\/(\d+[._]\d+[._]?\d*)/i);
                version = altMatch ? altMatch[1] : '';
            }

            // Clean up version string
            if (version) {
                return version.replace(/_/g, '.').replace(/^(\d+\.\d+)$/, '$1.0');
            }

            return 'Unknown Version';
        }

        // Match iOS version
        if ((match = ua.match(/OS (\d+[._]\d+[._]?\d*)/i))) {
            return match[1].replace(/_/g, '.');
        }

        // Match Android version
        if ((match = ua.match(/Android[\s\/](\d+(\.\d+)*)/i))) {
            return match[1];
        }

        // Match Chrome OS version
        if ((match = ua.match(/CrOS.+Chrome\/(\d+(\.\d+)*)/i))) {
            return match[1];
        }

        // Match Firefox OS version
        if ((match = ua.match(/Firefox\/(\d+(\.\d+)*)/i)) && ua.match(/Mobile|Tablet/i)) {
            return match[1];
        }

        // Match BlackBerry version
        if ((match = ua.match(/BB\d+|BlackBerry.+Version\/(\d+(\.\d+)*)/i))) {
            return match[1] || 'Unknown';
        }

        // Match Windows Phone version
        if ((match = ua.match(/Windows Phone (?:OS )?(\d+(\.\d+)*)/i))) {
            return match[1];
        }

        return 'Unknown';
    },

    getEngineVersion: () => {
        const ua = navigator.userAgent;
        let match;

        // Try to match common rendering engine patterns
        if ((match = ua.match(/Gecko\/[\d.]+/i))) {
            return match[0].split('/')[1];
        } else if ((match = ua.match(/WebKit\/[\d.]+/i))) {
            return match[0].split('/')[1];
        } else if ((match = ua.match(/Presto\/[\d.]+/i))) {
            return match[0].split('/')[1];
        } else if ((match = ua.match(/Trident\/[\d.]+/i))) {
            return match[0].split('/')[1];
        } else if ((match = ua.match(/Blink\/[\d.]+/i))) {
            return match[0].split('/')[1];
        }

        return 'Unknown';
    },
    // Browser data
    getBrowserVersion: () => {
        // Try modern client hints API first
        if (navigator.userAgentData) {
            return navigator.userAgentData.brands
                .find(b => !b.brand.includes('Not'))?.version || 'Unknown';
        }

        const ua = navigator.userAgent;
        let match;

        // Match common browser patterns
        if ((match = ua.match(/(Opera|OPR)[\s\/](\d+(\.\d+)?)/i))) {
            return match[2];
        } else if ((match = ua.match(/Edg[\s\/](\d+(\.\d+)?)/i))) {
            return match[1];
        } else if ((match = ua.match(/Chrome[\s\/](\d+(\.\d+)?)/i))) {
            return match[1];
        } else if ((match = ua.match(/Firefox[\s\/](\d+(\.\d+)?)/i))) {
            return match[1];
        } else if ((match = ua.match(/Version[\s\/](\d+(\.\d+)?).+Safari/i))) {
            return match[1];
        } else if ((match = ua.match(/MSIE\s(\d+(\.\d+)?)/i)) || (match = ua.match(/rv:(\d+(\.\d+)?)/i))) {
            return match[1];
        } else if ((match = ua.match(/YaBrowser[\s\/](\d+(\.\d+)?)/i))) {
            return match[1];
        } else if ((match = ua.match(/UCBrowser[\s\/](\d+(\.\d+)?)/i))) {
            return match[1];
        } else if ((match = ua.match(/SamsungBrowser[\s\/](\d+(\.\d+)?)/i))) {
            return match[1];
        } else if ((match = ua.match(/Brave[\s\/](\d+(\.\d+)?)/i))) {
            return match[1];
        }

        // Try to extract version from general pattern if no specific match
        match = ua.match(/[\s\/](\d+(\.\d+)?)/i);
        return match ? match[1] : 'Unknown';
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
            utils.debugError('Error getting redirect count:', e);
            return 0;
        }
    },

    getBrowser: () => {
        const ua = navigator.userAgent;

        // Try modern client hints API first
        if (navigator.userAgentData) {
            const brands = navigator.userAgentData.brands;
            const brand = brands.find(b => !b.brand.includes('Not'));
            if (brand) return brand.brand;
        }

        // Fallback to user agent string parsing
        if (/Opera|OPR/.test(ua)) return 'Opera';
        if (/Edg/.test(ua)) return 'Edge';
        if (/Brave/.test(ua)) return 'Brave';
        if (/Vivaldi/.test(ua)) return 'Vivaldi';
        if (/YaBrowser/.test(ua)) return 'Yandex';
        if (/UCBrowser/.test(ua)) return 'UC Browser';
        if (/SamsungBrowser/.test(ua)) return 'Samsung Browser';
        if (/Chrome/.test(ua)) return 'Chrome';
        if (/Safari/.test(ua)) return 'Safari';
        if (/Firefox|FxiOS/.test(ua)) return 'Firefox';
        if (/MSIE|Trident/.test(ua)) return 'Internet Explorer';
        if (/DuckDuckGo/.test(ua)) return 'DuckDuckGo';
        if (/Maxthon/.test(ua)) return 'Maxthon';
        if (/SeaMonkey/.test(ua)) return 'SeaMonkey';
        if (/Chromium/.test(ua)) return 'Chromium';
        if (/Tor/.test(ua)) return 'Tor Browser';
        if (/Pale Moon/.test(ua)) return 'Pale Moon';
        if (/Waterfox/.test(ua)) return 'Waterfox';
        if (/IceDragon/.test(ua)) return 'IceDragon';
        if (/K-Meleon/.test(ua)) return 'K-Meleon';
        if (/Comodo Dragon/.test(ua)) return 'Comodo Dragon';
        if (/Konqueror/.test(ua)) return 'Konqueror';
        if (/Midori/.test(ua)) return 'Midori';
        if (/QupZilla/.test(ua)) return 'QupZilla';
        if (/Otter/.test(ua)) return 'Otter Browser';

        // Check for mobile browsers
        if (/Instagram/.test(ua)) return 'Instagram Browser';
        if (/FB_IAB/.test(ua)) return 'Facebook In-App Browser';
        if (/Line/.test(ua)) return 'Line Browser';
        if (/KAKAOTALK/.test(ua)) return 'KakaoTalk Browser';
        if (/NAVER/.test(ua)) return 'Naver Browser';
        if (/WeChat/.test(ua)) return 'WeChat Browser';
        if (/QQBrowser/.test(ua)) return 'QQ Browser';
        if (/Baidu/.test(ua)) return 'Baidu Browser';
        if (/MIUI/.test(ua)) return 'Mi Browser';
        if (/HUAWEI/.test(ua)) return 'Huawei Browser';
        if (/OPPO/.test(ua)) return 'OPPO Browser';
        if (/vivo/.test(ua)) return 'Vivo Browser';
        if (/Mercury/.test(ua)) return 'Mercury Browser';
        if (/Puffin/.test(ua)) return 'Puffin Browser';
        if (/Dolphin/.test(ua)) return 'Dolphin Browser';

        return 'Unknown';
    },






   initializeEventListeners(enabledEvents) {
        const trimmedEvents = new Set(enabledEvents.map(event => event.trim()));
        utils.debugInfo('Enabled events:', [...trimmedEvents]);

        Object.entries(eventHandlers).forEach(([eventType, listener]) => {
            if (trimmedEvents.has(eventType)) {
                utils.debugInfo('Initializing event listener:', eventType);
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

    debugInfo: (...args) => {
        utils.debugLog('info', ...args);
    },
    debugWarning: (...args) => {
        utils.debugLog('warning', ...args);
    },
    debugError: (...args) => {
        utils.debugLog('error', ...args);
    },
    debugDebug: (...args) => {
        utils.debugLog('debug', ...args);
    },

    debugLog: (level = 'info', ...args) => {
        // Validate log level
        const validLevels = Object.values(utils.LOG_LEVELS);
        if (!validLevels.includes(level)) {
            level = utils.LOG_LEVELS.INFO;
        }

        // Prepare log data with level
        const logData = {
            level,
            timestamp: new Date().toISOString(),
            message: args.map(arg => {
                try {
                    if (typeof arg === 'object') {
                        const seen = new WeakSet();
                        return JSON.stringify(arg, (key, value) => {
                            if (typeof value === 'object' && value !== null) {
                                if (seen.has(value)) return '[Circular]';
                                seen.add(value);
                            }
                            return value;
                        });
                    }
                    return String(arg);
                } catch (e) {
                    return '[Unserializable Data]';
                }
            }).join(' '),
            userAgent: navigator.userAgent,
            url: window.location.href,
            sessionId: utils.getSessionId()
        };

        // Send to server if configured
        if (TSMonitorConfig.debugLogEndpoint && TSMonitorConfig.serverDebug) {
            try {
                if (!window.tsMonitorDebugLogQueue) {
                    window.tsMonitorDebugLogQueue = [];
                    const MAX_QUEUE_SIZE = 1000;
                    const BATCH_INTERVAL = 5000;

                    const processQueue = async () => {
                        if (window.tsMonitorDebugLogQueue.length > 0) {
                            const batch = window.tsMonitorDebugLogQueue.splice(0, 50);

                            try {
                                const response = await fetch(TSMonitorConfig.debugLogEndpoint, {
                                    method: 'POST',
                                    headers: {
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json',
                                        'X-TSMonitor-Debug': 'true',
                                        'X-CSRF-Token': TSMonitorConfig.csrfToken || ''
                                    },
                                    body: JSON.stringify({
                                        logs: batch,
                                        site_id: TSMonitorConfig.id,
                                        batch_size: batch.length
                                    })
                                });

                                if (!response.ok) {
                                    throw new Error(`HTTP error! status: ${response.status}`);
                                }
                            } catch (error) {
                                console.error('Failed to send debug log batch:', error);
                                batch.forEach(log => {
                                    log.retryCount = (log.retryCount || 0) + 1;
                                    if (log.retryCount <= 3) {
                                        window.tsMonitorDebugLogQueue.unshift(log);
                                    }
                                });
                            }
                        }
                    };

                    setInterval(processQueue, BATCH_INTERVAL);

                    setInterval(() => {
                        if (window.tsMonitorDebugLogQueue.length > MAX_QUEUE_SIZE) {
                            window.tsMonitorDebugLogQueue.splice(MAX_QUEUE_SIZE);
                            console.warn('Debug log queue exceeded maximum size, truncating');
                        }
                    }, BATCH_INTERVAL * 2);
                }

                window.tsMonitorDebugLogQueue.push({
                    ...logData,
                    queueTime: Date.now()
                });

            } catch (error) {
                console.error('Error preparing debug log:', error);
                console.log('[TSMonitor Debug Fallback]', level, ...args);
            }
        }

        // Local console logging with level
        if (TSMonitorConfig.debug) {
            const levelColor = {
                [utils.LOG_LEVELS.INFO]: 'color: #22c55e;',
                [utils.LOG_LEVELS.WARNING]: 'color: #eab308;',
                [utils.LOG_LEVELS.ERROR]: 'color: #ef4444;',
                [utils.LOG_LEVELS.DEBUG]: 'color: #3b82f6;'
            };
            console.log(`%c[TSMonitor ${level.toUpperCase()}]`, levelColor[level], ...args);
        }

        // Browser debug element logging
        if (TSMonitorConfig.browserDebug) {
            const debugElement = document.getElementById('ts-monitor-debug-log');
            if (debugElement) {
                const logEntry = document.createElement('div');
                let logText = `[${new Date().toISOString()}] [${level.toUpperCase()}] `;
                args.forEach((arg) => {
                    if (typeof arg === 'object') {
                        try {
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
                utils.debugError('Invalid URL:', url);
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

    getPerformanceMetrics: () => {
            try {
                // Initialize metrics object
                const metrics = {
                    timing: {},
                    navigation: {},
                    memory: null,
                    resources: null,
                    paint: null,
                    layout: null,
                    errors: [],
                    serverTiming: null,
                    frameRate: null,
                    longTasks: [],
                    visibility: null,
                    userTiming: null
                };

                // Get Navigation Timing metrics
                if (performance.getEntriesByType) {
                    const navigation = performance.getEntriesByType('navigation')[0];
                    if (navigation) {
                        metrics.navigation = {
                            type: navigation.type,
                            redirectCount: navigation.redirectCount,
                            duration: navigation.duration,
                            ttfb: navigation.responseStart - navigation.requestStart,
                            domInteractive: navigation.domInteractive,
                            domComplete: navigation.domComplete,
                            loadEventEnd: navigation.loadEventEnd,
                            dnsLookupTime: navigation.domainLookupEnd - navigation.domainLookupStart,
                            tcpConnectionTime: navigation.connectEnd - navigation.connectStart,
                            serverResponseTime: navigation.responseEnd - navigation.responseStart,
                            domContentLoadedTime: navigation.domContentLoadedEventEnd - navigation.domContentLoadedEventStart,
                            domParsingTime: navigation.domInteractive - navigation.responseEnd,
                            resourceLoadTime: navigation.loadEventEnd - navigation.domContentLoadedEventEnd
                        };
                    }
                }

                // Get Paint Timing metrics
                const paintEntries = performance.getEntriesByType('paint');
                if (paintEntries.length) {
                    metrics.paint = paintEntries.reduce((acc, entry) => {
                        acc[entry.name] = entry.startTime;
                        return acc;
                    }, {});
                }

                // Get Layout Shift metrics with more detail
                let totalLayoutShift = 0;
                let layoutShiftEntries = [];
                const layoutObserver = new PerformanceObserver((list) => {
                    for (const entry of list.getEntries()) {
                        if (!entry.hadRecentInput) {
                            totalLayoutShift += entry.value;
                            layoutShiftEntries.push({
                                value: entry.value,
                                timestamp: entry.startTime,
                                elements: entry.sources.map(source => ({
                                    node: source.node,
                                    currentRect: source.currentRect,
                                    previousRect: source.previousRect
                                }))
                            });
                        }
                    }
                });
                layoutObserver.observe({ entryTypes: ['layout-shift'] });
                metrics.layout = {
                    cumulativeLayoutShift: totalLayoutShift,
                    entries: layoutShiftEntries
                };

                // Enhanced Resource Timing metrics
                const resources = performance.getEntriesByType('resource');
                if (resources.length) {
                    metrics.resources = {
                        count: resources.length,
                        totalSize: resources.reduce((acc, r) => acc + (r.transferSize || 0), 0),
                        totalEncodedSize: resources.reduce((acc, r) => acc + (r.encodedBodySize || 0), 0),
                        totalDecodedSize: resources.reduce((acc, r) => acc + (r.decodedBodySize || 0), 0),
                        types: resources.reduce((acc, r) => {
                            acc[r.initiatorType] = (acc[r.initiatorType] || 0) + 1;
                            return acc;
                        }, {}),
                        timingBreakdown: resources.map(r => ({
                            name: r.name,
                            initiatorType: r.initiatorType,
                            duration: r.duration,
                            transferSize: r.transferSize,
                            dnsTime: r.domainLookupEnd - r.domainLookupStart,
                            tcpTime: r.connectEnd - r.connectStart,
                            requestTime: r.responseStart - r.requestStart,
                            responseTime: r.responseEnd - r.responseStart
                        }))
                    };
                }

                // Enhanced Memory metrics
                if (performance.memory) {
                    metrics.memory = {
                        jsHeapSizeLimit: performance.memory.jsHeapSizeLimit,
                        totalJSHeapSize: performance.memory.totalJSHeapSize,
                        usedJSHeapSize: performance.memory.usedJSHeapSize,
                        heapUtilization: (performance.memory.usedJSHeapSize / performance.memory.jsHeapSizeLimit) * 100
                    };
                }

            // First Input Delay with additional context
            const fidObserver = new PerformanceObserver((list) => {
                const firstInput = list.getEntries()[0];
                if (firstInput) {
                    metrics.timing.firstInputDelay = {
                        delay: firstInput.processingStart - firstInput.startTime,
                        processingTime: firstInput.processingEnd - firstInput.processingStart,
                        target: firstInput.target?.tagName,
                        type: firstInput.name
                    };
                }
            });
            fidObserver.observe({ entryTypes: ['first-input'] });

                // Enhanced Largest Contentful Paint
                const lcpObserver = new PerformanceObserver((list) => {
                    const entries = list.getEntries();
                    const lastEntry = entries[entries.length - 1];
                    metrics.timing.largestContentfulPaint = {
                        time: lastEntry.startTime,
                        size: lastEntry.size,
                        elementType: lastEntry.element?.tagName,
                        url: lastEntry.url
                    };
                });
                lcpObserver.observe({ entryTypes: ['largest-contentful-paint'] });

                // Server Timing
                const serverTimingEntries = performance.getEntriesByType('navigation')[0]?.serverTiming;
                if (serverTimingEntries?.length) {
                    metrics.serverTiming = serverTimingEntries.map(entry => ({
                        name: entry.name,
                        duration: entry.duration,
                        description: entry.description
                    }));
                }

                // Frame Rate monitoring
                let frameRates = [];
                let lastFrameTime = performance.now();
                const frameCallback = (timestamp) => {
                    const frameTime = timestamp - lastFrameTime;
                    const fps = 1000 / frameTime;
                    frameRates.push(fps);
                    if (frameRates.length > 60) frameRates.shift();
                    lastFrameTime = timestamp;
                    requestAnimationFrame(frameCallback);
                };
                requestAnimationFrame(frameCallback);
                metrics.frameRate = {
                    current: () => frameRates[frameRates.length - 1],
                    average: () => frameRates.reduce((a, b) => a + b, 0) / frameRates.length
                };

                // Long Tasks monitoring
                const longTaskObserver = new PerformanceObserver((list) => {
                    metrics.longTasks = list.getEntries().map(entry => ({
                        duration: entry.duration,
                        startTime: entry.startTime,
                        attribution: entry.attribution
                    }));
                });
                longTaskObserver.observe({ entryTypes: ['longtask'] });

                // Page Visibility tracking
                metrics.visibility = {
                    state: document.visibilityState,
                    hidden: document.hidden,
                    visibilityChangeTime: performance.now()
                };
                document.addEventListener('visibilitychange', () => {
                    metrics.visibility = {
                        state: document.visibilityState,
                        hidden: document.hidden,
                        visibilityChangeTime: performance.now()
                    };
                });

                // User Timing marks and measures
                metrics.userTiming = {
                    marks: performance.getEntriesByType('mark'),
                    measures: performance.getEntriesByType('measure')
                };

                // Enhanced error tracking with more context
                window.addEventListener('error', (event) => {
                    metrics.errors.push({
                        message: event.message,
                        source: event.filename,
                        line: event.lineno,
                        column: event.colno,
                        stack: event.error?.stack,
                        type: event.error?.name,
                        timestamp: Date.now(),
                        userAgent: navigator.userAgent,
                        url: window.location.href
                    });
                });

                // Enhanced network information
                if (navigator.connection) {
                    metrics.network = {
                        effectiveType: navigator.connection.effectiveType,
                        downlink: navigator.connection.downlink,
                        rtt: navigator.connection.rtt,
                        saveData: navigator.connection.saveData,
                        type: navigator.connection.type,
                        maxDownlink: navigator.connection.downlinkMax
                    };

                    // Monitor network changes
                    navigator.connection.addEventListener('change', () => {
                        metrics.network = {
                            effectiveType: navigator.connection.effectiveType,
                            downlink: navigator.connection.downlink,
                            rtt: navigator.connection.rtt,
                            saveData: navigator.connection.saveData,
                            type: navigator.connection.type,
                            maxDownlink: navigator.connection.downlinkMax
                        };
                    });
                }

                return metrics;

            } catch (e) {
                utils.debugError('Error collecting performance metrics:', e);
                return null;
            }
        },

    startPerformanceMonitoring: (options = {}) => {
            const defaultOptions = {
                interval: 5000, // Collect metrics every 5 seconds
                maxSamples: 100, // Maximum number of samples to keep
                minSampleInterval: 1000 // Minimum time between samples
            };

            const config = { ...defaultOptions, ...options };
            const samples = [];
            let lastSampleTime = 0;

            const collectSample = () => {
                const now = Date.now();
                if (now - lastSampleTime < config.minSampleInterval) {
                    return;
                }

            const metrics = utils.getPerformanceMetrics();
                if (metrics) {
                    samples.push({
                        timestamp: now,
                        metrics
                    });

                    // Trim old samples if exceeding maxSamples
                    if (samples.length > config.maxSamples) {
                        samples.shift();
                    }

                    lastSampleTime = now;
                }
            };

            // Start periodic collection
            const intervalId = setInterval(collectSample, config.interval);

            // Return control functions
            return {
                stop: () => clearInterval(intervalId),
                getSamples: () => [...samples],
                getLatestMetrics: () => samples[samples.length - 1]?.metrics || null,
            clearSamples: () => samples.length = 0
        };
    },

    getEngagementMetrics: () => {
        try {
            // Calculate time on page
            const timeOnPage = Date.now() - window.performance.timing.navigationStart;

            // Get scroll depth percentage
            const scrollDepth = (() => {
                const docHeight = Math.max(
                    document.body.scrollHeight,
                    document.documentElement.scrollHeight,
                    document.body.offsetHeight,
                    document.documentElement.offsetHeight
                );
                const viewportHeight = window.innerHeight;
                const scrollTop = window.pageYOffset;
                return Math.min(100, Math.round((scrollTop + viewportHeight) / docHeight * 100));
            })();

            // Calculate interaction score (0-1)
            const interactionScore = (() => {
                const clicks = window._totalClicks || 0;
                const keystrokes = window._totalKeystrokes || 0;
                const mouseDistance = window._mouseMovementDistance || 0;
                const scrollDistance = window._lastScrollDistance || 0;

                // Weight different interaction types
                const clickWeight = Math.min(clicks * 0.1, 0.3);
                const keystrokeWeight = Math.min(keystrokes * 0.05, 0.2);
                const mouseWeight = Math.min(mouseDistance / 1000 * 0.1, 0.2);
                const scrollWeight = Math.min(scrollDistance / 1000 * 0.1, 0.3);

                return Math.min(1, clickWeight + keystrokeWeight + mouseWeight + scrollWeight);
            })();

            return {
                timeOnPage,
                scrollDepth,
                score: interactionScore,
                interactions: {
                    clicks: window._totalClicks || 0,
                    keystrokes: window._totalKeystrokes || 0,
                    mouseDistance: window._mouseMovementDistance || 0,
                    scrollDistance: window._lastScrollDistance || 0
                }
            };
        } catch (e) {
            utils.debugError('Error getting engagement metrics:', e);
            return {
                timeOnPage: 0,
                scrollDepth: 0,
                score: 0,
                interactions: {
                    clicks: 0,
                    keystrokes: 0,
                    mouseDistance: 0,
                    scrollDistance: 0
                }
            };
        }
    },

    trackEngagement: () => {
        // Initialize state
        const state = {
            lastInteractionTime: Date.now(),
            lastScrollY: window.scrollY,
            lastMouseX: 0,
            lastMouseY: 0,
            metrics: null,
            lastClickTime: 0,
            clickSequence: [],
            lastKeyTime: 0,
            keySequence: [],
            rageEvents: [], // Track detailed rage events
            lastHoverTime: 0,
            lastCopyTime: 0,
            lastPasteTime: 0,
            lastSelectionTime: 0,
            lastTabTime: 0,
            lastModalTime: 0,
            lastDropdownTime: 0,
            lastSearchTime: 0,
            lastFormTime: 0,
            lastMenuTime: 0,
            lastLinkTime: 0,
            lastButtonTime: 0,
            lastImageTime: 0,
            lastVideoTime: 0,
            lastAudioTime: 0,
            lastFileTime: 0,
            lastDragTime: 0,
            lastResizeTime: 0,
            lastOrientationTime: 0,
            lastNetworkTime: 0,
            lastBatteryTime: 0,
            lastGeolocationTime: 0,
            lastNotificationTime: 0,
            lastShareTime: 0,
            lastPrintTime: 0,
            lastErrorTime: 0
        };

        // Initialize tracking variables in a namespace to avoid global pollution
        window._analytics = {
            scrollDistance: 0,
            clicks: 0,
            keystrokes: 0,
            mouseDistance: 0,
            rageEvents: 0,
            rapidScrollCount: 0,
            rapidClickCount: 0,
            backspaceCount: 0,
            formAbandonments: 0,
            erraticMouseMovements: 0,
            hovers: 0,
            copies: 0,
            pastes: 0,
            textSelections: 0,
            tabSwitches: 0,
            modalInteractions: 0,
            dropdownSelections: 0,
            searchQueries: 0,
            formInteractions: { total: 0, fields: {} },
            menuInteractions: 0,
            linkClicks: 0,
            buttonClicks: 0,
            imageInteractions: 0,
            videoInteractions: { plays: 0, pauses: 0, seeks: 0, volumeChanges: 0 },
            audioInteractions: { plays: 0, pauses: 0, seeks: 0, volumeChanges: 0 },
            fileInteractions: { uploads: 0, downloads: 0 },
            dragAndDrops: 0,
            windowResizes: 0,
            orientationChanges: 0,
            networkChanges: 0,
            batteryChanges: 0,
            geolocationRequests: 0,
            notificationInteractions: 0,
            shareActions: 0,
            printAttempts: 0,
            jsErrors: 0,
            rageData: {
                rapidClicks: [],
                rapidScrolls: [],
                erraticMouseMovements: [],
                rapidKeystrokes: [],
                formAbandonment: []
            }
        };

        // Throttled update function
        const updateEngagement = utils.throttle(() => {
            const timeSinceLastInteraction = Date.now() - state.lastInteractionTime;

            // Get fresh metrics
            state.metrics = utils.getEngagementMetrics();
            let score = state.metrics.score * 100;

            // Calculate bonuses for all interaction types
            score += Math.min(window._analytics.scrollDistance / 1000, 20);
            score += Math.min(window._analytics.clicks * 2, 15);
            score += Math.min(window._analytics.keystrokes / 10, 10);
            score += Math.min(window._analytics.mouseDistance / 1000, 15);
            score += Math.min(window._analytics.hovers / 5, 10);
            score += Math.min(window._analytics.copies + window._analytics.pastes, 5);
            score += Math.min(window._analytics.textSelections / 2, 5);
            score += Math.min(window._analytics.tabSwitches, 5);
            score += Math.min(window._analytics.modalInteractions * 2, 10);
            score += Math.min(window._analytics.dropdownSelections * 2, 10);
            score += Math.min(window._analytics.searchQueries * 3, 15);
            score += Math.min(window._analytics.formInteractions.total / 2, 20);
            score += Math.min(window._analytics.menuInteractions, 10);
            score += Math.min(window._analytics.linkClicks + window._analytics.buttonClicks, 15);
            score += Math.min(window._analytics.imageInteractions, 5);
            score += Math.min(window._analytics.videoInteractions.plays * 3, 15);
            score += Math.min(window._analytics.audioInteractions.plays * 2, 10);
            score += Math.min(window._analytics.fileInteractions.uploads + window._analytics.fileInteractions.downloads, 10);
            score += Math.min(window._analytics.dragAndDrops * 2, 10);

            // Apply rage penalties
            score -= Math.min(window._analytics.rageEvents * 5, 30);
            score -= Math.min(window._analytics.rapidScrollCount * 2, 10);
            score -= Math.min(window._analytics.rapidClickCount * 3, 15);
            score -= Math.min(window._analytics.erraticMouseMovements * 2, 10);
            score -= Math.min(window._analytics.jsErrors * 5, 20);

            // Apply decay after 30 seconds of inactivity
            if (timeSinceLastInteraction > 30000) {
                score *= 0.8;
            }

            utils.updateEngagementScore(Math.round(score));
        }, 1000);

        // Detect rage patterns with detailed tracking
        const detectRagePatterns = {
            rapidClicks: (e) => {
                const now = Date.now();
                state.clickSequence.push(now);
                state.clickSequence = state.clickSequence.filter(time => now - time < 1000);

                if (state.clickSequence.length >= 5) {
                    window._analytics.rapidClickCount++;
                    window._analytics.rageEvents++;
                    window._analytics.rageData.rapidClicks.push({
                        timestamp: now,
                        position: { x: e.pageX, y: e.pageY },
                        element: e.target.tagName,
                        elementPath: utils.getElementPath(e.target),
                        clickSequence: [...state.clickSequence],
                        intensity: state.clickSequence.length
                    });
                }
            },

            rapidScrolls: () => {
                const now = Date.now();
                if (now - state.lastScrollTime < 100) {
                    window._analytics.rapidScrollCount++;
                    if (window._analytics.rapidScrollCount > 5) {
                        window._analytics.rageEvents++;
                        window._analytics.rageData.rapidScrolls.push({
                            timestamp: now,
                            scrollPosition: window.scrollY,
                            scrollDistance: Math.abs(window.scrollY - state.lastScrollY),
                            scrollSpeed: Math.abs(window.scrollY - state.lastScrollY) / 100,
                            intensity: window._analytics.rapidScrollCount
                        });
                    }
                }
                state.lastScrollTime = now;
            },

            erraticMouseMovement: (e) => {
                const now = Date.now();
                const movement = Math.sqrt(
                    Math.pow(e.movementX, 2) + Math.pow(e.movementY, 2)
                );

                if (movement > 100 && now - state.lastMouseTime < 50) {
                    window._analytics.erraticMouseMovements++;
                    window._analytics.rageEvents++;
                    window._analytics.rageData.erraticMouseMovements.push({
                        timestamp: now,
                        position: { x: e.pageX, y: e.pageY },
                        movement: { x: e.movementX, y: e.movementY },
                        speed: movement / 50,
                        intensity: window._analytics.erraticMouseMovements
                    });
                }
                state.lastMouseTime = now;
            },

            rapidKeystrokes: (e) => {
                const now = Date.now();
                state.keySequence.push(now);
                state.keySequence = state.keySequence.filter(time => now - time < 500);

                if (state.keySequence.length > 10) {
                    window._analytics.rageEvents++;
                    window._analytics.rageData.rapidKeystrokes.push({
                        timestamp: now,
                        element: e.target.tagName,
                        elementPath: utils.getElementPath(e.target),
                        keySequence: [...state.keySequence],
                        isBackspace: e.key === 'Backspace',
                        intensity: state.keySequence.length
                    });
                }

                if (e.key === 'Backspace') {
                    window._analytics.backspaceCount++;
                    if (window._analytics.backspaceCount > 5) {
                        window._analytics.rageEvents++;
                    }
                }
            },

            formAbandonment: (e) => {
                if (e.target.form && e.relatedTarget === null) {
                    window._analytics.formAbandonments++;
                    window._analytics.rageEvents++;
                    window._analytics.rageData.formAbandonment.push({
                        timestamp: Date.now(),
                        formId: e.target.form.id || utils.getElementPath(e.target.form),
                        fieldType: e.target.type,
                        fieldName: e.target.name,
                        timeSpentOnForm: Date.now() - state.lastInteractionTime,
                        filledFields: Array.from(e.target.form.elements).filter(el => el.value).length
                    });
                }
            }
        };

        // Event handlers
        const handlers = {
            click: (e) => {
                window._analytics.clicks++;
                if (e.target.tagName === 'A') window._analytics.linkClicks++;
                if (e.target.tagName === 'BUTTON') window._analytics.buttonClicks++;
                if (e.target.tagName === 'IMG') window._analytics.imageInteractions++;
                state.lastInteractionTime = Date.now();
                detectRagePatterns.rapidClicks(e);
                updateEngagement();
            },

            scroll: (e) => {
                const currentScroll = window.scrollY;
                window._analytics.scrollDistance += Math.abs(currentScroll - state.lastScrollY);
                state.lastScrollY = currentScroll;
                state.lastInteractionTime = Date.now();
                detectRagePatterns.rapidScrolls();
                updateEngagement();
            },

            mousemove: (e) => {
                const dx = e.pageX - state.lastMouseX;
                const dy = e.pageY - state.lastMouseY;
                window._analytics.mouseDistance += Math.sqrt(dx * dx + dy * dy);
                state.lastMouseX = e.pageX;
                state.lastMouseY = e.pageY;
                state.lastInteractionTime = Date.now();
                detectRagePatterns.erraticMouseMovement(e);
                updateEngagement();
            },

            keypress: (e) => {
                window._analytics.keystrokes++;
                state.lastInteractionTime = Date.now();
                detectRagePatterns.rapidKeystrokes(e);
                updateEngagement();
            },

            focus: (e) => {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                    window._analytics.formInteractions.total++;
                    const fieldName = e.target.name || e.target.id;
                    if (fieldName) {
                        window._analytics.formInteractions.fields[fieldName] = (window._analytics.formInteractions.fields[fieldName] || 0) + 1;
                    }
                    state.lastInteractionTime = Date.now();
                    updateEngagement();
                }
            },

            blur: (e) => {
                detectRagePatterns.formAbandonment(e);
            },

            mediaEvent: (e) => {
                const mediaType = e.target.tagName.toLowerCase();
                const eventType = e.type;
                const interactions = mediaType === 'video' ? window._analytics.videoInteractions : window._analytics.audioInteractions;

                switch(eventType) {
                    case 'play':
                        interactions.plays++;
                        break;
                    case 'pause':
                        interactions.pauses++;
                        break;
                    case 'seeking':
                        interactions.seeks++;
                        break;
                    case 'volumechange':
                        interactions.volumeChanges++;
                        break;
                }

                state.lastInteractionTime = Date.now();
                updateEngagement();
            },

            hover: (e) => {
                window._analytics.hovers++;
                state.lastHoverTime = Date.now();
                updateEngagement();
            },

            copy: () => {
                window._analytics.copies++;
                state.lastCopyTime = Date.now();
                updateEngagement();
            },

            paste: () => {
                window._analytics.pastes++;
                state.lastPasteTime = Date.now();
                updateEngagement();
            },

            select: () => {
                window._analytics.textSelections++;
                state.lastSelectionTime = Date.now();
                updateEngagement();
            },

            visibilitychange: () => {
                if (!document.hidden) {
                    window._analytics.tabSwitches++;
                    state.lastTabTime = Date.now();
                    updateEngagement();
                }
            },

            error: (e) => {
                window._analytics.jsErrors++;
                state.lastErrorTime = Date.now();
                updateEngagement();
            },

            dragstart: () => {
                window._analytics.dragAndDrops++;
                state.lastDragTime = Date.now();
                updateEngagement();
            },

            resize: () => {
                window._analytics.windowResizes++;
                state.lastResizeTime = Date.now();
                updateEngagement();
            },

            orientationchange: () => {
                window._analytics.orientationChanges++;
                state.lastOrientationTime = Date.now();
                updateEngagement();
            },

            online: () => {
                window._analytics.networkChanges++;
                state.lastNetworkTime = Date.now();
                updateEngagement();
            },

            offline: () => {
                window._analytics.networkChanges++;
                state.lastNetworkTime = Date.now();
                updateEngagement();
            }
        };

        // Attach event listeners
        document.addEventListener('click', handlers.click);
        document.addEventListener('scroll', handlers.scroll);
        document.addEventListener('mousemove', handlers.mousemove);
        document.addEventListener('keypress', handlers.keypress);
        document.addEventListener('focus', handlers.focus, true);
        document.addEventListener('blur', handlers.blur, true);
        document.addEventListener('mouseover', handlers.hover);
        document.addEventListener('copy', handlers.copy);
        document.addEventListener('paste', handlers.paste);
        document.addEventListener('select', handlers.select);
        document.addEventListener('visibilitychange', handlers.visibilitychange);
        document.addEventListener('dragstart', handlers.dragstart);
        window.addEventListener('error', handlers.error);
        window.addEventListener('resize', handlers.resize);
        window.addEventListener('orientationchange', handlers.orientationchange);
        window.addEventListener('online', handlers.online);
        window.addEventListener('offline', handlers.offline);

        // Track video and audio engagement
        document.querySelectorAll('video, audio').forEach(media => {
            ['play', 'pause', 'seeking', 'volumechange'].forEach(event => {
                media.addEventListener(event, handlers.mediaEvent);
            });
        });

        // Reset metrics every 5 minutes but keep rage data
        const resetInterval = setInterval(() => {
            const rageData = window._analytics.rageData;
            window._analytics = {
                scrollDistance: 0,
                clicks: 0,
                keystrokes: 0,
                mouseDistance: 0,
                rageEvents: 0,
                rapidScrollCount: 0,
                rapidClickCount: 0,
                backspaceCount: 0,
                formAbandonments: 0,
                erraticMouseMovements: 0,
                hovers: 0,
                copies: 0,
                pastes: 0,
                textSelections: 0,
                tabSwitches: 0,
                modalInteractions: 0,
                dropdownSelections: 0,
                searchQueries: 0,
                formInteractions: { total: 0, fields: {} },
                menuInteractions: 0,
                linkClicks: 0,
                buttonClicks: 0,
                imageInteractions: 0,
                videoInteractions: { plays: 0, pauses: 0, seeks: 0, volumeChanges: 0 },
                audioInteractions: { plays: 0, pauses: 0, seeks: 0, volumeChanges: 0 },
                fileInteractions: { uploads: 0, downloads: 0 },
                dragAndDrops: 0,
                windowResizes: 0,
                orientationChanges: 0,
                networkChanges: 0,
                batteryChanges: 0,
                geolocationRequests: 0,
                notificationInteractions: 0,
                shareActions: 0,
                printAttempts: 0,
                jsErrors: 0,
                rageData // Preserve rage data
            };
            state.metrics = utils.getEngagementMetrics();
        }, 300000);

        // Return cleanup function and rage data access
        return {
            cleanup: () => {
                document.removeEventListener('click', handlers.click);
                document.removeEventListener('scroll', handlers.scroll);
                document.removeEventListener('mousemove', handlers.mousemove);
                document.removeEventListener('keypress', handlers.keypress);
                document.removeEventListener('focus', handlers.focus, true);
                document.removeEventListener('blur', handlers.blur, true);
                document.removeEventListener('mouseover', handlers.hover);
                document.removeEventListener('copy', handlers.copy);
                document.removeEventListener('paste', handlers.paste);
                document.removeEventListener('select', handlers.select);
                document.removeEventListener('visibilitychange', handlers.visibilitychange);
                document.removeEventListener('dragstart', handlers.dragstart);
                window.removeEventListener('error', handlers.error);
                window.removeEventListener('resize', handlers.resize);
                window.removeEventListener('orientationchange', handlers.orientationchange);
                window.removeEventListener('online', handlers.online);
                window.removeEventListener('offline', handlers.offline);

                document.querySelectorAll('video, audio').forEach(media => {
                    ['play', 'pause', 'seeking', 'volumechange'].forEach(event => {
                        media.removeEventListener(event, handlers.mediaEvent);
                    });
                });

                clearInterval(resetInterval);
            },
            getRageData: () => window._analytics.rageData
        };
    },

    updateEngagementScore(value) {
        try {
            // Ensure value is a number
            const numericValue = Number(value);

            // Only update if value is a valid number
            if (!isNaN(numericValue)) {
                // Get existing score from localStorage or default to 0
                const currentScore = Number(localStorage.getItem('ts_monitor_engagement_score') || 0);

                // Calculate new score
                const newScore = Math.max(0, currentScore + numericValue);

                // Store in both localStorage and instance variable
                localStorage.setItem('ts_monitor_engagement_score', newScore);
                this.engagementScore = newScore;

                // Optional: Log score update if debug mode is enabled
                if (internalConfig?.DEBUG_MODE) {
                    console.log('Engagement score updated:', newScore);
                }
            } else {
                utils.debugError('Invalid engagement score value:', value);
            }
        } catch (e) {
            utils.debugError('Error updating engagement score:', e);
        }
    },

    trackElementVisibility() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (utils.isElementVisible(entry.target)) {
                    const elementData = {
                        path: utils.getElementPath(entry.target),
                        depth: utils.getElementDepth(entry.target),
                        zLevel: utils.getElementZLevel(entry.target)
                    };
                    this.queueRequest({
                        name: 'element_visible',
                        value: elementData
                    });
                }
            });
        });

        document.querySelectorAll('*').forEach(element => {
            if (utils.isElementInteractive(element)) {
                observer.observe(element);
            }
        });
    },

    getTotalInteractions: () => {
        // Get interaction counts from window._analytics if available
        const analytics = window._analytics || {};

        // Sum up all tracked interactions
        const totalInteractions = {
            clicks: analytics.clicks || 0,
            keystrokes: analytics.keystrokes || 0,
            scrollDistance: analytics.scrollDistance || 0,
            mouseDistance: analytics.mouseDistance || 0,
            formInteractions: analytics.formInteractions?.total || 0,
            videoInteractions: {
                plays: analytics.videoInteractions?.plays || 0,
                pauses: analytics.videoInteractions?.pauses || 0,
                seeks: analytics.videoInteractions?.seeks || 0,
                volumeChanges: analytics.videoInteractions?.volumeChanges || 0
            },
            audioInteractions: {
                plays: analytics.audioInteractions?.plays || 0,
                pauses: analytics.audioInteractions?.pauses || 0,
                seeks: analytics.audioInteractions?.seeks || 0,
                volumeChanges: analytics.audioInteractions?.volumeChanges || 0
            },
            linkClicks: analytics.linkClicks || 0,
            buttonClicks: analytics.buttonClicks || 0,
            imageInteractions: analytics.imageInteractions || 0,
            hovers: analytics.hovers || 0,
            copies: analytics.copies || 0,
            pastes: analytics.pastes || 0,
            textSelections: analytics.textSelections || 0,
            tabSwitches: analytics.tabSwitches || 0,
            modalInteractions: analytics.modalInteractions || 0,
            dropdownSelections: analytics.dropdownSelections || 0,
            searchQueries: analytics.searchQueries || 0,
            menuInteractions: analytics.menuInteractions || 0,
            dragAndDrops: analytics.dragAndDrops || 0,
            rageEvents: analytics.rageEvents || 0,
            rapidScrollCount: analytics.rapidScrollCount || 0,
            rapidClickCount: analytics.rapidClickCount || 0,
            erraticMouseMovements: analytics.erraticMouseMovements || 0,
            backspaceCount: analytics.backspaceCount || 0,
            formAbandonments: analytics.formAbandonments || 0
        };

        // Calculate interaction rates
        const durationInMinutes = ((Date.now() - (analytics.startTime || Date.now())) / 1000 / 60);
        const interactionRates = {
            clicksPerMinute: durationInMinutes > 0 ? totalInteractions.clicks / durationInMinutes : 0,
            keystrokesPerMinute: durationInMinutes > 0 ? totalInteractions.keystrokes / durationInMinutes : 0,
            scrollDepthPercentage: analytics.maxScrollDepth || 0,
            averageMouseSpeed: totalInteractions.mouseDistance / (durationInMinutes || 1),
            rageEventsPerMinute: durationInMinutes > 0 ? totalInteractions.rageEvents / durationInMinutes : 0
        };

        return {
            ...totalInteractions,
            ...interactionRates,
            total: Object.values(totalInteractions).reduce((sum, val) =>
                typeof val === 'object' ? sum : sum + val, 0),
            uniqueInteractionTypes: Object.keys(totalInteractions).filter(key =>
                typeof totalInteractions[key] === 'object' ?
                    Object.values(totalInteractions[key]).some(v => v > 0) :
                    totalInteractions[key] > 0
            ).length
        };
    },

    calculateEngagementScore: (analytics) => {
        // Convert duration to minutes
        const durationInMinutes = ((Date.now() - (analytics.startTime || Date.now())) / 1000 / 60);

        // Base engagement metrics
        const baseScore = (() => {
            let score = 0;

            // Click interactions (max 20)
            score += Math.min(analytics.clicks / durationInMinutes, 20);

            // Scroll depth (max 25)
            score += Math.min(analytics.maxScrollDepth / 4, 25);

            // Mouse movement (max 15)
            score += Math.min(analytics.mouseDistance / 1000, 15);

            // Keystrokes (max 15)
            score += Math.min(analytics.keystrokes / durationInMinutes, 15);

            return score;
        })();

        // Media engagement bonus (max 10)
        const mediaBonus = (() => {
            const videoScore = analytics.videoInteractions?.plays || 0;
            const audioScore = analytics.audioInteractions?.plays || 0;
            return Math.min((videoScore + audioScore) * 2, 10);
        })();

        // Form interaction bonus (max 10)
        const formBonus = (() => {
            const formStats = analytics.formInteractions || {};
            return Math.min(formStats.total || 0, 10);
        })();

        // Interactive element bonus (max 5)
        const interactiveBonus = (() => {
            let bonus = 0;
            bonus += Math.min(analytics.linkClicks || 0, 2);
            bonus += Math.min(analytics.buttonClicks || 0, 2);
            bonus += Math.min(analytics.dropdownSelections || 0, 1);
            return bonus;
        })();

        // Rage penalty (max -20)
        const ragePenalty = (() => {
            let penalty = 0;
            penalty += (analytics.rageEvents || 0) * 2;
            penalty += (analytics.rapidScrollCount || 0);
            penalty += (analytics.rapidClickCount || 0);
            penalty += (analytics.erraticMouseMovements || 0);
            penalty += (analytics.formAbandonments || 0) * 2;
            return Math.min(penalty, 20);
        })();

        // Calculate final score
        let finalScore = baseScore + mediaBonus + formBonus + interactiveBonus - ragePenalty;

        // Normalize to 0-100 range
        finalScore = Math.min(Math.max(finalScore, 0), 100);

        return Math.round(finalScore);
    },


};

