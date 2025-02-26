trackSearchInteractions() {
    // Track form submissions
    document.addEventListener('submit', this.handleSearchFormSubmit.bind(this));

    // Track search input interactions with debounce
    document.addEventListener('input', utils.debounce(this.handleSearchInput.bind(this), 500));

    // Track search result clicks
    document.addEventListener('click', this.handleSearchResultClick.bind(this));

    // Track search autocomplete interactions
    document.addEventListener('click', this.handleAutocompleteClick.bind(this));
}

handleSearchFormSubmit(event) {
    const form = event.target;
    if (!form || !form.tagName || form.tagName.toLowerCase() !== 'form') return;

    const searchInputs = form.querySelectorAll(`
        input[type="search"],
        input[name*="search"],
        input[id*="search"],
        input[placeholder*="search" i],
        input[aria-label*="search" i],
        input[role="searchbox"]
    `);

    searchInputs.forEach(input => {
        const searchData = {
            name: 'site_search',
            value: {
                query: input.value.trim(),
                formId: form.id || form.getAttribute('name') || 'unnamed_search',
                searchInputName: input.name || input.id || 'unnamed_input',
                timestamp: new Date().toISOString(),
                url: window.location.href,
                referrer: document.referrer,
                searchType: input.getAttribute('data-search-type') || 'site_search',
                searchCategory: input.getAttribute('data-search-category') || null,
                searchFilters: this.getSearchFilters(form) || {},
                searchResults: document.querySelector(input.getAttribute('data-results-container'))?.children.length || null,
                searchDuration: Date.now() - (this.lastSearchTime || Date.now()),
                searchSuccess: Boolean(document.querySelector(input.getAttribute('data-results-container'))?.children.length),
                searchLanguage: document.documentElement.lang || navigator.language,
                searchDevice: utils.getDeviceType(),
                searchLocation: utils.getUserTimeZone()
            }
        };

        // Get search filters
        const searchFilters = this.getSearchFilters(form);
        if (searchFilters) {
            searchData.value.searchFilters = searchFilters;
        }

        // Update engagement score for search interactions
        utils.updateEngagementScore(1);

        this.queueRequest(searchData);
        utils.debugInfo('Search interaction tracked:', searchData);

        // Store timestamp for duration calculation
        this.lastSearchTime = Date.now();
    });
}

handleSearchInput(event) {
    const input = event.target;
    if (!this.isSearchInput(input)) return;

    const searchData = {
        name: 'search_typing',
        value: {
            query: input.value.trim(),
            inputId: input.id || input.name || 'unnamed_input',
            timestamp: new Date().toISOString(),
            type: 'incremental',
            typingSpeed: this.calculateTypingSpeed(input),
            suggestionsShown: Boolean(document.querySelector(input.getAttribute('data-suggestions-container'))),
            inputLength: input.value.length,
            backspaceCount: this.backspaceCount || 0
        }
    };

    this.queueRequest(searchData);
    utils.debugInfo('Search typing tracked:', searchData);
}

handleSearchResultClick(event) {
    const searchResult = event.target.closest('[data-search-result]');
    if (!searchResult) return;

    const searchData = {
        name: 'search_result_click',
        value: {
            resultId: searchResult.getAttribute('data-search-result') || null,
            resultUrl: searchResult.href || null,
            query: searchResult.getAttribute('data-search-query') || null,
            position: Array.from(searchResult.parentNode.children).indexOf(searchResult) + 1,
            timestamp: new Date().toISOString(),
            resultType: searchResult.getAttribute('data-result-type') || 'default',
            resultCategory: searchResult.getAttribute('data-result-category'),
            timeSinceSearch: Date.now() - (this.lastSearchTime || Date.now()),
            resultScore: parseFloat(searchResult.getAttribute('data-result-score')) || null
        }
    };

    this.queueRequest(searchData);
    utils.debugInfo('Search result click tracked:', searchData);
}

handleAutocompleteClick(event) {
    const suggestion = event.target.closest('[data-search-suggestion]');
    if (!suggestion) return;

    const searchData = {
        name: 'search_autocomplete',
        value: {
            suggestion: suggestion.textContent.trim(),
            originalQuery: suggestion.getAttribute('data-original-query'),
            position: Array.from(suggestion.parentNode.children).indexOf(suggestion) + 1,
            timestamp: new Date().toISOString(),
            suggestionType: suggestion.getAttribute('data-suggestion-type') || 'default',
            interactionTime: Date.now() - (this.lastTypingTime || Date.now())
        }
    };

    this.queueRequest(searchData);
    utils.debugInfo('Search autocomplete interaction tracked:', searchData);
}

isSearchInput(input) {
    if (!input || !input.tagName || input.tagName.toLowerCase() !== 'input') return false;

    return (
        input.type === 'search' ||
        input.name?.toLowerCase().includes('search') ||
        input.id?.toLowerCase().includes('search') ||
        input.placeholder?.toLowerCase().includes('search') ||
        input.getAttribute('aria-label')?.toLowerCase().includes('search') ||
        input.getAttribute('role') === 'searchbox'
    );
}

calculateTypingSpeed(input) {
    const now = Date.now();
    const timeDiff = now - (this.lastTypingTime || now);
    this.lastTypingTime = now;
    return timeDiff > 0 ? Math.round(60000 / timeDiff) : 0; // Characters per minute
}
