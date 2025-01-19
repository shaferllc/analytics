trackScrollDepth() {
    let maxScroll = 0;
    let lastScrollTime = Date.now();
    let scrollStartTime = Date.now();
    let scrollCount = 0;
    let scrollDirection = null;
    let lastScrollY = window.scrollY;
    let lastScrollPosition = window.scrollY;
    let lastScrollTimestamp = Date.now();
    const sectionTimes = new Map();

    const handleScroll = utils.throttle(() => {
        const currentTime = Date.now();
        const currentScrollY = window.scrollY;
        const currentPosition = window.scrollY;
        const scrollPercentage = Math.round((currentScrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100);

        // Determine scroll direction
        scrollDirection = currentScrollY > lastScrollY ? 'down' : 'up';
        lastScrollY = currentScrollY;

        // Track scroll milestones
        if (scrollPercentage > maxScroll) {
            maxScroll = scrollPercentage;
            const milestones = [25, 50, 75, 90, 100];
            const milestone = milestones.find(m => maxScroll >= m && (maxScroll - scrollPercentage) < m);

            // Track scroll speed
            const distance = Math.abs(currentPosition - lastScrollPosition);
            const time = currentTime - lastScrollTimestamp;
            const speed = distance / time; // pixels per millisecond

            this.queueRequest({
                name: 'scroll_event',
                value: {
                    depth: maxScroll,
                    direction: scrollDirection,
                    scrollCount: ++scrollCount,
                    timeSinceLastScroll: currentTime - lastScrollTime,
                    totalScrollTime: currentTime - scrollStartTime,
                    viewportHeight: window.innerHeight,
                    documentHeight: document.documentElement.scrollHeight,
                    timestamp: new Date().toISOString(),
                    milestone: milestone || null,
                    timeToReachMilestone: milestone ? currentTime - scrollStartTime : null,
                    speed: speed > 0 ? speed : null,
                    distance: speed > 0 ? distance : null,
                    time: speed > 0 ? time : null
                }
            });
        }

        lastScrollTime = currentTime;
        lastScrollPosition = currentPosition;
        lastScrollTimestamp = currentTime;
    }, 1000);

    window.addEventListener('scroll', handleScroll);

    // Track time spent on each section of the page
    const sections = document.querySelectorAll('section');

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                sectionTimes.set(entry.target, Date.now());
            } else {
                const startTime = sectionTimes.get(entry.target);
                const endTime = Date.now();
                const timeSpent = endTime - startTime;

                this.queueRequest({
                    name: 'scroll_event',
                    value: {
                        section: entry.target.id || entry.target.className,
                        timeSpent
                    }
                });

                sectionTimes.delete(entry.target);
            }
        });
    }, {
        threshold: 0.5
    });

    sections.forEach(section => {
        observer.observe(section);
    });
}
