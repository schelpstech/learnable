'use strict';
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const source = fs.readFileSync(require('node:path').join(__dirname, '../assets/js/cbt-exam.js'), 'utf8');

function harness() {
    const nodes = new Map(), listeners = {}, calls = [], timers = [], local = new Map();
    function node() {
        const children = new Map();
        return {hidden: false, disabled: false, textContent: '', classList: {add() {}, toggle() {}},
            parentNode: {classList: {toggle() {}}, setAttribute() {}},
            querySelector(s) { if (!children.has(s)) children.set(s, node()); return children.get(s); },
            querySelectorAll(s) { return s === 'dd' ? [node(), node()] : []; },
            addEventListener() {}, appendChild() {}, setAttribute() {}};
    }
    const now = Date.now();
    const state = {attempt: {id: 1, status: 'in_progress', assessment_status: 'scheduled',
        expires_at_iso: new Date(now + 60000).toISOString(), navigation_mode: 'free'},
        server_time: new Date(now).toISOString(),
        questions: [{id: 9, answer: null, save_version: 0, question_type: 'short_answer', marks_available: 1}]};
    const root = node();
    const get = s => { if (!nodes.has(s)) nodes.set(s, node()); return nodes.get(s); };
    root.querySelector = get;
    const context = {Promise, Date, JSON, Number, String, Object, Array, Math, Error,
        document: {getElementById: () => ({textContent: JSON.stringify({state})}),
            querySelector: s => s === '[data-cbt-exam]' ? root : get(s),
            createElement: node, addEventListener() {}},
        navigator: {onLine: true}, localStorage: {getItem: k => local.get(k), setItem: (k,v) => local.set(k,v), removeItem: k => local.delete(k)},
        window: {confirm: () => true, alert() {}, addEventListener: (k,f) => { listeners[k] = f; }},
        setTimeout: f => { timers.push(f); return timers.length; }, clearTimeout() {}, setInterval() {},
        fetch: (_, options) => { const payload = JSON.parse(options.body); calls.push(payload); return context.respond(payload); }};
    const response = data => Promise.resolve({ok: true, json: () => Promise.resolve({ok: true, data})});
    context.respond = payload => response(payload.action === 'submit' ? {status: 'marked', submission_ref: 'QA'} : {submitted: false, save_version: payload.save_version});
    const cutoff = source.lastIndexOf("    if (attempt.status !== 'in_progress') showReceipt");
    assert(cutoff > 0);
    vm.runInNewContext(source.slice(0, cutoff) + 'globalThis.test = {submit, markChanged, saveQuestion, saveAll, refreshState, tick, dirty, answers, versions, attempt, getExpiry: () => expiry, isSubmitting: () => submitting}; }());', context);
    return {context, test: context.test, response, calls, nodes, state, local, timers};
}
async function flush() { for (let n = 0; n < 30; n++) await Promise.resolve(); }
(async function () {
    let h = harness();
    h.test.markChanged(9, 'Latest answer'); h.test.submit(); await flush();
    assert.deepEqual(h.calls.map(p => p.action), ['save', 'submit']);
    assert.equal(h.calls[0].answer, 'Latest answer');
    assert.equal(h.test.attempt.status, 'marked');
    console.log('PASS: manual submission waits for pending answers');

    h = harness(); let resolve;
    h.context.respond = () => new Promise(r => { resolve = r; });
    h.test.markChanged(9, 'Old'); const saving = h.test.saveQuestion(9);
    h.test.markChanged(9, 'New'); resolve(await h.response({submitted: false})); await saving;
    assert.equal(h.test.dirty[9], true); assert.equal(h.test.answers[9], 'New');
    console.log('PASS: an older acknowledgement preserves newer unsaved edits');

    h = harness(); h.context.respond = () => Promise.reject(new Error('Offline'));
    h.test.markChanged(9, 'Keep this'); h.test.submit(); await flush();
    assert(!h.calls.some(p => p.action === 'submit')); assert(h.test.dirty[9]);
    assert(!h.test.isSubmitting()); assert(h.local.size);
    console.log('PASS: a failed final save prevents submission and preserves the local answer');

    h = harness(); h.context.navigator.onLine = false;
    h.test.markChanged(9, 'Offline answer'); h.test.submit(); await flush();
    assert.equal(h.calls.length, 0); assert(!h.test.isSubmitting());
    console.log('PASS: offline manual submission waits for reconnection');

    h = harness(); const later = new Date(Date.now() + 300000).toISOString();
    h.context.respond = () => h.response({attempt: {...h.state.attempt, expires_at_iso: later, assessment_status: 'paused'}, server_time: new Date().toISOString()});
    await h.test.refreshState(); assert.equal(h.test.getExpiry(), Date.parse(later));
    assert.equal(h.nodes.get('[data-cbt-submit]').disabled, true);
    h.test.markChanged(9, 'Blocked'); assert.equal(h.test.versions[9], 0);
    console.log('PASS: live state updates extra time and disables paused answers');

    h = harness(); h.context.respond = () => h.response({attempt: {...h.state.attempt, expires_at_iso: new Date(Date.now()-10000).toISOString()}, server_time: new Date().toISOString()});
    await h.test.refreshState(); let failures = 0;
    h.context.respond = () => { failures++; return Promise.reject(new Error('Network lost')); };
    h.test.tick(); await flush(); assert(!h.test.isSubmitting());
    h.test.tick(); await flush(); assert.equal(failures, 2);
    h.context.respond = () => h.response({attempt: {...h.state.attempt, status: 'marked', submission_ref: 'RECOVERED'}, server_time: new Date().toISOString()});
    h.test.tick(); await flush(); assert.equal(h.test.attempt.status, 'marked');
    console.log('PASS: failed timeout requests retry and recover the server receipt');
    h = harness();
    h.context.respond = () => h.response({attempt: {...h.state.attempt, expires_at_iso: new Date(Date.now()-1000).toISOString()}, server_time: new Date().toISOString()});
    await h.test.refreshState(); h.calls.length = 0;
    h.test.markChanged(9, 'Last answer');
    h.context.respond = payload => h.response(payload.action === 'save' ? {submitted: false} : {attempt: {...h.state.attempt, status: 'marked', submission_ref: 'TIMEOUT'}, server_time: new Date().toISOString()});
    h.test.tick(); await flush();
    assert.deepEqual(h.calls.map(p => p.action), ['save', 'state']);
    assert.equal(h.test.attempt.status, 'marked');
    console.log('PASS: timeout flushes pending answers before confirming the authoritative server state');

})().catch(error => { console.error(error); process.exitCode = 1; });
