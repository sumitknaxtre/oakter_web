import './password-toggle.js';
import './meta-pixel.js';

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

initMobileMenu();
initRevealAnimations();
initCarousels();
