(function () {
    'use strict';

    function setAllocation(select) {
        var form = select.closest('form');
        if (!form) return;
        var parts = String(select.value || '').split(':');
        var classField = form.querySelector('[data-cbt-class-id]');
        var subjectField = form.querySelector('[data-cbt-subject-id]');
        if (classField) classField.value = parts[0] || '';
        if (subjectField) subjectField.value = parts[1] || '';

        var topicSelect = form.querySelector('[data-cbt-topic-select]');
        var topicData = document.getElementById('cbt-topic-data');
        if (!topicSelect || !topicData) return;
        var data = {};
        try { data = JSON.parse(topicData.textContent || '{}'); } catch (error) { data = {}; }
        var topics = data[select.value] || [];
        topicSelect.innerHTML = '';
        var placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = topics.length ? 'Choose an approved topic' : 'No approved topic for this allocation';
        topicSelect.appendChild(placeholder);
        topics.forEach(function (topic) {
            var option = document.createElement('option');
            option.value = topic.id;
            option.textContent = topic.week + ' · ' + topic.topic;
            topicSelect.appendChild(option);
        });
    }

    function disablePanel(panel, disabled) {
        if (!panel) return;
        panel.hidden = disabled;
        panel.querySelectorAll('input, select, textarea').forEach(function (control) {
            control.disabled = disabled;
        });
    }

    function configureQuestionForm(form) {
        var typeSelect = form.querySelector('[data-cbt-question-type]');
        if (!typeSelect) return;
        var optionsPanel = form.querySelector('[data-cbt-options-panel]');
        var truePanel = form.querySelector('[data-cbt-true-panel]');
        var textPanel = form.querySelector('[data-cbt-text-panel]');

        function update() {
            var type = typeSelect.value;
            var usesOptions = ['single_choice', 'multiple_choice', 'matching', 'ordering'].indexOf(type) !== -1;
            disablePanel(optionsPanel, !usesOptions);
            disablePanel(truePanel, type !== 'true_false');
            disablePanel(textPanel, ['fill_blank', 'short_answer'].indexOf(type) === -1);
            if (usesOptions) {
                optionsPanel.querySelectorAll('input[name="correct_option"]').forEach(function (input) {
                    input.disabled = type !== 'single_choice';
                });
                optionsPanel.querySelectorAll('input[name="correct_options[]"]').forEach(function (input) {
                    input.disabled = type !== 'multiple_choice';
                });
                optionsPanel.querySelectorAll('input[name="match_key[]"]').forEach(function (input) {
                    input.disabled = ['matching', 'ordering'].indexOf(type) === -1;
                });
            }
        }
        typeSelect.addEventListener('change', update);
        update();
    }

    document.querySelectorAll('[data-cbt-allocation-select], [data-cbt-bank-allocation]').forEach(function (select) {
        select.addEventListener('change', function () { setAllocation(select); });
        setAllocation(select);
    });
    document.querySelectorAll('[data-cbt-question-form]').forEach(configureQuestionForm);

    var fingerprint = [
        navigator.userAgent || '', navigator.language || '',
        Intl.DateTimeFormat().resolvedOptions().timeZone || '',
        window.screen ? window.screen.width + 'x' + window.screen.height : ''
    ].join('|').slice(0, 1000);
    document.querySelectorAll('[data-cbt-fingerprint]').forEach(function (field) { field.value = fingerprint; });
    document.querySelectorAll('.cbt-start-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm('Start this assessment now? The official server timer begins immediately.')) {
                event.preventDefault();
            }
        });
    });
    document.querySelectorAll('[data-cbt-print]').forEach(function (button) {
        button.addEventListener('click', function () { window.print(); });
    });
}());
