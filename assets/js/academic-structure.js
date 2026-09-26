document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-allocation-form]').forEach(function (form) {
        var classSelect = form.querySelector('[data-allocation-class]');
        var subjectSelect = form.querySelector('[data-allocation-subject]');
        if (!classSelect || !subjectSelect) return;
        var subjects = Array.from(subjectSelect.options).filter(function (option) { return !!option.value; });
        var placeholder = subjectSelect.options[0];
        function filterSubjects() {
            var selected = subjectSelect.value;
            var classId = classSelect.value;
            var available = subjects.filter(function (option) { return option.dataset.classId === classId; });
            // Rebuild native options so mobile pickers also omit other classes.
            subjectSelect.replaceChildren(placeholder);
            available.forEach(function (option) {
                subjectSelect.appendChild(option);
            });
            placeholder.textContent = !classId ? 'Choose a class first' : (available.length ? 'Choose subject' : 'No unallocated subjects for this class');
            subjectSelect.value = available.some(function (option) { return option.value === selected; }) ? selected : '';
        }
        classSelect.addEventListener('change', filterSubjects);
        filterSubjects();
    });
});
