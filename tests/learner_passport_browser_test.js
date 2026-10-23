// Optional browser coverage invoked by learner_passport_http_test.php --browser.
const fs = require('fs');
const assert = require('assert/strict');
const { chromium } = require('playwright');
const manifestPath = process.argv[2];
const fixture = JSON.parse(fs.readFileSync(manifestPath, 'utf8'));
const save = () => fs.writeFileSync(manifestPath, JSON.stringify(fixture));
function cookies(file) {
    return fs.readFileSync(file, 'utf8').split(/\r?\n/).filter(line => line && (!line.startsWith('#') || line.startsWith('#HttpOnly_'))).map(line => {
        const fields = line.replace(/^#HttpOnly_/, '').split('\t');
        return { domain: fields[0], path: fields[2], secure: fields[3] === 'TRUE', name: fields[5], value: fields[6], httpOnly: line.startsWith('#HttpOnly_') };
    });
}
(async () => {
    const browser = await chromium.launch({ headless: true, ...(process.env.PLAYWRIGHT_CHANNEL ? { channel: process.env.PLAYWRIGHT_CHANNEL } : {}) });
    try {
        for (const role of ['teacher', 'admin']) {
            const context = await browser.newContext({ viewport: { width: role === 'teacher' ? 390 : 1280, height: 900 } });
            await context.addCookies(cookies(fixture.jars[role]));
            // This is a local UI test; avoid loading analytics or third-party widgets.
            await context.route('**/*', route => new URL(route.request().url()).origin === new URL(fixture.base).origin ? route.continue() : route.abort());
            const page = await context.newPage();
            const path = role === 'teacher' ? '/learn/app/router.php?pageid=manage_learner&instance=' : '/admin/stdedit.php?unam=';
            await page.goto(fixture.base + path + fixture.learner, { waitUntil: 'domcontentloaded' });
            const form = page.locator('.learner-passport-form');
            await form.locator('input[type=file]').waitFor({ state: 'visible' });
            await page.waitForFunction(() => document.querySelector('.learner-passport-form').dataset.ready === 'true');
            const button = form.getByRole('button', { name: 'Upload passport' });
            const status = form.getByRole('status');
            const saved = await form.locator('img').getAttribute('src');
            await form.locator('input[type=file]').setInputFiles({ name: 'invalid.txt', mimeType: 'text/plain', buffer: Buffer.from('not a photograph') });
            assert.match(await status.innerText(), /Choose a JPG or PNG/);
            assert.equal(await form.locator('img').getAttribute('src'), saved.startsWith('http') ? saved : new URL(saved, await page.evaluate(() => document.baseURI)).href);
            for (let attempt = 0; attempt < 2; attempt++) {
                await form.locator('input[type=file]').setInputFiles(fixture.image);
                await page.waitForFunction(() => document.querySelector('.learner-passport-status').textContent.includes('Preview ready'));
                assert.match(await form.locator('img').getAttribute('src'), /^blob:/);
                const responsePromise = page.waitForResponse(response => response.url().endsWith('/learner_passport.php') && response.request().method() === 'POST');
                await button.click();
                const response = await responsePromise;
                const data = await response.json();
                if (data.picture) { fixture.pictures.push(data.picture); save(); }
                assert.equal(response.status(), 200, data.message);
                assert.equal(data.ok, true);
                await page.waitForFunction(() => document.querySelector('.learner-passport-status').textContent.includes('uploaded successfully'));
                assert.equal(await button.isEnabled(), true);
                assert.equal(await form.locator('input[type=file]').isEnabled(), true);
                assert.equal(await form.locator('input[type=file]').inputValue(), '');
                await page.waitForFunction(() => { const img = document.querySelector('.learner-passport-image'); return img.complete && img.naturalWidth > 0 && !img.src.startsWith('blob:'); });
                assert.equal(await form.locator('img').getAttribute('src'), new URL(data.path, fixture.base + '/').href);
            }
            const cardFits = await form.evaluate(el => { const rect = el.closest('section').getBoundingClientRect(); return rect.left >= 0 && rect.right <= window.innerWidth + 1 && el.scrollWidth <= el.clientWidth + 1; });
            assert.equal(cardFits, true, role + ' passport controls fit the viewport');
            if (process.env.PASSPORT_QA_SCREENSHOT_DIR) {
                await form.scrollIntoViewIfNeeded();
                await page.screenshot({ path: process.env.PASSPORT_QA_SCREENSHOT_DIR + '/learner-passport-' + role + '.png', fullPage: true });
            }
            console.log('PASS: ' + role + ' browser preview, upload, repeat upload, image refresh and responsive form');
            if (role === 'teacher') {
                const latest = await form.locator('img').getAttribute('src');
                await page.getByRole('link', { name: 'Back to student list', exact: true }).click();
                await page.getByRole('heading', { name: /Student list/ }).waitFor();
                const row = page.locator('tr').filter({ hasText: fixture.learner });
                assert.equal(await row.locator('img').evaluate(img => img.src), latest);
                await row.getByRole('link', { name: 'Profile / passport', exact: true }).click();
                await page.locator('.learner-passport-form').waitFor();
                console.log('PASS: passport upload returns to the correct student list and learner editor with the latest photo');
            }
            if (role === 'admin') {
                await page.goto(fixture.base + '/admin/index.php?route=classes', { waitUntil: 'domcontentloaded' });
                const editTeacher = () => page.locator('a[href="allocateclass.php?classid=' + fixture.class_id + '"]');
                assert.equal(await editTeacher().innerText(), 'Edit class teacher');
                assert.equal(await editTeacher().locator('xpath=ancestor::tr').getByRole('link', { name: 'Edit class name', exact: true }).count(), 1);
                for (const teacher of [fixture.replacement, fixture.teacher]) {
                    await editTeacher().click();
                    await page.locator('select[name=teacher_id]').selectOption(teacher);
                    await Promise.all([page.waitForURL(/route=classes$/), page.getByRole('button', { name: 'Save class teacher', exact: true }).click()]);
                    assert.match(await page.getByRole('status').innerText(), /Class teacher updated successfully/);
                    const rowText = await editTeacher().locator('xpath=ancestor::tr').innerText();
                    assert.match(rowText, teacher === fixture.replacement ? /QA Passport other/ : /QA Passport teacher/);
                }
                console.log('PASS: class teacher changes twice through the browser, displays the new teacher and confirms each save');
            }
            await context.close();
        }
    } finally { await browser.close(); }
})().catch(error => { console.error(error.message); process.exitCode = 1; });
