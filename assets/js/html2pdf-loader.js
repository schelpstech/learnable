(function () {
    'use strict';
    var root = document.documentElement;
    var script = document.currentScript;
    if (!script) return;
    root.dataset.pdfLoader = 'waiting';
    var bundleUrl = new URL('../../js/html2pdf.bundle.min.js', script.src).toString();

    function loadBundle() {
        if (root.dataset.pdfLoader === 'loading' || root.dataset.pdfLoader === 'ready') return;
        root.dataset.pdfLoader = 'loading';
        var bundle = document.createElement('script');
        bundle.src = bundleUrl;
        bundle.async = true;
        bundle.onload = function () {
            root.dataset.pdfLoader = typeof window.html2pdf === 'function' ? 'ready' : 'error';
        };
        bundle.onerror = function () {
            root.dataset.pdfLoader = 'error';
        };
        document.head.appendChild(bundle);
    }

    if (document.readyState === 'complete') {
        loadBundle();
    } else {
        window.addEventListener('load', loadBundle, { once: true });
    }
}());
