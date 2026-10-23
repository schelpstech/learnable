(function () {
    'use strict';
    document.querySelectorAll('.learner-passport-form').forEach(function (form) {
        if (form.dataset.ready) return;
        form.dataset.ready = 'true';
        var input = form.querySelector('[name="passport"]');
        var preview = form.querySelector('.learner-passport-image');
        var status = form.querySelector('[role="status"]');
        var button = form.querySelector('button[type="submit"]');
        var savedUrl = preview.src;
        var pendingUrl = '';
        function message(text, error) { status.textContent = text; status.dataset.error = String(error); }
        function clearPreview() {
            if (pendingUrl) URL.revokeObjectURL(pendingUrl);
            pendingUrl = '';
            preview.src = savedUrl;
        }
        input.addEventListener('change', function () {
            clearPreview();
            var file = input.files[0];
            if (!file) { message('', false); return; }
            if (!['image/jpeg', 'image/png'].includes(file.type) || file.size > Number(form.dataset.maxBytes)) {
                input.value = '';
                message('Choose a JPG or PNG within the displayed size limit.', true);
                return;
            }
            pendingUrl = URL.createObjectURL(file);
            preview.src = pendingUrl;
            message('Preview ready. Click Upload passport to save.', false);
        });
        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            if (button.disabled || !form.reportValidity()) return;
            var body = new FormData(form);
            button.disabled = true;
            input.disabled = true;
            message('Uploading passport…', false);
            try {
                var response = await fetch(form.action, { method: 'POST', body: body, credentials: 'same-origin' });
                var data = await response.json();
                if (!response.ok || !data.ok) throw new Error(data.message || 'Unable to upload the passport. Please try again.');
                savedUrl = new URL(data.path, new URL(form.dataset.root, document.baseURI)).href;
                clearPreview();
                input.value = '';
                message(data.message, false);
            } catch (error) {
                message(error instanceof SyntaxError ? 'The server did not return a valid response. Please refresh and try again.' : error.message, true);
            } finally {
                button.disabled = false;
                input.disabled = false;
            }
        });
    });
}());
