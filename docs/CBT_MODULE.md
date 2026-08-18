# LearnAble CBT and continuous assessment module

## Purpose

This module adds scheme-of-work-based computer testing without changing the structure of any existing LearnAble table. Existing assignments in `lhpquestion` remain untouched; the new question bank and permanent test scripts use isolated `cbt_*` tables.

## Installation

1. Back up the school database using the normal deployment procedure.
2. Copy the project files and retain the existing `.env` values.
3. Run `php database/migrate.php` once. The migration is additive and idempotent.
4. Configure `CBT_INTEGRITY_SALT` in `.env` with a random deployment-specific value of at least 32 characters.
5. Schedule `php tasks/cbt_notifications.php` every 10 to 15 minutes if opening and closing reminders are required.

No existing table or column is dropped, renamed, or altered.

## Routes

### Instructor and learner portal

- `learn/app/router.php?pageid=cbt` — role-aware assessment register.
- `learn/app/router.php?pageid=cbt_builder` — instructor paper setup.
- `learn/app/router.php?pageid=cbt_bank` — instructor question bank and CSV import.
- `learn/app/router.php?pageid=cbt_marking&assessment_id={id}` — scripts, marking and analytics.
- `learn/app/router.php?pageid=cbt_review&attempt_id={id}` — learner's published script.
- `learn/exam.php` — distraction-free runner authenticated by an HttpOnly, SameSite secure-attempt cookie.
- `learn/app/cbt_api.php` — same-origin JSON autosave, activity and submission endpoint.
- `learn/app/cbt_export.php?assessment_id={id}` — authorization-checked CSV score export.
- `learn/app/cbt_template.php` — approved question-import template.

### Administrator

- `admin/index.php?route=cbt` — school register, moderation, monitoring, marking, publication, exceptional cases and transfer.

All state-changing portal and administrator forms use their existing role-specific CSRF session token. The exam API uses the separate high-entropy attempt cookie plus a same-origin custom request header.

## Permissions

- Instructors can create papers and questions only for the active-term class/subject allocations in `lhpalloc`.
- Instructors can select only active topics in `lhpscheme`; future weeks are refused from the normal instructor flow.
- Private questions remain visible only to their owner. School questions may be reused, but copying creates a new private draft.
- Learners are authorized through `cbt_assessment_assignments` and can never select another learner's attempt.
- Administrators can approve, pause, reschedule, cancel, archive, reopen, grant extra time, publish and transfer.
- The current project has no parent/guardian account model. Parent access therefore remains disabled rather than adding an unsafe pseudo-role. The published-script service is ready to be wrapped by a real child-to-guardian relationship if that module is enabled later.

## Tables

- `cbt_assessments` and `cbt_assessment_topics` — paper configuration and approved scheme scope.
- `cbt_questions`, `cbt_question_options`, `cbt_assessment_questions` — reusable, profiled bank and paper composition.
- `cbt_assessment_assignments` — class and learner-level eligibility plus accommodations.
- `cbt_attempts`, `cbt_attempt_questions`, `cbt_attempt_answers` — official timer, immutable question/answer snapshots and idempotent scripts.
- `cbt_marking_events` — original and revised mark history with reason and actor.
- `cbt_score_transfers` — conversion evidence and duplicate prevention.
- `cbt_integrity_events` — tab, connectivity, clipboard and session observations for human review only.
- `cbt_notification_targets` — learner-specific portal notification and reminder delivery state.
- `cbt_audit_log` — actor, action, before/after evidence, reason and privacy-preserving IP hash.
- `cbt_schema_migrations` — applied migration register.

Internal CBT relationships use constraints. References into legacy LearnAble records stay indexed but do not add foreign keys because several deployed school databases use different legacy identifier widths.

## Score mapping

No CBT score affects a term report until its script is marked, the result is published, and an authorized user confirms transfer.

- Weekly treatment converts to a score over 10 and writes `lhpweekrecord.score` for the selected scheme week.
- CA treatment converts to `lhpresultconfig.ca_score` and writes `lhpresultrecord.score`.
- Examination treatment converts to `lhpresultconfig.exam_score` and writes `lhpresultrecord.examscore`.
- `lhpresultrecord.totalscore` is recalculated after CA or examination transfer.
- Practice, temporary and excluded treatments cannot be transferred.
- A unique attempt/component constraint and a transactional recheck make a repeated transfer safe.

Schools that still use `admin/popca.php` to recalculate CA from weekly scores can continue doing so. A direct CBT CA transfer is an explicit authorized action; running the legacy population workflow later may intentionally recompute the CA value from weekly records.

## Examination behavior

- The expiry is stored server-side when the attempt starts. Refresh, browser restart and reconnect do not reset it.
- Questions and options are copied into the attempt at start. Later bank edits cannot change an old script.
- Correct answers are never returned by the active-exam state endpoint.
- Answers save on change and at the configured interval. Unsent changes remain in local browser storage during a connection loss and synchronize after reconnection without extending time.
- Submission is serialized in a database transaction and is idempotent. Repeated requests return the original receipt.
- Objective types are auto-marked. Submitted essay responses stay in `marking` until a teacher or administrator completes the manual mark.
- Correct answers and explanations can be released only after publication and after the assessment closing time.

Fullscreen, tab and clipboard controls are monitoring aids. They create reviewable events and never automatically punish a learner.

## Question import

Download the CSV template from Question Bank. Pipe (`|`) separates options, multiple correct answer indices, accepted answer variations and matching keys. Row numbering for option answers starts at `0`, matching the template example. Every row is validated through the same allocation, scheme, type, mark, HTML and answer rules as a manually entered question. Files are read from PHP's upload temporary location, limited to 2 MB, and never stored as executable content.

## Configuration

- `CBT_INTEGRITY_SALT` — salt used to hash IP/device evidence.
- `CBT_AUTOSAVE_INTERVAL` — browser autosave interval in milliseconds; minimum 3000.
- `CBT_OPENING_REMINDER_MINUTES` — lead time for opening reminders.
- `CBT_CLOSING_REMINDER_MINUTES` — lead time for closing reminders.

Email, SMS and push delivery remain opt-in integration points because this LearnAble installation has no confirmed configured provider. Portal notifications are fully active; external channels should consume queued targets only after a school configures and validates its provider.

## Verification

Run:

```text
php tests/demo_accounts.php
php tests/cbt_demo_data.php
php tests/cbt_module_test.php
php tests/cbt_score_transfer_test.php
php tests/cbt_security_test.php
php tests/http_portal_audit.php
php tests/link_audit.php
```

The demo fixtures are named `codex_demo_*` and the CBT assignments are learner-specific, so no real class is enrolled in a demonstration assessment.
