import './swal.js';

/* ---------- Global JavaScript Libraries ---------- */
import $ from './jquery-global.js';

import 'jquery-ui-dist/jquery-ui.js';
import 'jquery-ui-dist/jquery-ui.min.css';

import 'select2';
import 'select2/dist/css/select2.min.css';

import 'slick-carousel';
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';

import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';
window.Swiper = Swiper;

import Odometer from 'odometer';
import 'odometer/themes/odometer-theme-default.css';
window.Odometer = Odometer;

import moment from 'moment';
window.moment = moment;

import Masonry from 'masonry-layout';
window.Masonry = Masonry;

/* ---------- Livewire progress bar ---------- */


let progressBar;

function showProgress() {
    if (! progressBar) {
        progressBar = document.createElement('div');
        progressBar.id = 'livewire-progress';
        progressBar.style.opacity = '0';
        document.body.appendChild(progressBar);
    }

    progressBar.style.opacity = '1';
}

function hideProgress() {
    if (progressBar) {
        progressBar.style.opacity = '0';
    }
}

document.addEventListener('livewire:init', () => {
    Livewire.hook('request', ({ respond }) => {
        let finished = false;

        const timer = setTimeout(() => {
            if (! finished) {
                showProgress();
            }
        }, 150);

        respond(() => {
            finished = true;
            clearTimeout(timer);
            hideProgress();

            document.querySelectorAll('[data-loading]').forEach((el) => {
                el.removeAttribute('data-loading');
            });
        });
    });
});

/* ---------- Submit button spinners ---------- */

document.addEventListener(
    'submit',
    (event) => {
        const form = event.target.closest('form');
        if (! form) {
            return;
        }

        const button = form.querySelector('button[type="submit"]');
        if (button) {
            button.setAttribute('data-loading', '');
        }
    },
    true,
);

/* ---------- Scroll reveal ---------- */

const revealObserver = new IntersectionObserver(
    (entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-revealed');
                revealObserver.unobserve(entry.target);
            }
        });
    },
    { threshold: 0.12 },
);

function observeReveals() {
    document.querySelectorAll('[data-reveal]:not(.is-revealed)').forEach((el) => {
        revealObserver.observe(el);
    });
}

observeReveals();
document.addEventListener('livewire:navigated', observeReveals);

/* ---------- Animated counters ---------- */

function animateCounters() {
    document.querySelectorAll('[data-count]').forEach((el) => {
        if (el.dataset.counted) {
            return;
        }

        el.dataset.counted = 'true';

        const target = parseFloat(el.dataset.count);
        if (isNaN(target)) {
            return;
        }
        const decimals = (el.dataset.count.split('.')[1] || '').length;
        const prefix = el.dataset.prefix || '';
        const duration = 1200;
        const start = performance.now();

        const tick = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const value = target * eased;

            el.textContent = prefix + value.toFixed(decimals);

            if (progress < 1) {
                requestAnimationFrame(tick);
            } else {
                el.textContent = prefix + target.toFixed(decimals);
            }
        };

        requestAnimationFrame(tick);
    });
}

const counterObserver = new IntersectionObserver(
    (entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                animateCounters();
                counterObserver.unobserve(entry.target);
            }
        });
    },
    { threshold: 0.3 },
);

document.querySelectorAll('[data-count]').forEach((el) => counterObserver.observe(el));
document.addEventListener('livewire:navigated', () => {
    document.querySelectorAll('[data-count]').forEach((el) => counterObserver.observe(el));
});

/* ---------- Copy to clipboard ---------- */

document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-copy]');
    if (! trigger) {
        return;
    }

    const text = trigger.dataset.copy;

    navigator.clipboard.writeText(text).then(() => {
        const original = trigger.dataset.title || '';

        trigger.dataset.title = trigger.dataset.copied || '✓';
        trigger.classList.add('text-emerald-600');

        setTimeout(() => {
            trigger.dataset.title = original;
            trigger.classList.remove('text-emerald-600');
        }, 1500);
    });
});

/* ---------- Lightbox ---------- */

document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-lightbox]');
    if (! trigger) {
        return;
    }

    const src = trigger.dataset.lightbox;

    const backdrop = document.createElement('div');
    backdrop.className = 'lightbox-backdrop animate-fade-in';

    const img = document.createElement('img');
    img.src = src;
    img.className = 'lightbox-image animate-scale-in';

    backdrop.appendChild(img);
    document.body.appendChild(backdrop);

    const close = () => backdrop.remove();

    backdrop.addEventListener('click', close);
    document.addEventListener('keydown', function onKey(event) {
        if (event.key === 'Escape') {
            close();
            document.removeEventListener('keydown', onKey);
        }
    });
});

/* ---------- Animated bar charts ---------- */

const barObserver = new IntersectionObserver(
    (entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const el = entry.target;
                el.style.width = `${el.dataset.bar}%`;
                barObserver.unobserve(el);
            }
        });
    },
    { threshold: 0.2 },
);

function observeBars() {
    document.querySelectorAll('[data-bar]:not(.is-done)').forEach((el) => {
        el.classList.add('is-done');
        barObserver.observe(el);
    });
}

observeBars();
document.addEventListener('livewire:navigated', observeBars);

/* ---------- Keyboard shortcut: focus search ---------- */

document.addEventListener('keydown', (event) => {
    if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
        const search = document.querySelector('input[data-shortcut-search]');
        if (search) {
            event.preventDefault();
            search.focus();
        }
    }
});

/* ---------- Print layout handlers ---------- */

document.addEventListener('click', (event) => {
    if (event.target.closest('[data-print-btn]')) {
        window.print();
    }
});

function handleAutoPrint() {
    if (document.body && document.body.dataset.autoprint === 'true') {
        setTimeout(() => window.print(), 350);
    }
}

document.addEventListener('DOMContentLoaded', handleAutoPrint);
document.addEventListener('livewire:navigated', handleAutoPrint);

/* ---------- Dynamic Theme Color Application (CSP compliant via CSSOM) ---------- */

function applyThemeColors() {
    const meta = document.querySelector('meta[name="theme-color-custom"]');
    if (! meta) {
        return;
    }

    const cssContent = meta.getAttribute('content') || '';
    const declarations = cssContent.split(';').map((s) => s.trim()).filter(Boolean);

    declarations.forEach((decl) => {
        const parts = decl.split(':');
        if (parts.length >= 2) {
            const prop = parts[0].trim();
            const val = parts.slice(1).join(':').trim();
            if (prop && val) {
                document.documentElement.style.setProperty(prop, val);
            }
        }
    });
}

applyThemeColors();
document.addEventListener('DOMContentLoaded', applyThemeColors);
document.addEventListener('livewire:navigated', applyThemeColors);

/* ---------- Swiper Carousel (Testimonials) ---------- */

function initSwiper() {
    const swiperEl = document.querySelector('.testimonials-swiper');
    if (!swiperEl || !window.Swiper) {
        return;
    }

    if (swiperEl.swiper) {
        swiperEl.swiper.destroy(true, true);
    }

    new window.Swiper('.testimonials-swiper', {
        slidesPerView: 1,
        spaceBetween: 24,
        loop: true,
        grabCursor: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next-custom',
            prevEl: '.swiper-button-prev-custom',
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
                spaceBetween: 24,
            },
            1024: {
                slidesPerView: 3,
                spaceBetween: 28,
            },
        },
    });
}

/* ---------- Odometer Counters ---------- */

function initOdometers() {
    if (!window.Odometer) {
        return;
    }

    const odometers = document.querySelectorAll('.odometer-counter:not([data-odometer-initialized])');
    if (!odometers.length) {
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const el = entry.target;
                el.setAttribute('data-odometer-initialized', 'true');
                const targetValue = parseInt(el.dataset.odometerValue, 10);

                const od = new window.Odometer({
                    el: el,
                    value: 0,
                    format: '(,ddd)',
                    theme: 'default',
                    duration: 1800,
                });

                setTimeout(() => {
                    od.update(targetValue);
                }, 200);

                observer.unobserve(el);
            }
        });
    }, { threshold: 0.25 });

    odometers.forEach((el) => observer.observe(el));
}

document.addEventListener('DOMContentLoaded', () => {
    initSwiper();
    initOdometers();
});

document.addEventListener('livewire:navigated', () => {
    initSwiper();
    initOdometers();
});

