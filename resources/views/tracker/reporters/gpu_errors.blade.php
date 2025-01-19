trackGPUInternalErrors() {
    if (typeof WebGLRenderingContext !== 'undefined') {
        const originalGetError = WebGLRenderingContext.prototype.getError;
        WebGLRenderingContext.prototype.getError = function() {
            const error = originalGetError.apply(this, arguments);
            if (error === this.UNKNOWN_ERR) {
                this.queueRequest({
                    type: 'event',
                    name: 'gpu_internal_error',
                    value: {
                        type: 'GPUInternalError',
                        code: error,
                        timestamp: new Date().toISOString(),
                        url: window.location.href,
                        userAgent: navigator.userAgent,
                        // Additional context
                        pageState: {
                            readyState: document.readyState,
                            documentMode: document.documentMode,
                            compatMode: document.compatMode,
                            lastModified: document.lastModified,
                            // Add viewport info
                            viewport: {
                                width: window.innerWidth,
                                height: window.innerHeight,
                                pixelRatio: window.devicePixelRatio
                            },
                            // Add WebGL context info
                            webgl: {
                                vendor: this.getParameter(this.VENDOR),
                                renderer: this.getParameter(this.RENDERER),
                                version: this.getParameter(this.VERSION),
                                shadingLanguageVersion: this.getParameter(this.SHADING_LANGUAGE_VERSION),
                                maxTextureSize: this.getParameter(this.MAX_TEXTURE_SIZE),
                                maxViewportDims: this.getParameter(this.MAX_VIEWPORT_DIMS),
                                maxRenderbufferSize: this.getParameter(this.MAX_RENDERBUFFER_SIZE)
                            }
                        }
                    }
                });
                utils.debugLog('GPU internal error tracked:', error);
            }
            return error;
        };
    }
}

trackGPUOutOfMemoryError() {
    if (typeof WebGLRenderingContext !== 'undefined') {
        const originalGetError = WebGLRenderingContext.prototype.getError;
        WebGLRenderingContext.prototype.getError = function() {
            const error = originalGetError.apply(this, arguments);
            if (error === this.OUT_OF_MEMORY) {
                this.queueRequest({
                    type: 'event',
                    name: 'gpu_out_of_memory_error',
                    value: {
                        type: 'GPUOutOfMemoryError',
                        code: error,
                        timestamp: new Date().toISOString(),
                        url: window.location.href,
                        userAgent: navigator.userAgent,
                        // Additional context
                        pageState: {
                            readyState: document.readyState,
                            documentMode: document.documentMode,
                            compatMode: document.compatMode,
                            lastModified: document.lastModified,
                            // Add memory info
                            memory: {
                                jsHeapSizeLimit: performance?.memory?.jsHeapSizeLimit,
                                totalJSHeapSize: performance?.memory?.totalJSHeapSize,
                                usedJSHeapSize: performance?.memory?.usedJSHeapSize
                            },
                            // Add WebGL memory info
                            webgl: {
                                maxTextureSize: this.getParameter(this.MAX_TEXTURE_SIZE),
                                maxRenderbufferSize: this.getParameter(this.MAX_RENDERBUFFER_SIZE),
                                maxViewportDims: this.getParameter(this.MAX_VIEWPORT_DIMS)
                            }
                        }
                    }
                });
                utils.debugLog('GPU out of memory error tracked:', error);
            }
            return error;
        };
    }
}

trackGPUPipelineError() {
    if (typeof WebGLRenderingContext !== 'undefined') {
        const originalGetError = WebGLRenderingContext.prototype.getError;
        WebGLRenderingContext.prototype.getError = function() {
            const error = originalGetError.apply(this, arguments);
            if (error === this.INVALID_OPERATION) {
                this.queueRequest({
                    type: 'event',
                    name: 'gpu_pipeline_error',
                    value: {
                        type: 'GPUPipelineError',
                        code: error,
                        timestamp: new Date().toISOString(),
                        url: window.location.href,
                        userAgent: navigator.userAgent,
                        // Additional context
                        pageState: {
                            readyState: document.readyState,
                            documentMode: document.documentMode,
                            compatMode: document.compatMode,
                            lastModified: document.lastModified,
                            // Add WebGL pipeline info
                            webgl: {
                                activeTexture: this.getParameter(this.ACTIVE_TEXTURE),
                                currentProgram: this.getParameter(this.CURRENT_PROGRAM),
                                boundFramebuffer: this.getParameter(this.FRAMEBUFFER_BINDING),
                                boundRenderbuffer: this.getParameter(this.RENDERBUFFER_BINDING)
                            }
                        }
                    }
                });
                utils.debugLog('GPU pipeline error tracked:', error);
            }
            return error;
        };
    }
}

trackGPUValidationError() {
    if (typeof GPUValidationError !== 'undefined') {
        window.addEventListener('gpuvalidationerror', (event) => {
            const error = event.error;
            this.queueRequest({
                type: 'event',
                name: 'gpu_validation_error',
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
                        lastModified: document.lastModified,
                        // Add validation specific info
                        validation: {
                            errorSource: event.source,
                            lineno: event.lineno,
                            colno: event.colno
                        }
                    }
                }
            });
            utils.debugLog('GPU validation error tracked:', error);
        });
    }
}

trackGPUUncapturedError() {
    if (typeof GPUUncapturedErrorEvent !== 'undefined') {
        window.addEventListener('uncapturederror', (event) => {
            const error = event.error;
            this.queueRequest({
                type: 'event',
                name: 'gpu_uncaptured_error',
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
                        lastModified: document.lastModified,
                        // Add error specific info
                        error: {
                            source: event.source,
                            lineno: event.lineno,
                            colno: event.colno,
                            filename: event.filename
                        }
                    }
                }
            });
            utils.debugLog('GPU uncaptured error tracked:', error);
        });
    }
}

trackGPUErrors() {
    if (typeof WebGLRenderingContext !== 'undefined') {
        const originalGetError = WebGLRenderingContext.prototype.getError;
        WebGLRenderingContext.prototype.getError = function() {
            const error = originalGetError.apply(this, arguments);
            if (error !== this.NO_ERROR) {
                const errorName = this.getErrorName(error);
                this.queueRequest({
                    type: 'event',
                    name: 'gpu_error',
                    value: {
                        type: errorName,
                        code: error,
                        timestamp: new Date().toISOString(),
                        url: window.location.href,
                        userAgent: navigator.userAgent,
                        // Additional context
                        pageState: {
                            readyState: document.readyState,
                            documentMode: document.documentMode,
                            compatMode: document.compatMode,
                            lastModified: document.lastModified,
                            // Add WebGL state info
                            webgl: {
                                vendor: this.getParameter(this.VENDOR),
                                renderer: this.getParameter(this.RENDERER),
                                version: this.getParameter(this.VERSION),
                                extensions: this.getSupportedExtensions(),
                                maxTextureSize: this.getParameter(this.MAX_TEXTURE_SIZE),
                                maxViewportDims: this.getParameter(this.MAX_VIEWPORT_DIMS),
                                maxRenderbufferSize: this.getParameter(this.MAX_RENDERBUFFER_SIZE)
                            }
                        }
                    }
                });
                utils.debugLog('GPU error tracked:', errorName, error);
            }
            return error;
        };

        WebGLRenderingContext.prototype.getErrorName = function(error) {
            switch (error) {
                case this.INVALID_ENUM:
                    return 'InvalidEnum';
                case this.INVALID_VALUE:
                    return 'InvalidValue';
                case this.INVALID_OPERATION:
                    return 'InvalidOperation';
                case this.INVALID_FRAMEBUFFER_OPERATION:
                    return 'InvalidFramebufferOperation';
                case this.OUT_OF_MEMORY:
                    return 'OutOfMemory';
                case this.CONTEXT_LOST_WEBGL:
                    return 'ContextLostWebGL';
                default:
                    return 'UnknownError';
            }
        };
    }

    // Add WebGL2 context tracking
    if (typeof WebGL2RenderingContext !== 'undefined') {
        const originalWebGL2GetError = WebGL2RenderingContext.prototype.getError;
        WebGL2RenderingContext.prototype.getError = WebGLRenderingContext.prototype.getError;
        WebGL2RenderingContext.prototype.getErrorName = WebGLRenderingContext.prototype.getErrorName;
    }

    // Track context lost/restored events
    const canvas = document.createElement('canvas');
    const gl = canvas.getContext('webgl') || canvas.getContext('webgl2');
    if (gl) {
        canvas.addEventListener('webglcontextlost', (event) => {
            this.queueRequest({
                type: 'event',
                name: 'gpu_context_lost',
                value: {
                    timestamp: new Date().toISOString(),
                    reason: event.statusMessage || 'unknown'
                }
            });
        });

        canvas.addEventListener('webglcontextrestored', () => {
            this.queueRequest({
                type: 'event',
                name: 'gpu_context_restored',
                value: {
                    timestamp: new Date().toISOString()
                }
            });
        });
    }
}
