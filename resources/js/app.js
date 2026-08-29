import './swal.js';

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
