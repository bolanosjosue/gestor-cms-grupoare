/* ============================================================
   GRUPO ARE — Scroll Animation Engine
   IntersectionObserver + Split-Text
   ============================================================ */
(function () {
    'use strict';

    /* ---- Split Text ---- */
    document.querySelectorAll('.split-text').forEach(function (el) {
        var text = el.textContent.trim();
        var html = text.split(/\s+/).map(function (w) {
            return '<span class="word"><span class="word-inner">' + w + '</span></span>';
        }).join(' ');
        el.innerHTML = html;
    });

    /* ---- Observer ---- */
    var targets = document.querySelectorAll(
        '[data-animate], [data-stagger], .split-text, .hero-content, ' +
        '.about-timeline__item, .about-three__experience-box, .site-footer'
    );

    if (!('IntersectionObserver' in window)) {
        targets.forEach(function (el) { el.classList.add('is-visible'); });
        return;
    }

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.15,
        rootMargin: '0px 0px -40px 0px'
    });

    targets.forEach(function (el) { observer.observe(el); });
})();
