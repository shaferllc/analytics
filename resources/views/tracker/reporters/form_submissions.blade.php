trackFormSubmissions() {
    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!form || !form.tagName || form.tagName.toLowerCase() !== 'form') return;

        // Get form metadata
        const formMetadata = {
            formId: form.id || 'unnamed_form',
            formAction: form.action || 'no_action',
            formMethod: form.method || 'unknown',
            formEnctype: form.enctype || 'application/x-www-form-urlencoded',
            formTarget: form.target || '_self',
            formNoValidate: form.noValidate || false,
            formClass: form.className || '',
            formName: form.name || '',
            formAcceptCharset: form.acceptCharset || ''
        };

        // Get field details
        const formFields = Array.from(form.elements)
            .filter(el => el.name)
            .map(el => ({
                type: el.type,
                name: el.name,
                id: el.id || '',
                required: el.required || false,
                disabled: el.disabled || false,
                readOnly: el.readOnly || false,
                value: el.type === 'password' ? '[REDACTED]' : el.value || '',
                checked: el.checked || false,
                validationMessage: el.validationMessage || '',
                validity: el.validity ? {
                    valid: el.validity.valid,
                    valueMissing: el.validity.valueMissing,
                    typeMismatch: el.validity.typeMismatch,
                    patternMismatch: el.validity.patternMismatch,
                    tooLong: el.validity.tooLong,
                    tooShort: el.validity.tooShort,
                    rangeUnderflow: el.validity.rangeUnderflow,
                    rangeOverflow: el.validity.rangeOverflow,
                    stepMismatch: el.validity.stepMismatch,
                    badInput: el.validity.badInput,
                    customError: el.validity.customError
                } : null
            }));

        const formData = {
            name: 'form_submission',
            type: 'event',
            value: {
                ...formMetadata,
                formFields,
                timestamp: new Date().toISOString(),
                url: window.location.href,
                referrer: document.referrer,
                userAgent: navigator.userAgent,
                screenResolution: {
                    width: window.screen.width,
                    height: window.screen.height
                },
                viewport: {
                    width: window.innerWidth,
                    height: window.innerHeight
                },
                formValidation: {
                    isValid: form.checkValidity(),
                    invalidFields: Array.from(form.elements)
                        .filter(el => !el.validity.valid)
                        .map(el => el.name)
                }
            }
        };

        this.queueRequest(formData);
        utils.debugInfo('Form submission tracked:', formData);

        // Track form submission timing
        const submissionTiming = {
            name: 'form_submission_timing',
            type: 'event',
            value: {
                formId: formMetadata.formId,
                timing: performance.now(),
                navigationStart: performance.timing.navigationStart
            }
        };
        this.queueRequest(submissionTiming);
    });

    // Track form abandonment
    document.addEventListener('change', (event) => {
        const form = event.target.form;
        if (!form) return;

        const abandonmentCheck = () => {
            if (document.visibilityState === 'hidden') {
                this.queueRequest({
                    name: 'form_abandonment',
                    type: 'event',
                    value: {
                        formId: form.id || 'unnamed_form',
                        lastModifiedField: event.target.name,
                        filledFields: Array.from(form.elements)
                            .filter(el => el.value && el.name)
                            .map(el => el.name)
                    }
                });
            }
        };

        document.addEventListener('visibilitychange', abandonmentCheck, { once: true });
    });
}
