(function () {
    'use strict';

    function ready(callback) {
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', callback);
        else callback();
    }

    ready(function () {
        var root = document.querySelector('[data-scheme-workspace]');
        if (!root) return;
        var form = root.querySelector('[data-scheme-form]');
        var classSelect = root.querySelector('[data-scheme-class]');
        var subjectSelect = root.querySelector('[data-scheme-subject]');
        var weekSelect = root.querySelector('[data-scheme-week]');
        var topicInput = root.querySelector('[data-scheme-topic]');
        var idInput = root.querySelector('[data-scheme-id]');
        var list = root.querySelector('[data-scheme-list]');
        var empty = root.querySelector('[data-scheme-empty]');
        var count = root.querySelector('[data-scheme-count]');
        var message = root.querySelector('[data-scheme-message]');
        var submit = root.querySelector('[data-scheme-submit]');
        var cancel = root.querySelector('[data-scheme-cancel]');
        var title = root.querySelector('[data-scheme-form-title]');
        var endpoint = root.dataset.endpoint;
        var csrf = form.querySelector('[name=csrf_token]').value;
        var topics = [];
        var subjectRequest = 0;

        try { topics = JSON.parse(list.dataset.topics || '[]'); } catch (ignore) { topics = []; }

        function request(action, values) {
            var body = new URLSearchParams(values || {});
            body.set('scheme_action', action);
            body.set('csrf_token', csrf);
            return fetch(endpoint, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8', 'Accept': 'application/json'},
                body: body.toString()
            }).then(function (response) {
                return response.json().catch(function () { return {ok: false, message: 'The server returned an unreadable response.'}; })
                    .then(function (data) { if (!response.ok || !data.ok) throw new Error(data.message || 'Unable to complete this request.'); return data; });
            });
        }

        function notice(text, error) {
            message.className = text ? (error ? 'workspace-error' : 'workspace-notice') : '';
            message.textContent = text || '';
        }

        function option(value, label) {
            var item = document.createElement('option');
            item.value = value;
            item.textContent = label;
            return item;
        }

        function resetEditor() {
            idInput.value = '0';
            weekSelect.value = '';
            topicInput.value = '';
            submit.textContent = 'Add topic';
            title.textContent = 'Plan a teaching week';
            cancel.hidden = true;
        }

        function render(items) {
            topics = Array.isArray(items) ? items : [];
            list.replaceChildren();
            count.textContent = topics.length + (topics.length === 1 ? ' topic' : ' topics');
            empty.hidden = topics.length > 0;
            topics.forEach(function (item, index) {
                var row = document.createElement('article');
                row.className = 'scheme-topic';
                var number = document.createElement('span');
                number.className = 'scheme-topic__number';
                number.textContent = String(index + 1);
                var copy = document.createElement('div');
                var meta = document.createElement('small');
                meta.textContent = item.week;
                var heading = document.createElement('h3');
                heading.textContent = item.topic;
                copy.append(meta, heading);
                var actions = document.createElement('div');
                actions.className = 'workspace-actions';
                if (String(item.staffid) === String(root.dataset.actor)) {
                    var edit = document.createElement('button');
                    edit.type = 'button'; edit.className = 'workspace-button secondary compact'; edit.textContent = 'Edit';
                    edit.addEventListener('click', function () {
                        idInput.value = item.schmid;
                        weekSelect.value = String(parseInt(String(item.week).replace(/\D/g, ''), 10));
                        topicInput.value = item.topic;
                        submit.textContent = 'Save changes'; title.textContent = 'Update ' + item.week; cancel.hidden = false;
                        topicInput.focus(); root.scrollIntoView({behavior: 'smooth', block: 'start'});
                    });
                    var remove = document.createElement('button');
                    remove.type = 'button'; remove.className = 'workspace-button danger compact'; remove.textContent = 'Remove';
                    remove.addEventListener('click', function () {
                        if (!window.confirm('Remove this topic from the active scheme? Existing records are retained.')) return;
                        request('archive', {id: item.schmid, class_id: classSelect.value, subject_id: subjectSelect.value, confirm: 'yes'})
                            .then(function (data) { render(data.topics); resetEditor(); notice(data.message, false); })
                            .catch(function (error) { notice(error.message, true); });
                    });
                    actions.append(edit, remove);
                }
                row.append(number, copy, actions);
                list.appendChild(row);
            });
        }

        function loadTopics() {
            if (!classSelect.value || !subjectSelect.value) { render([]); return Promise.resolve(); }
            return request('topics', {class_id: classSelect.value, subject_id: subjectSelect.value})
                .then(function (data) { render(data.topics); })
                .catch(function (error) { render([]); notice(error.message, true); });
        }

        classSelect.addEventListener('change', function () {
            var requestNumber = ++subjectRequest;
            resetEditor(); notice('', false); subjectSelect.replaceChildren(option('', 'Loading subjects…')); subjectSelect.disabled = true; render([]);
            if (!classSelect.value) { subjectSelect.replaceChildren(option('', 'Choose subject')); subjectSelect.disabled = false; return; }
            request('subjects', {class_id: classSelect.value}).then(function (data) {
                if (requestNumber !== subjectRequest) return;
                subjectSelect.replaceChildren(option('', data.subjects.length ? 'Choose subject' : 'No allocated subject'));
                data.subjects.forEach(function (item) { subjectSelect.appendChild(option(item.sbjid, item.sbjname)); });
                subjectSelect.disabled = false;
            }).catch(function (error) { if (requestNumber !== subjectRequest) return; subjectSelect.replaceChildren(option('', 'Unable to load subjects')); subjectSelect.disabled = false; notice(error.message, true); });
        });
        subjectSelect.addEventListener('change', function () { resetEditor(); notice('', false); loadTopics(); });
        cancel.addEventListener('click', resetEditor);
        form.addEventListener('submit', function (event) {
            event.preventDefault(); notice('', false); submit.disabled = true; submit.textContent = idInput.value !== '0' ? 'Saving changes…' : 'Adding topic…';
            var values = Object.fromEntries(new FormData(form).entries());
            request('save', values).then(function (data) {
                render(data.topics); resetEditor(); notice(data.message, false);
            }).catch(function (error) { notice(error.message, true); }).finally(function () { submit.disabled = false; submit.textContent = idInput.value !== '0' ? 'Save changes' : 'Add topic'; });
        });

        render(topics);
    });
}());
