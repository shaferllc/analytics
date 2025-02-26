trackOutboundLinks() {
    document.addEventListener('click', (event) => {
        const link = event.target.closest('a');
        if (!link || !link.href || link.href.startsWith('mailto:')) return;

        try {
            const linkUrl = new URL(link.href);
            if (linkUrl.hostname !== window.location.hostname) {
                const surroundingText = link.closest('p, div, section')?.textContent?.trim()?.slice(0, 100) || '';
                const parentElement = link.parentElement?.tagName || '';
                const childElements = Array.from(link.children).map(child => child.tagName).join(', ');

                this.queueRequest({
                    type: 'event',
                    name: 'outbound_link_click',
                    value: {
                        url: link.href,
                        text: link.textContent?.trim() || '',
                        hostname: linkUrl.hostname,
                        protocol: linkUrl.protocol,
                        pathname: linkUrl.pathname,
                        search: linkUrl.search,
                        hash: linkUrl.hash,
                        port: linkUrl.port || '',
                        target: link.target || '_self',
                        rel: link.rel || '',
                        title: link.title || '',
                        classList: Array.from(link.classList).join(' '),
                        timestamp: new Date().toISOString(),
                        pageTitle: document.title,
                        pageUrl: window.location.href,
                        surroundingText: surroundingText,
                        parentElement: parentElement,
                        childElements: childElements
                    }
                });
            }
        } catch (e) {
            utils.debugError('Invalid URL in outbound link tracking:', e);
            return;
        }
    });
}
