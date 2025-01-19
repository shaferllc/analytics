trackFormInteractions() {
    // Track all forms on the page
    document.querySelectorAll('form').forEach(form => {
        let startTime = Date.now();
        let fieldsFilled = 0;
        let totalFields = form.elements.length;
        let formErrors = [];
        let abandonmentTime = null;
        let lastInteractionTime = startTime;
        let formData = {};
        let hesitationTime = 0;
        let backspaceCount = 0;

        // Track initial form state
        this.queueRequest({
            type: 'event',
            name: 'form_init',
            value: {
                formId: form.id || 'unnamed_form',
                formAction: form.action,
                totalFields,
                formMethod: form.method,
                hasFileUpload: form.enctype === 'multipart/form-data',
                formSize: new Blob([form.innerHTML]).size
            }
        });

        // Track field input
        form.addEventListener('input', (event) => {
            const currentTime = Date.now();
            const timeSinceLastInteraction = currentTime - lastInteractionTime;

            if (timeSinceLastInteraction > 2000) {
                hesitationTime += timeSinceLastInteraction;
            }

            lastInteractionTime = currentTime;
            fieldsFilled = Array.from(form.elements)
                .filter(element => element.value.length > 0).length;

            // Track typing speed and patterns
            if (event.target.type === 'text' || event.target.tagName === 'TEXTAREA') {
                formData[event.target.name] = {
                    ...formData[event.target.name],
                    typingSpeed: event.target.value.length / ((currentTime - startTime) / 1000),
                    valueLength: event.target.value.length
                };

                this.queueRequest({
                    type: 'event',
                    name: 'form_typing_speed',
                    value: {
                        fieldName: event.target.name || 'unnamed_field',
                        typingSpeed: formData[event.target.name].typingSpeed,
                        hesitationTime
                    }
                });
            }
        });

        // Track keydown for additional metrics
        form.addEventListener('keydown', (event) => {
            if (event.key === 'Backspace') {
                backspaceCount++;
            }
        });

        // Track form submission
        form.addEventListener('submit', () => {
            const duration = Date.now() - startTime;
            const completionRate = fieldsFilled / totalFields;
            const timePerField = duration / fieldsFilled;

            this.queueRequest({
                type: 'event',
                name: 'form_submission',
                value: {
                    formId: form.id || 'unnamed_form',
                    formAction: form.action,
                    duration,
                    completionRate,
                    fieldsFilled,
                    totalFields,
                    timePerField,
                    errors: formErrors,
                    formSize: new Blob([form.innerHTML]).size,
                    hesitationTime,
                    backspaceCount,
                    formData
                }
            });
        });

        // Track field focus with improved metrics
        form.addEventListener('focus', (event) => {
            if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA' || event.target.tagName === 'SELECT') {
                const fieldStartTime = Date.now();
                formData[event.target.name] = {
                    ...formData[event.target.name],
                    focusCount: (formData[event.target.name]?.focusCount || 0) + 1,
                    lastFocusTime: fieldStartTime
                };

                this.queueRequest({
                    type: 'event',
                    name: 'form_field_focus',
                    value: {
                        fieldName: event.target.name || 'unnamed_field',
                        fieldType: event.target.type,
                        timeToFocus: fieldStartTime - lastInteractionTime,
                        focusCount: formData[event.target.name].focusCount
                    }
                });
            }
        }, true);

        // Track field blur with enhanced data
        form.addEventListener('blur', (event) => {
            if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA' || event.target.tagName === 'SELECT') {
                const timeSpent = Date.now() - (formData[event.target.name]?.lastFocusTime || lastInteractionTime);

                formData[event.target.name] = {
                    ...formData[event.target.name],
                    totalTimeSpent: (formData[event.target.name]?.totalTimeSpent || 0) + timeSpent,
                    finalValue: event.target.value
                };

                this.queueRequest({
                    type: 'event',
                    name: 'form_field_blur',
                    value: {
                        fieldName: event.target.name || 'unnamed_field',
                        timeSpent,
                        totalTimeSpent: formData[event.target.name].totalTimeSpent,
                        valueLength: event.target.value.length,
                        isValid: event.target.checkValidity()
                    }
                });
            }
        }, true);

        // Track validation errors with more context
        form.addEventListener('invalid', (event) => {
            const errorTime = Date.now();
            const error = {
                fieldName: event.target.name || 'unnamed_field',
                errorMessage: event.target.validationMessage,
                errorType: event.target.validity,
                time: errorTime - startTime,
                fieldValue: event.target.value
            };

            formErrors.push(error);

            this.queueRequest({
                type: 'event',
                name: 'form_validation_error',
                value: error
            });
        }, true);

        // Track form abandonment with enhanced metrics
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden' && fieldsFilled > 0 && fieldsFilled < totalFields) {
                abandonmentTime = Date.now();
                this.queueRequest({
                    type: 'event',
                    name: 'form_abandonment',
                    value: {
                        formId: form.id || 'unnamed_form',
                        timeSpent: abandonmentTime - startTime,
                        fieldsCompleted: fieldsFilled,
                        totalFields,
                        lastFieldInteracted: lastInteractionTime - startTime,
                        completionPercentage: (fieldsFilled / totalFields) * 100,
                        formData,
                        hesitationTime,
                        backspaceCount
                    }
                });
            }
        });
    });
}
