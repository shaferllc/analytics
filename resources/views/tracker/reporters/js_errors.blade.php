trackOverconstrainedError() {
    if (typeof OverconstrainedError !== 'undefined') {
        window.addEventListener('overconstrained', (event) => {
            const error = event.error;
            this.queueRequest({
                name: 'overconstrained_error',
                value: {
                    type: error.constructor.name,
                    message: error.message,
                    stack: error.stack,
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
            utils.debugLog('OverconstrainedError tracked:', error);
        });
    }
}

trackJSErrors() {
    // Capture window errors
    window.addEventListener('error', (event) => {
        this.reportError(event.message, event.filename, event.lineno, event.colno, event.error);
    });

    // Capture unhandled promise rejections
    window.addEventListener('unhandledrejection', (event) => {
        this.reportError(event.reason.message, null, null, null, event.reason);
    });

    // Capture worker errors
    if (typeof WorkerGlobalScope !== 'undefined' && self instanceof WorkerGlobalScope) {
        self.addEventListener('error', (event) => {
            this.reportError(event.message, event.filename, event.lineno, event.colno, event.error);
        });
    }

    // Capture resource errors (e.g., image loading errors)
    window.addEventListener('error', (event) => {
        if (event.target instanceof HTMLElement) {
            this.reportError(`Resource loading error: ${event.target.src || event.target.href}`, null, null, null, null);
        }
    }, true);
}

reportError(msg, url, lineNo, columnNo, error) {
    this.queueRequest({
        name: 'js_error',
        value: {
            type: 'error',
            message: msg || 'Unknown error',
            source: url,
            lineno: lineNo,
            colno: columnNo,
            error: error ? {
                name: error.name || 'Error',
                message: error.message || 'Unknown error',
                stack: error.stack || '',
                cause: error.cause || null,
                // Additional error details
                type: error.type || null,
                code: error.code || null,
                fileName: error.fileName || null,
                lineNumber: error.lineNumber || null,
                columnNumber: error.columnNumber || null,
                description: error.description || null
            } : null,
            timestamp: new Date().toISOString(),
            url: window.location.href,
            userAgent: navigator.userAgent,
            // Additional context
            pageState: {
                readyState: document.readyState,
                documentMode: document.documentMode,
                compatMode: document.compatMode,
                lastModified: document.lastModified
            },
            performance: {
                memory: performance?.memory ? {
                    jsHeapSizeLimit: performance.memory.jsHeapSizeLimit,
                    totalJSHeapSize: performance.memory.totalJSHeapSize,
                    usedJSHeapSize: performance.memory.usedJSHeapSize
                } : null,
                navigation: performance?.navigation ? {
                    redirectCount: performance.navigation.redirectCount,
                    type: performance.navigation.type
                } : null
            }
        }
    });
    utils.debugLog('Exception tracked:', {msg, url, lineNo, columnNo, error});
    return false;
};

trackJSErrors() {
    // Capture window errors
    w.addEventListener('error', (event) => {
        this.queueRequest({
            name: 'js_error',
            value: {
                type: 'error',
                message: event.message || 'Unknown error',
                source: event.filename || 'Unknown source',
                lineno: event.lineno || 0, 
                colno: event.colno || 0,
                error: event.error ? {
                    name: event.error.name || 'Error',
                    message: event.error.message || 'Unknown error message',
                    stack: event.error.stack || '',
                    cause: event.error.cause || null,
                    // Additional error details
                    type: event.error.type || null,
                    code: event.error.code || null,
                    fileName: event.error.fileName || null,
                    lineNumber: event.error.lineNumber || null,
                    columnNumber: event.error.columnNumber || null,
                    description: event.error.description || null
                } : null,
                timestamp: new Date().toISOString(),
                url: window.location.href,
                userAgent: navigator.userAgent,
                // Additional context
                pageState: {
                    readyState: document.readyState,
                    documentMode: document.documentMode,
                    compatMode: document.compatMode,
                    lastModified: document.lastModified
                },
                performance: {
                    memory: performance?.memory ? {
                        jsHeapSizeLimit: performance.memory.jsHeapSizeLimit,
                        totalJSHeapSize: performance.memory.totalJSHeapSize,
                        usedJSHeapSize: performance.memory.usedJSHeapSize
                    } : null,
                    navigation: performance?.navigation ? {
                        redirectCount: performance.navigation.redirectCount,
                        type: performance.navigation.type
                    } : null
                }
            }
        });
        utils.debugLog('JavaScript error tracked:', event);
    });

    // Capture unhandled promise rejections
    w.addEventListener('unhandledrejection', (event) => {
        this.queueRequest({
            name: 'js_error',
            value: {
                type: 'promise_rejection',
                message: event.reason?.message || 'Unhandled Promise Rejection',
                source: 'Promise Rejection',
                error: {
                    name: event.reason?.name || 'UnhandledPromiseRejection',
                    message: event.reason?.message || 'Unknown promise rejection',
                    stack: event.reason?.stack || '',
                    cause: event.reason?.cause || null,
                    // Additional promise rejection details
                    type: event.reason?.type || null,
                    code: event.reason?.code || null,
                    fileName: event.reason?.fileName || null,
                    lineNumber: event.reason?.lineNumber || null,
                    columnNumber: event.reason?.columnNumber || null,
                    description: event.reason?.description || null
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
        utils.debugLog('Unhandled promise rejection tracked:', event);
    });

    // Override console.error to capture console errors
    const originalConsoleError = console.error;
    console.error = (...args) => {
        const stack = new Error().stack;
        this.queueRequest({
            name: 'js_error',
            value: {
                type: 'console_error',
                message: args.map(arg => String(arg)).join(' '),
                stack: stack,
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
        utils.debugLog('Console error tracked:', args);
        originalConsoleError.apply(console, args);
    };

    // Capture exceptions
    w.onerror = (msg, url, lineNo, columnNo, error) => {
        this.queueRequest({
            name: 'js_error',
            value: {
                type: 'exception',
                message: msg,
                source: url,
                lineno: lineNo,
                colno: columnNo,
                error: error ? {
                    name: error.name || 'Error',
                    message: error.message || 'Unknown error',
                    stack: error.stack || '',
                    cause: error.cause || null,
                    // Additional error details
                    type: error.type || null,
                    code: error.code || null,
                    fileName: error.fileName || null,
                    lineNumber: error.lineNumber || null,
                    columnNumber: error.columnNumber || null,
                    description: error.description || null
                } : null,
                timestamp: new Date().toISOString(),
                url: window.location.href,
                userAgent: navigator.userAgent,
                // Additional context
                pageState: {
                    readyState: document.readyState,
                    documentMode: document.documentMode,
                    compatMode: document.compatMode,
                    lastModified: document.lastModified
                },
                performance: {
                    memory: performance?.memory ? {
                        jsHeapSizeLimit: performance.memory.jsHeapSizeLimit,
                        totalJSHeapSize: performance.memory.totalJSHeapSize,
                        usedJSHeapSize: performance.memory.usedJSHeapSize
                    } : null,
                    navigation: performance?.navigation ? {
                        redirectCount: performance.navigation.redirectCount,
                        type: performance.navigation.type
                    } : null
                }
            }
        });
        utils.debugLog('Exception tracked:', {msg, url, lineNo, columnNo, error});
        return false;
    };
}

trackRTCError() {
    if (typeof RTCError !== 'undefined') {
        window.addEventListener('error', (event) => {
            if (event.error instanceof RTCError) {
                const error = event.error;
                this.queueRequest({
                    name: 'rtc_error',
                    value: {
                        type: error.constructor.name,
                        message: error.message,
                        stack: error.stack,
                        errorDetail: error.errorDetail,
                        sdpLineNumber: error.sdpLineNumber,
                        httpRequestStatusCode: error.httpRequestStatusCode,
                        sctpCauseCode: error.sctpCauseCode,
                        receivedAlert: error.receivedAlert,
                        sentAlert: error.sentAlert,
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
                utils.debugLog('RTCError tracked:', error);
            }
        });
    }
}

trackRTCErrorEvent() {
    if (typeof RTCErrorEvent !== 'undefined') {
        window.addEventListener('error', (event) => {
            if (event instanceof RTCErrorEvent) {
                const error = event.error;
                this.queueRequest({
                    name: 'rtc_error_event',
                    value: {
                        type: error.constructor.name,
                        message: error.message,
                        stack: error.stack,
                        errorDetail: error.errorDetail,
                        sdpLineNumber: error.sdpLineNumber,
                        httpRequestStatusCode: error.httpRequestStatusCode,
                        sctpCauseCode: error.sctpCauseCode,
                        receivedAlert: error.receivedAlert,
                        sentAlert: error.sentAlert,
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
                utils.debugLog('RTCErrorEvent tracked:', error);
            }
        });
    }
}

trackSensorErrorEvent() {
    if (typeof SensorErrorEvent !== 'undefined') {
        window.addEventListener('error', (event) => {
            if (event instanceof SensorErrorEvent) {
                const error = event.error;
                this.queueRequest({
                    name: 'sensor_error_event',
                    value: {
                        type: error.constructor.name,
                        message: error.message,
                        stack: error.stack,
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
                utils.debugLog('SensorErrorEvent tracked:', error);
            }
        });
    }
}
trackSpeechSynthesisErrorEvent() {
    if (typeof SpeechSynthesisErrorEvent !== 'undefined') {
        window.addEventListener('error', (event) => {
            if (event instanceof SpeechSynthesisErrorEvent) {
                const error = event.error;
                this.queueRequest({
                    name: 'speech_synthesis_error_event',
                    value: {
                        type: error.constructor.name,
                        message: error.message,
                        stack: error.stack,
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
                utils.debugLog('SpeechSynthesisErrorEvent tracked:', error);
            }
        });
    }
}
trackWebTransportError() {
    if (typeof WebTransportError !== 'undefined') {
        window.addEventListener('error', (event) => {
            if (event.error instanceof WebTransportError) {
                const error = event.error;
                this.queueRequest({
                    name: 'web_transport_error',
                    value: {
                        type: error.constructor.name,
                        message: error.message,
                        stack: error.stack,
                        source: error.source,
                        streamErrorCode: error.streamErrorCode,
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
                utils.debugLog('WebTransportError tracked:', error);
            }
        });
    }
}

trackSpeechRecognitionErrorEvent() {
    if (typeof SpeechRecognitionErrorEvent !== 'undefined') {
        window.addEventListener('error', (event) => {
            if (event instanceof SpeechRecognitionErrorEvent) {
                const error = event.error;
                this.queueRequest({
                    name: 'speech_recognition_error_event',
                    value: {
                        type: error.constructor.name,
                        message: error.message,
                        stack: error.stack,
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
                utils.debugLog('SpeechRecognitionErrorEvent tracked:', error);
            }
        });
    }
}
