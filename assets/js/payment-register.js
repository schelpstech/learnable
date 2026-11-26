document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('[data-payment-entry]');
    if (!form) return;
    const classField = form.elements.class_id;
    const learnerField = form.elements.student;
    const term = form.elements.term.value;
    const output = form.querySelector('[data-payment-balance]');
    const formatter = new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN', maximumFractionDigits: 0 });
    let requestNumber = 0;

    async function request(params) {
        const query = new URLSearchParams({term, ...params});
        const response = await fetch('getamt.php?' + query, {headers: {Accept: 'application/json'}});
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Unable to load the ledger.');
        return data;
    }

    async function loadBalance() {
        const version = ++requestNumber;
        output.textContent = '';
        if (!learnerField.value) return;
        output.textContent = 'Checking term balance…';
        try {
            const {balance} = await request({stdid: learnerField.value});
            if (version !== requestNumber) return;
            const result = balance.outstanding > 0
                ? formatter.format(balance.outstanding) + ' outstanding'
                : balance.credit > 0 ? formatter.format(balance.credit) + ' credit' : 'No balance due';
            output.textContent = result + ' · ' + formatter.format(balance.net) + ' net billed · ' + formatter.format(balance.paid) + ' confirmed';
        } catch (error) {
            if (version === requestNumber) output.textContent = 'Term balance could not be loaded.';
        }
    }

    async function loadLearners(selected) {
        const version = ++requestNumber;
        learnerField.replaceChildren(new Option(classField.value ? 'Loading learners…' : 'Choose a class first', ''));
        learnerField.disabled = true;
        output.textContent = '';
        if (!classField.value) return;
        try {
            const {learners} = await request({class_id: classField.value});
            if (version !== requestNumber) return;
            learnerField.replaceChildren(new Option(learners.length ? 'Choose a learner' : 'No learners found', ''));
            learners.forEach(function (learner) {
                learnerField.add(new Option(learner.learner_name + ' · ' + learner.uname, learner.uname));
            });
            learnerField.disabled = false;
            if (selected) learnerField.value = selected;
            loadBalance();
        } catch (error) {
            if (version === requestNumber) learnerField.replaceChildren(new Option('Unable to load learners', ''));
        }
    }

    classField.addEventListener('change', function () { loadLearners(''); });
    learnerField.addEventListener('change', loadBalance);
    if (classField.value) loadLearners(form.dataset.selectedLearner || '');
});
