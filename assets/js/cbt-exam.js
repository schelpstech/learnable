(function () {
    'use strict';
    var stateNode = document.getElementById('cbt-exam-state');
    if (!stateNode) return;
    var initial = JSON.parse(stateNode.textContent);
    var state = initial.state;
    var attempt = state.attempt;
    var questions = state.questions || [];
    var root = document.querySelector('[data-cbt-exam]');
    var current = 0;
    var dirty = {};
    var answers = {};
    var versions = {};
    var serverStartedAt = Date.parse(state.server_time);
    var clientStartedAt = Date.now();
    var expiry = Date.parse(attempt.expires_at);
    var submitting = false;
    var storageKey = 'learnable_cbt_' + attempt.id;
    var saveTimer = null;

    var elements = {
        timer: root.querySelector('[data-cbt-timer] strong'),
        save: root.querySelector('[data-cbt-save-status]'),
        nav: root.querySelector('[data-cbt-question-nav]'),
        number: root.querySelector('[data-cbt-question-number]'),
        marks: root.querySelector('[data-cbt-question-marks]'),
        prompt: root.querySelector('[data-cbt-prompt]'),
        media: root.querySelector('[data-cbt-media]'),
        answer: root.querySelector('[data-cbt-answer]'),
        flag: root.querySelector('[data-cbt-flag]'),
        previous: root.querySelector('[data-cbt-previous]'),
        next: root.querySelector('[data-cbt-next]'),
        progress: root.querySelector('[data-cbt-progress]'),
        submit: root.querySelector('[data-cbt-submit]'),
        receipt: root.querySelector('[data-cbt-receipt]'),
        question: root.querySelector('[data-cbt-question]'),
        connectivity: root.querySelector('[data-cbt-connectivity]'),
        fullscreen: root.querySelector('[data-cbt-fullscreen]')
    };

    questions.forEach(function (question) {
        answers[question.id] = question.answer;
        versions[question.id] = Number(question.save_version || 0);
    });
    try {
        var local = JSON.parse(localStorage.getItem(storageKey) || '{}');
        Object.keys(local).forEach(function (id) {
            if (Object.prototype.hasOwnProperty.call(answers, id) && local[id] && local[id].version > versions[id]) {
                answers[id] = local[id].answer;
                versions[id] = local[id].version;
                dirty[id] = true;
            }
        });
    } catch (error) {}

    function api(payload) {
        payload.attempt_id = Number(attempt.id);
        return fetch('app/cbt_api.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type': 'application/json', 'X-CBT-Request': '1'},
            body: JSON.stringify(payload)
        }).then(function (response) {
            return response.json().then(function (body) {
                if (!response.ok || !body.ok) throw new Error(body.message || 'Request failed.');
                return body.data;
            });
        });
    }

    function setSaveStatus(status, message) {
        elements.save.className = 'cbt-save-indicator is-' + status;
        elements.save.querySelector('span').textContent = message;
    }

    function isAnswered(answer) {
        if (Array.isArray(answer)) return answer.length > 0 && answer.some(function (value) { return value !== ''; });
        if (answer && typeof answer === 'object') return Object.keys(answer).length > 0;
        return answer !== null && String(answer || '').trim() !== '';
    }

    function persistLocal() {
        var data = {};
        Object.keys(answers).forEach(function (id) { data[id] = {answer: answers[id], version: versions[id]}; });
        try { localStorage.setItem(storageKey, JSON.stringify(data)); } catch (error) {}
    }

    function markChanged(questionId, answer) {
        answers[questionId] = answer;
        versions[questionId] = Number(versions[questionId] || 0) + 1;
        dirty[questionId] = true;
        persistLocal();
        setSaveStatus('saving', navigator.onLine ? 'Saving answer…' : 'Saved on this device');
        renderNavigation();
        clearTimeout(saveTimer);
        saveTimer = setTimeout(function () { saveQuestion(questionId); }, 650);
    }

    function saveQuestion(questionId) {
        if (!dirty[questionId] || submitting || attempt.status !== 'in_progress') return Promise.resolve();
        if (!navigator.onLine) {
            setSaveStatus('offline', 'Waiting to reconnect');
            return Promise.resolve();
        }
        var question = questions.filter(function (item) { return String(item.id) === String(questionId); })[0];
        return api({
            action: 'save', question_id: Number(questionId), answer: answers[questionId],
            flagged: !!question.is_flagged, save_version: versions[questionId]
        }).then(function (data) {
            if (data.submitted) {
                showReceipt(data.receipt);
                return;
            }
            delete dirty[questionId];
            setSaveStatus(Object.keys(dirty).length ? 'saving' : 'saved', Object.keys(dirty).length ? 'Saving answers…' : 'All answers saved');
        }).catch(function () {
            setSaveStatus('offline', 'Save pending — reconnecting');
        });
    }

    function saveAll() {
        return Promise.all(Object.keys(dirty).map(saveQuestion));
    }

    function renderNavigation() {
        elements.nav.innerHTML = '';
        questions.forEach(function (question, index) {
            var button = document.createElement('button');
            button.type = 'button';
            button.textContent = String(index + 1);
            if (index === current) button.classList.add('is-current');
            if (isAnswered(answers[question.id])) button.classList.add('is-answered');
            if (question.is_flagged) button.classList.add('is-flagged');
            button.disabled = attempt.navigation_mode === 'linear' && !Number(attempt.allow_backtrack) && index < current;
            button.addEventListener('click', function () { current = index; render(); });
            elements.nav.appendChild(button);
        });
    }

    function addOptionControl(container, question, option, multiple) {
        var label = document.createElement('label');
        label.className = 'cbt-exam-option';
        var input = document.createElement('input');
        input.type = multiple ? 'checkbox' : 'radio';
        input.name = 'answer-' + question.id;
        input.value = option.option_key;
        var currentAnswer = answers[question.id];
        input.checked = multiple ? Array.isArray(currentAnswer) && currentAnswer.indexOf(option.option_key) !== -1 : String(currentAnswer || '') === String(option.option_key);
        input.addEventListener('change', function () {
            if (multiple) {
                var selected = Array.isArray(answers[question.id]) ? answers[question.id].slice() : [];
                var position = selected.indexOf(option.option_key);
                if (input.checked && position === -1) selected.push(option.option_key);
                if (!input.checked && position !== -1) selected.splice(position, 1);
                markChanged(question.id, selected);
            } else {
                markChanged(question.id, option.option_key);
            }
        });
        var key = document.createElement('span'); key.className = 'cbt-exam-option__key'; key.textContent = option.option_key;
        var text = document.createElement('span'); text.innerHTML = option.option_text;
        label.appendChild(input); label.appendChild(key); label.appendChild(text); container.appendChild(label);
    }

    function makeSelect(options, value) {
        var select = document.createElement('select');
        var placeholder = document.createElement('option'); placeholder.value = ''; placeholder.textContent = 'Choose'; select.appendChild(placeholder);
        options.forEach(function (option) {
            var node = document.createElement('option');
            node.value = typeof option === 'string' ? option : option.option_key;
            node.textContent = typeof option === 'string' ? option : option.option_key + ' · ' + option.option_text;
            if (String(node.value) === String(value || '')) node.selected = true;
            select.appendChild(node);
        });
        return select;
    }

    function renderAnswer(question) {
        var container = elements.answer;
        container.innerHTML = '';
        var type = question.question_type;
        if (type === 'single_choice' || type === 'true_false' || type === 'multiple_choice') {
            (question.options || []).forEach(function (option) { addOptionControl(container, question, option, type === 'multiple_choice'); });
        } else if (type === 'fill_blank' || type === 'short_answer') {
            var input = document.createElement('input'); input.type = 'text'; input.className = 'cbt-exam-text-answer'; input.placeholder = 'Type your answer'; input.value = answers[question.id] || '';
            input.addEventListener('input', function () { markChanged(question.id, input.value); }); container.appendChild(input);
        } else if (type === 'essay') {
            var textarea = document.createElement('textarea'); textarea.className = 'cbt-exam-essay'; textarea.rows = 10; textarea.placeholder = 'Write your answer clearly…'; textarea.value = answers[question.id] || '';
            textarea.addEventListener('input', function () { markChanged(question.id, textarea.value); }); container.appendChild(textarea);
        } else if (type === 'matching') {
            var matching = question.options || {items: [], targets: []};
            var matchingAnswer = Array.isArray(answers[question.id]) ? answers[question.id] : [];
            (matching.items || []).forEach(function (item, index) {
                var row = document.createElement('label'); row.className = 'cbt-match-row'; var text = document.createElement('span'); text.textContent = item.option_text;
                var select = makeSelect(matching.targets || [], matchingAnswer[index]);
                select.addEventListener('change', function () { var next = matchingAnswer.slice(); next[index] = select.value; matchingAnswer = next; markChanged(question.id, next); });
                row.appendChild(text); row.appendChild(select); container.appendChild(row);
            });
        } else if (type === 'ordering') {
            var orderingAnswer = Array.isArray(answers[question.id]) ? answers[question.id] : [];
            (question.options || []).forEach(function (_, index) {
                var row = document.createElement('label'); row.className = 'cbt-match-row'; var text = document.createElement('span'); text.textContent = 'Position ' + (index + 1);
                var select = makeSelect(question.options || [], orderingAnswer[index]);
                select.addEventListener('change', function () { var next = orderingAnswer.slice(); next[index] = select.value; orderingAnswer = next; markChanged(question.id, next); });
                row.appendChild(text); row.appendChild(select); container.appendChild(row);
            });
        }
    }

    function renderMedia(question) {
        elements.media.innerHTML = '';
        if (!question.media_url || !question.media_type) return;
        var media;
        if (question.media_type === 'image') { media = document.createElement('img'); media.alt = 'Question illustration'; }
        else if (question.media_type === 'audio') { media = document.createElement('audio'); media.controls = true; }
        else { media = document.createElement('video'); media.controls = true; }
        media.src = question.media_url; elements.media.appendChild(media);
    }

    function render() {
        if (!questions.length || attempt.status !== 'in_progress') return;
        var question = questions[current];
        elements.number.textContent = 'Question ' + (current + 1);
        elements.marks.textContent = Number(question.marks_available) + (Number(question.marks_available) === 1 ? ' mark' : ' marks');
        elements.prompt.innerHTML = question.prompt_snapshot;
        elements.flag.checked = !!question.is_flagged;
        elements.progress.textContent = (current + 1) + ' of ' + questions.length;
        elements.previous.disabled = current === 0 || (attempt.navigation_mode === 'linear' && !Number(attempt.allow_backtrack));
        elements.next.textContent = current === questions.length - 1 ? 'Review answers →' : 'Next →';
        renderMedia(question); renderAnswer(question); renderNavigation();
    }

    function sendEvent(type, details) {
        if (attempt.status !== 'in_progress') return;
        api({action: 'event', event_type: type, details: details || {}}).catch(function () {});
    }

    function showReceipt(receipt) {
        attempt.status = receipt.status || 'submitted';
        submitting = false;
        elements.question.hidden = true;
        document.querySelector('.cbt-exam-controls').hidden = true;
        elements.receipt.hidden = false;
        elements.receipt.innerHTML = '<div class="cbt-exam-seal">✓</div><h1>Script submitted</h1><p>Your answers are locked and safely recorded.</p><dl><div><dt>Submission reference</dt><dd></dd></div><div><dt>Submitted</dt><dd></dd></div></dl><a class="cbt-btn cbt-btn--primary" href="app/router.php?pageid=cbt">Return to my assessments</a>';
        var values = elements.receipt.querySelectorAll('dd');
        values[0].textContent = receipt.submission_ref || 'Recorded';
        values[1].textContent = receipt.submitted_at || new Date().toLocaleString();
        try { localStorage.removeItem(storageKey); } catch (error) {}
    }

    function submit() {
        if (submitting || attempt.status !== 'in_progress') return;
        var unanswered = questions.filter(function (question) { return !isAnswered(answers[question.id]); }).length;
        var prompt = unanswered ? unanswered + ' question(s) are unanswered. Submit your script anyway?' : 'Submit your completed script now? You cannot change answers afterwards.';
        if (!window.confirm(prompt)) return;
        submitting = true; setSaveStatus('saving', 'Finalising script…');
        saveAll().then(function () { return api({action: 'submit'}); }).then(showReceipt).catch(function (error) {
            submitting = false; setSaveStatus('offline', error.message || 'Submission pending'); window.alert(error.message || 'Unable to submit yet. Please stay on this page.');
        });
    }

    function tick() {
        if (attempt.status !== 'in_progress') return;
        var serverNow = serverStartedAt + (Date.now() - clientStartedAt);
        var seconds = Math.max(0, Math.floor((expiry - serverNow) / 1000));
        var hours = Math.floor(seconds / 3600); var minutes = Math.floor((seconds % 3600) / 60); var remainder = seconds % 60;
        elements.timer.textContent = (hours ? String(hours).padStart(2, '0') + ':' : '') + String(minutes).padStart(2, '0') + ':' + String(remainder).padStart(2, '0');
        elements.timer.parentNode.classList.toggle('is-warning', seconds <= 300);
        if (seconds === 300 || seconds === 60) elements.timer.parentNode.setAttribute('aria-label', seconds === 300 ? 'Five minutes remaining' : 'One minute remaining');
        if (seconds <= 0 && !submitting) {
            submitting = true; saveAll().finally(function () { api({action: 'submit'}).then(showReceipt).catch(function () { setTimeout(tick, 1000); }); });
            return;
        }
        setTimeout(tick, 1000);
    }

    elements.previous.addEventListener('click', function () { if (current > 0) { current--; render(); } });
    elements.next.addEventListener('click', function () { if (current < questions.length - 1) { current++; render(); } else { submit(); } });
    elements.submit.addEventListener('click', submit);
    elements.flag.addEventListener('change', function () { var question = questions[current]; question.is_flagged = elements.flag.checked ? 1 : 0; markChanged(question.id, answers[question.id]); });
    elements.fullscreen.addEventListener('click', function () { if (!document.fullscreenElement && document.documentElement.requestFullscreen) document.documentElement.requestFullscreen(); else if (document.exitFullscreen) document.exitFullscreen(); });
    document.addEventListener('visibilitychange', function () { sendEvent(document.hidden ? 'tab_hidden' : 'tab_visible', {client_time: new Date().toISOString()}); });
    document.addEventListener('fullscreenchange', function () { if (!document.fullscreenElement) sendEvent('fullscreen_exit', {client_time: new Date().toISOString()}); });
    window.addEventListener('offline', function () { elements.connectivity.hidden = false; setSaveStatus('offline', 'Saved on this device'); sendEvent('offline', {client_time: new Date().toISOString()}); });
    window.addEventListener('online', function () { elements.connectivity.hidden = true; sendEvent('online', {client_time: new Date().toISOString()}); saveAll(); });
    if (Number(attempt.restrict_clipboard)) document.addEventListener('copy', function (event) { event.preventDefault(); sendEvent('clipboard', {client_time: new Date().toISOString()}); });
    window.addEventListener('beforeunload', function (event) { if (attempt.status === 'in_progress' && Object.keys(dirty).length) { event.preventDefault(); event.returnValue = ''; } });
    setInterval(saveAll, Number(initial.autosave_interval || 8000));

    if (attempt.status !== 'in_progress') showReceipt({submission_ref: attempt.submission_ref, submitted_at: attempt.submitted_at, status: attempt.status});
    else { render(); tick(); if (Object.keys(dirty).length) saveAll(); }
}());
