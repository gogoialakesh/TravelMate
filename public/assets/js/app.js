/**
 * TravelMate — Main App JS
 *
 * Handles:
 *  - Flash alert auto-dismiss
 *  - Navbar scroll behaviour
 *  - Form loading states
 *  - End-date minimum from start-date
 *  - Particle canvas (hero)
 *  - Scroll-reveal animations
 *  - Animated stat counters
 */

'use strict';

/* ============================================================
   1. Auto-dismiss flash alerts after 5 seconds
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {
    const flashAlerts = document.querySelectorAll('#flash-container .alert');
    flashAlerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

/* ============================================================
   2. Navbar background on scroll
   ============================================================ */
const navbar = document.querySelector('.tm-navbar');
if (navbar) {
    const setNavbarBg = () => {
        if (window.scrollY > 50) {
            navbar.style.background = 'rgba(15, 23, 42, 0.98)';
        } else {
            navbar.style.background = 'rgba(15, 23, 42, 0.92)';
        }
    };
    window.addEventListener('scroll', setNavbarBg, { passive: true });
}

/* ============================================================
   3. Form submit — loading state on submit buttons
   ============================================================ */
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function () {
        const btn = this.querySelector('[type="submit"]');
        if (btn && !btn.dataset.noLoad) {
            const originalText = btn.innerHTML;
            setTimeout(() => {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Please wait...';
            }, 0);
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }, 10000);
        }
    });
});

/* ============================================================
   4. Trip date: enforce end_date >= start_date
   ============================================================ */
const startDateInput = document.getElementById('start_date');
const endDateInput   = document.getElementById('end_date');

if (startDateInput && endDateInput) {
    startDateInput.addEventListener('change', () => {
        if (endDateInput.value && endDateInput.value < startDateInput.value) {
            endDateInput.value = startDateInput.value;
        }
        endDateInput.min = startDateInput.value;
    });
}

/* ============================================================
   5. Auto-resize textarea in chat
   ============================================================ */
const autoResizeTextarea = (el) => {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
};

document.querySelectorAll('textarea.tm-chat-input').forEach(ta => {
    ta.addEventListener('input', () => autoResizeTextarea(ta));
});

/* ============================================================
   6. Number-only input enforcement
   ============================================================ */
document.querySelectorAll('input[type="number"]').forEach(input => {
    input.addEventListener('wheel', e => e.preventDefault(), { passive: false });
});

/* ============================================================
   7. Hero Particle Canvas
   ============================================================ */
(function initParticles() {
    const canvas = document.getElementById('tm-particles');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    let W, H;
    const particles = [];

    const resize = () => {
        W = canvas.width  = canvas.offsetWidth;
        H = canvas.height = canvas.offsetHeight;
    };
    resize();
    window.addEventListener('resize', resize, { passive: true });

    const NUM = 55;
    const colors = ['#38bdf8','#818cf8','#34d399','#60a5fa','#a5b4fc'];

    for (let i = 0; i < NUM; i++) {
        particles.push({
            x:     Math.random() * 2000,
            y:     Math.random() * 1000,
            r:     Math.random() * 1.8 + 0.4,
            dx:    (Math.random() - 0.5) * 0.35,
            dy:    (Math.random() - 0.5) * 0.35,
            alpha: Math.random() * 0.5 + 0.15,
            color: colors[Math.floor(Math.random() * colors.length)],
        });
    }

    function draw() {
        ctx.clearRect(0, 0, W, H);

        particles.forEach((p, i) => {
            p.x += p.dx;
            p.y += p.dy;
            if (p.x < 0) p.x = W;
            if (p.x > W) p.x = 0;
            if (p.y < 0) p.y = H;
            if (p.y > H) p.y = 0;

            // Draw dot
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = p.color;
            ctx.globalAlpha = p.alpha;
            ctx.fill();

            // Draw connections
            for (let j = i + 1; j < particles.length; j++) {
                const q = particles[j];
                const dist = Math.hypot(p.x - q.x, p.y - q.y);
                if (dist < 110) {
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(q.x, q.y);
                    ctx.strokeStyle = '#38bdf8';
                    ctx.globalAlpha = (1 - dist / 110) * 0.12;
                    ctx.lineWidth = 0.6;
                    ctx.stroke();
                }
            }
            ctx.globalAlpha = 1;
        });

        requestAnimationFrame(draw);
    }
    draw();
})();

/* ============================================================
   8. Scroll Reveal — IntersectionObserver
   ============================================================ */
(function initScrollReveal() {
    const els = document.querySelectorAll('.tm-reveal');
    if (!els.length) return;

    const obs = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('is-visible');
                obs.unobserve(e.target);
            }
        });
    }, { threshold: 0.12 });

    els.forEach(el => obs.observe(el));
})();

/* ============================================================
   9. Animated stat counters (data-count attribute)
   ============================================================ */
(function initCounters() {
    const els = document.querySelectorAll('[data-count]');
    if (!els.length) return;

    const easeOut = t => 1 - Math.pow(1 - t, 3);

    const obs = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (!e.isIntersecting) return;
            const el = e.target;
            const target = parseInt(el.dataset.count, 10);
            const duration = 1800;
            const start = performance.now();

            const tick = (now) => {
                const progress = Math.min((now - start) / duration, 1);
                const val = Math.round(easeOut(progress) * target);
                el.textContent = val >= 1000
                    ? (val / 1000).toFixed(1) + 'k+'
                    : val + '+';
                if (progress < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
            obs.unobserve(el);
        });
    }, { threshold: 0.5 });

    els.forEach(el => obs.observe(el));
})();
