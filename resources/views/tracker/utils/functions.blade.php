

getCsrfToken() {
    return document.querySelector(`meta[name="${config.csrfTokenName}"]`)?.getAttribute('content');
}