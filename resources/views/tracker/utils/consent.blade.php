checkConsent() {
    const consentTimestamp = w.localStorage.getItem(internalConfig.consentCookieName);
    if (consentTimestamp === 'denied') {
        this.isTrackingAllowed = false;
    } else if (consentTimestamp && (Date.now() - parseInt(consentTimestamp, 10) < internalConfig.consentDuration)) {
        this.isTrackingAllowed = true;
    } else {
        this.createConsentOverlay();
    }

    this.manageConsentPreferences();
}

createConsentOverlay() {
    if (document.getElementById('consent-overlay')) return;

    const overlay = document.createElement('div');
    overlay.id = 'consent-overlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    `;

    const consentBox = document.createElement('div');
    consentBox.style.cssText = `
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        text-align: center;
    `;

    const message = document.createElement('p');
    message.textContent = 'Do you consent to being tracked for analytics purposes?';
    message.style.cssText = `
        font-size: 18px;
        color: #333;
        margin-bottom: 20px;
        line-height: 1.5;
    `;

    const buttonStyle = `
        padding: 10px 20px;
        margin: 0 10px;
        font-size: 16px;
        cursor: pointer;
        border: none;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    `;

    const createButton = (text, color, hoverColor, onClick) => {
        const button = document.createElement('button');
        button.textContent = text;
        button.style.cssText = `${buttonStyle} background-color: ${color}; color: white;`;
        button.onmouseover = () => button.style.backgroundColor = hoverColor;
        button.onmouseout = () => button.style.backgroundColor = color;
        button.onclick = onClick;
        return button;
    };

    const yesButton = createButton('Yes', '#4CAF50', '#45a049', () => {
        this.isTrackingAllowed = true;
        w.localStorage.setItem(internalConfig.consentCookieName, Date.now().toString());
        document.body.removeChild(overlay);
        this.initializeAnalytics();
    });

    const noButton = createButton('No', '#f44336', '#da190b', () => {
        this.isTrackingAllowed = false;
        w.localStorage.setItem(internalConfig.consentCookieName, 'denied');
        document.body.removeChild(overlay);
    });

    consentBox.append(message, yesButton, noButton);
    overlay.appendChild(consentBox);
    document.body.appendChild(overlay);
}

manageConsentPreferences() {
    const consentButton = document.createElement('button');
    consentButton.textContent = 'Manage Consent';
    consentButton.style.cssText = 'position:fixed;bottom:10px;right:10px;z-index:10000;background-color:#007BFF;color:white;padding:10px;border:none;border-radius:5px;cursor:pointer;';
    consentButton.onclick = () => this.createConsentOverlay();
    document.body.appendChild(consentButton);
}


