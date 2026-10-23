(function () {
    'use strict';
    var selector = document.querySelector('[data-midterm-selector]');
    if (selector) {
        var term = selector.querySelector('[data-midterm-term]');
        var classField = selector.querySelector('[data-midterm-class]');
        var learner = selector.querySelector('[data-midterm-learner]');
        term.addEventListener('change', function () { classField.value = ''; learner.value = ''; selector.submit(); });
        classField.addEventListener('change', function () { learner.value = ''; selector.submit(); });
    }
    var reports = document.querySelector('[data-midterm-reports]');
    if (!reports) return;
    var status = document.querySelector('[data-midterm-status]');
    var sheets = Array.from(reports.querySelectorAll('[data-midterm-sheet]'));
    var exporting = false;

    function message(text) { if (status) status.textContent = text; }
    function fitSheets() {
        sheets.forEach(function (sheet) {
            var content = sheet.querySelector('.midterm-content');
            sheet.style.setProperty('--midterm-scale', '1');
            var style = window.getComputedStyle(sheet);
            var available = sheet.clientHeight - parseFloat(style.paddingTop) - parseFloat(style.paddingBottom);
            var required = content.scrollHeight;
            var scale = Math.min(1, available / required);
            if (scale < 0.65) throw new Error('A report contains unusually long content. Review it before exporting.');
            sheet.style.setProperty('--midterm-scale', scale.toFixed(5));
            sheet.dataset.midtermScale = scale.toFixed(5);
        });
    }
    function imagesReady() {
        return Promise.all(Array.from(reports.querySelectorAll('img')).map(function (image) {
            if (image.complete) return image.naturalWidth ? Promise.resolve() : Promise.reject(new Error('A report image could not be loaded. Refresh the page before exporting.'));
            return new Promise(function (resolve, reject) {
                image.addEventListener('load', resolve, {once: true});
                image.addEventListener('error', function () { reject(new Error('A report image could not be loaded. Refresh the page before exporting.')); }, {once: true});
            });
        }));
    }
    function libraryReady() {
        return new Promise(function (resolve, reject) {
            var started = Date.now();
            function check() {
                if (typeof window.html2pdf === 'function') return resolve();
                if (document.documentElement.dataset.pdfLoader === 'error' || Date.now() - started > 15000) return reject(new Error('The PDF library is unavailable. Use Print / Save PDF instead.'));
                window.setTimeout(check, 100);
            }
            check();
        });
    }
    async function printReports() {
        try { await imagesReady(); fitSheets(); window.print(); }
        catch (error) { message(error.message); }
    }
    async function downloadReports() {
        if (exporting) return;
        exporting = true;
        var buttons = Array.from(document.querySelectorAll('[data-midterm-download], [data-midterm-print]'));
        buttons.forEach(function (button) { button.disabled = true; });
        reports.dataset.exportStatus = 'preparing';
        try {
            await imagesReady(); await libraryReady(); fitSheets();
            var options = {margin: 0, image: {type: 'jpeg', quality: 0.98}, html2canvas: {scale: 2, useCORS: true, backgroundColor: '#ffffff', logging: false}, jsPDF: {unit: 'mm', format: 'a4', orientation: 'landscape', compress: true}, pagebreak: {mode: []}};
            var pdf = null;
            for (var index = 0; index < sheets.length; index++) {
                message('Preparing sheet ' + (index + 1) + ' of ' + sheets.length + '…');
                var canvas = await window.html2pdf().set(options).from(sheets[index]).toCanvas().get('canvas');
                // Normalize the entire rendered sheet to one exact A4 ratio. Nothing
                // is cropped or removed, and class exports avoid one giant canvas.
                var page = document.createElement('canvas');
                page.width = canvas.width;
                page.height = Math.floor(canvas.width * 210 / 297);
                var context = page.getContext('2d');
                context.fillStyle = '#ffffff'; context.fillRect(0, 0, page.width, page.height);
                context.drawImage(canvas, 0, 0, page.width, page.height);
                if (pdf === null) {
                    pdf = await window.html2pdf().set(options).from(page, 'canvas').toPdf().get('pdf');
                    if (pdf.internal.getNumberOfPages() !== 1) throw new Error('The PDF page size could not be verified. Use Print / Save PDF instead.');
                } else {
                    pdf.addPage('a4', 'landscape');
                    pdf.addImage(page.toDataURL('image/jpeg', 0.98), 'JPEG', 0, 0, 297, 210, undefined, 'FAST');
                }
            }
            if (!pdf) throw new Error('No report sheets are available.');
            await Promise.resolve(pdf.save(reports.dataset.midtermFile || 'midterm-reports.pdf'));
            reports.dataset.exportStatus = 'complete';
            reports.dataset.exportPages = String(pdf.internal.getNumberOfPages());
            message(sheets.length + ' landscape sheet(s) exported.');
        } catch (error) {
            reports.dataset.exportStatus = 'error';
            message((error.message || 'Unable to export the report.') + ' You can also use Print / Save PDF.');
        } finally {
            exporting = false; buttons.forEach(function (button) { button.disabled = false; });
        }
    }
    document.querySelectorAll('[data-midterm-print]').forEach(function (button) { button.addEventListener('click', printReports); });
    document.querySelectorAll('[data-midterm-download]').forEach(function (button) { button.addEventListener('click', downloadReports); });
    window.addEventListener('beforeprint', function () { try { fitSheets(); } catch (error) { message(error.message); } });
    window.addEventListener('resize', function () { try { fitSheets(); } catch (error) { message(error.message); } });
    imagesReady().then(fitSheets).catch(function (error) { message(error.message); });
    if (reports.hasAttribute('data-midterm-auto-download')) downloadReports();
}());
