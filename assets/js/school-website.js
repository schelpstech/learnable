(function () {
    'use strict';
    document.querySelectorAll('[data-school-gallery]').forEach(function (root) {
        var slides = Array.from(root.querySelectorAll('[data-gallery-slide]'));
        if (slides.length < 2) return;
        var track = root.querySelector('[data-gallery-track]');
        var play = root.querySelector('[data-gallery-play]');
        var count = root.querySelector('[data-gallery-count]');
        var live = root.querySelector('[data-gallery-live]');
        var motion = window.matchMedia('(prefers-reduced-motion: reduce)');
        var paused = motion.matches;
        var hovered = false;
        var focused = false;
        var current = 0;
        var timer = null;

        function schedule() {
            clearTimeout(timer);
            if (!paused && !hovered && !focused && !document.hidden) {
                timer = setTimeout(function () { show(current + 1, false); }, 10000);
            }
        }
        function show(index, announce) {
            current = (index + slides.length) % slides.length;
            track.style.transform = 'translateX(-' + (current * 100) + '%)';
            slides.forEach(function (slide, i) { slide.setAttribute('aria-hidden', i === current ? 'false' : 'true'); });
            count.textContent = (current + 1) + ' / ' + slides.length;
            if (announce) live.textContent = 'Photograph ' + (current + 1) + ' of ' + slides.length;
            play.textContent = paused ? 'Play slideshow' : 'Pause slideshow';
            play.setAttribute('aria-label', paused ? 'Play automatic slideshow' : 'Pause automatic slideshow');
            schedule();
        }
        root.classList.add('is-ready');
        root.querySelector('[data-gallery-controls]').hidden = false;
        root.querySelector('[data-gallery-previous]').addEventListener('click', function () { show(current - 1, true); });
        root.querySelector('[data-gallery-next]').addEventListener('click', function () { show(current + 1, true); });
        play.addEventListener('click', function () { paused = !paused; show(current, false); });
        root.addEventListener('mouseenter', function () { hovered = true; schedule(); });
        root.addEventListener('mouseleave', function () { hovered = false; schedule(); });
        root.addEventListener('focusin', function () { focused = true; schedule(); });
        root.addEventListener('focusout', function (event) { focused = root.contains(event.relatedTarget); schedule(); });
        document.addEventListener('visibilitychange', schedule);
        if (motion.addEventListener) motion.addEventListener('change', function (event) {
            if (event.matches) { paused = true; show(current, false); }
        });
        show(0, false);
    });
}());
