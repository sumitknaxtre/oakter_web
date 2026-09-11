import './password-toggle.js';
import './meta-pixel.js';
import './google-analytics.js';

function initMobileMenu() {
    const header = document.querySelector('.site-header');
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelectorAll('.primary-nav a');

    if (!header || !menuToggle) {
        return;
    }

    menuToggle.addEventListener('click', () => {
        const isOpen = header.classList.toggle('is-open');
        menuToggle.setAttribute('aria-expanded', String(isOpen));
        menuToggle.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
    });

    navLinks.forEach((link) => {
        link.addEventListener('click', () => {
            header.classList.remove('is-open');
            menuToggle.setAttribute('aria-expanded', 'false');
            menuToggle.setAttribute('aria-label', 'Open menu');
        });
    });
}

function initRevealAnimations() {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
        return;
    }

    const targets = document.querySelectorAll(
        [
            '.section',
            '.feature-grid article',
            '.pdp-section > *',
            '.product-band > *',
            '.cards > *',
            '.detail-columns > *',
            '.gan-details > *',
            '.section-heading',
        ].join(', '),
    );

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting || entry.boundingClientRect.top < 0) {
                    entry.target.classList.add('in-view');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -8% 0px' },
    );

    targets.forEach((target, index) => {
        target.classList.add('reveal');
        target.style.setProperty('--reveal-delay', `${Math.min(index % 4, 3) * 60}ms`);
        observer.observe(target);
    });
}

function initCarousels() {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const mobileQuery = window.matchMedia('(max-width: 620px)');

    document.querySelectorAll('.carousel-shell').forEach((shell) => {
        const track = shell.querySelector('[data-carousel]');
        const dotsContainer = shell.querySelector('.carousel-dots');
        const slides = track ? Array.from(track.children) : [];

        if (!track || !dotsContainer || slides.length < 2) {
            return;
        }

        let activeIndex = 0;
        let autoplayTimer;

        const dots = slides.map((_, index) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.setAttribute('aria-label', `Go to slide ${index + 1}`);
            dotsContainer.append(button);
            return button;
        });

        const updateDots = () => {
            dots.forEach((dot, index) => {
                dot.classList.toggle('is-active', index === activeIndex);
            });
        };

        const goToSlide = (index) => {
            activeIndex = (index + slides.length) % slides.length;
            track.scrollTo({
                left: slides[activeIndex].offsetLeft - track.offsetLeft,
                behavior: 'smooth',
            });
            updateDots();
        };

        const restartAutoplay = () => {
            if (prefersReducedMotion || !mobileQuery.matches) {
                return;
            }

            window.clearInterval(autoplayTimer);
            autoplayTimer = window.setInterval(() => goToSlide(activeIndex + 1), 3600);
        };

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                goToSlide(index);
                restartAutoplay();
            });
        });

        track.addEventListener('scroll', () => {
            const nearest = slides.reduce(
                (closest, slide, index) => {
                    const distance = Math.abs(track.scrollLeft - (slide.offsetLeft - track.offsetLeft));
                    return distance < closest.distance ? { index, distance } : closest;
                },
                { index: activeIndex, distance: Number.POSITIVE_INFINITY },
            );

            if (nearest.index !== activeIndex) {
                activeIndex = nearest.index;
                updateDots();
            }
        });

        updateDots();
        restartAutoplay();
    });
}

function initStoreFinder() {
    const page = document.querySelector('[data-retail-outlets-page]');
    const finder = document.querySelector('[data-store-finder]');
    const stateSelect = finder?.querySelector('[data-store-state]');
    const directory = document.querySelector('[data-outlet-directory]');
    const directoryTitle = directory?.querySelector('[data-outlet-title]');
    const directoryMessage = directory?.querySelector('[data-outlet-message]');
    const directoryList = directory?.querySelector('[data-outlet-list]');
    const dealersUrl = finder?.dataset.dealersUrl;

    if (!finder || !stateSelect || !directory || !directoryTitle || !directoryMessage || !directoryList || !dealersUrl) {
        return;
    }

    const setPageState = (state) => {
        if (!page) {
            return;
        }

        page.classList.toggle('is-searching', state === 'searching');
        page.classList.toggle('has-results', state === 'results');
    };

    const formatAddress = (address, storeName) => {
        let formattedAddress = address.replace(/\s+/g, ' ').trim();
        const escapedStoreName = storeName.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

        formattedAddress = formattedAddress.replace(
            new RegExp(`^${escapedStoreName}\\s*[,.-]*\\s*`, 'i'),
            '',
        );

        formattedAddress = formattedAddress.replace(
            /\s*(?:PIN|Pin|pin)(?:\s*Code)?\s*[:\-]?\s*/,
            '\nPIN ',
        );

        if (!/\nPIN\b/i.test(formattedAddress)) {
            formattedAddress = formattedAddress.replace(/(?:,\s*|\s+)(\d{6})\b/, '\nPIN $1');
        }

        return formattedAddress;
    };

    const renderDistricts = (state, districts) => {
        directoryTitle.textContent = `Stores in ${state}`;
        directoryList.replaceChildren();

        if (!districts.length) {
            directoryMessage.textContent = `No retail outlet addresses have been published for ${state} yet.`;
            setPageState('results');
            return;
        }

        const storeCount = districts.reduce((total, group) => total + (group.outlets?.length || 0), 0);
        directoryMessage.textContent = `${storeCount} outlet${storeCount === 1 ? '' : 's'} found in ${state}.`;

        districts.forEach((subRegion) => {
            const group = document.createElement('article');
            const heading = document.createElement('h3');
            const outletList = document.createElement('ul');

            group.className = 'outlet-subregion';
            heading.textContent = subRegion.name;

            (subRegion.outlets || []).forEach((outlet) => {
                const item = document.createElement('li');
                const name = document.createElement('strong');
                const address = document.createElement('span');

                name.textContent = outlet.name;
                address.className = 'outlet-address';
                address.textContent = formatAddress(outlet.address || '', outlet.name || '');
                item.append(name, address);

                if (outlet.phone) {
                    const phone = document.createElement('a');
                    phone.className = 'outlet-phone';
                    phone.href = `tel:+91${String(outlet.phone).replace(/\D/g, '')}`;
                    phone.textContent = `Phone: ${outlet.phone}`;
                    item.append(phone);
                }

                if (outlet.map_url) {
                    const map = document.createElement('a');
                    map.className = 'outlet-map';
                    map.href = outlet.map_url;
                    map.target = '_blank';
                    map.rel = 'noopener';
                    map.textContent = 'View on map';
                    item.append(map);
                }

                outletList.append(item);
            });

            group.append(heading, outletList);
            directoryList.append(group);
        });

        setPageState('results');
    };

    const resetDirectory = () => {
        directoryTitle.textContent = 'Choose a state to see stores.';
        directoryMessage.textContent = 'Store addresses will appear here as soon as you select a state or UT.';
        directoryList.replaceChildren();
        setPageState('idle');
    };

    const revealResults = () => {
        directory.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    };

    const showOutlets = async () => {
        const state = stateSelect.value;

        if (!state) {
            resetDirectory();
            return;
        }

        setPageState('searching');
        directoryTitle.textContent = `Stores in ${state}`;
        directoryMessage.textContent = 'Looking up retail outlets…';
        directoryList.replaceChildren();
        revealResults();

        try {
            const url = new URL(dealersUrl, window.location.origin);
            url.searchParams.set('state', state);

            const response = await fetch(url.toString(), {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Unable to load dealers.');
            }

            const payload = await response.json();
            renderDistricts(payload.state || state, payload.districts || []);
            revealResults();
        } catch {
            directoryMessage.textContent = 'Something went wrong while loading stores. Please try again.';
            directoryList.replaceChildren();
            setPageState('results');
        }
    };

    stateSelect.addEventListener('change', () => {
        void showOutlets();
    });
}

initMobileMenu();
initRevealAnimations();
initCarousels();
initStoreFinder();
