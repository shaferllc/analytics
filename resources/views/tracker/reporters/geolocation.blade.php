trackGeolocationErrors() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                // Track successful geolocation
                this.queueRequest({
                    type: 'event',
                    name: 'geolocation_success',
                    value: {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy: position.coords.accuracy,
                        timestamp: position.timestamp,
                        url: window.location.href,
                        userAgent: navigator.userAgent
                    }
                });
                utils.debugLog('Geolocation success tracked:', position);
            },
            (error) => {
                let errorName;
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorName = "PermissionDeniedError";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorName = "PositionUnavailableError";
                        break;
                    case error.TIMEOUT:
                        errorName = "TimeoutError";
                        break;
                    default:
                        errorName = "UnknownError";
                        break;
                }

                this.queueRequest({
                    type: 'event',
                    name: 'geolocation_error',
                    value: {
                        type: errorName,
                        message: error.message,
                        code: error.code,
                        timestamp: new Date().toISOString(),
                        url: window.location.href,
                        userAgent: navigator.userAgent
                    }
                });
                utils.debugError('Geolocation error tracked:', error);
            }
        );
    }
}
