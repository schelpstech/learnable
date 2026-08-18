(function () {
    'use strict';

    function reportSheets() {
        return Array.prototype.slice.call(document.querySelectorAll('.report-sheet'));
    }

    function fitReportSheet(sheet) {
        if (!sheet) return;
        var content = sheet.querySelector('.report-sheet-content');
        if (!content) return;
        sheet.style.setProperty('--report-scale', '1');
        window.requestAnimationFrame(function () {
            var style = window.getComputedStyle(sheet);
            var available = sheet.clientHeight - parseFloat(style.paddingTop) - parseFloat(style.paddingBottom);
            var required = content.scrollHeight;
            var scale = required > available ? Math.max(0.68, available / required) : 1;
            sheet.style.setProperty('--report-scale', scale.toFixed(4));
            sheet.dataset.reportScale = scale.toFixed(4);
        });
    }

    function fitAllReports() {
        reportSheets().forEach(fitReportSheet);
    }

    function printReports() {
        fitAllReports();
        window.setTimeout(function () { window.print(); }, 100);
    }

    function targetSheet(button) {
        var target = button.getAttribute('data-report-target');
        return target ? document.getElementById(target) : reportSheets()[0];
    }

    function downloadReport(button) {
        var sheet = targetSheet(button);
        if (!sheet || typeof window.html2pdf !== 'function') {
            printReports();
            return;
        }
        fitReportSheet(sheet);
        var original = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Preparing PDF';
        document.body.classList.add('report-exporting');
        window.html2pdf().set({
            margin: 0,
            filename: sheet.getAttribute('data-report-file') || 'learner-report.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true, letterRendering: true, backgroundColor: '#fffdf8' },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait', compress: true },
            pagebreak: { mode: ['css'] }
        }).from(sheet).toPdf().get('pdf', function (pdf) {
            var pages = pdf.internal.getNumberOfPages();
            sheet.dataset.exportOriginalPages = String(pages);
            while (pages > 1) {
                pdf.deletePage(pages);
                pages -= 1;
            }
            sheet.dataset.exportPages = String(pdf.internal.getNumberOfPages());
        }).save().then(function () {
            sheet.dataset.exportStatus = 'complete';
            document.body.classList.remove('report-exporting');
            button.disabled = false;
            button.innerHTML = original;
        }).catch(function () {
            sheet.dataset.exportStatus = 'error';
            document.body.classList.remove('report-exporting');
            button.disabled = false;
            button.innerHTML = original;
            printReports();
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        fitAllReports();
        document.querySelectorAll('[data-report-print]').forEach(function (button) {
            button.addEventListener('click', printReports);
        });
        document.querySelectorAll('[data-report-download]').forEach(function (button) {
            button.addEventListener('click', function () { downloadReport(button); });
        });
    });
    window.addEventListener('resize', fitAllReports);
    window.addEventListener('beforeprint', fitAllReports);
}());
