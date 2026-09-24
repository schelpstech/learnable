document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-allocation-form]').forEach(function (form) {
        var classSelect = form.querySelector('[data-allocation-class]');
        var subjectSelect = form.querySelector('[data-allocation-subject]');
        if (!classSelect || !subjectSelect) return;
        function filterSubjects() {
            var selected = subjectSelect.value;
            var classId = classSelect.value;
            Array.from(subjectSelect.options).forEach(function (option) {
                if (!option.value) return;
                option.hidden = !classId || option.dataset.classId !== classId;
                option.disabled = option.hidden;
            });
            if (selected && subjectSelect.selectedOptions[0] && subjectSelect.selectedOptions[0].disabled) subjectSelect.value = '';
        }
        classSelect.addEventListener('change', filterSubjects);
        filterSubjects();
    });
});
