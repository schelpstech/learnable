# Registry Admin and teacher CBT publishing

The main administrator selects **Registry Admin** in **Workspace → Staff** when creating or editing a staff account. The staff member signs in with the existing staff login or the learning portal login and opens the Registry workspace.

Registry Admin can manage student accounts, classes, subjects, subject allocation, terms, calendar, promotions, academic score sheets, result settings, academic records, affective records, mid-term results, reports, learning resources and CBT moderation. Financial routes and actions, the bursary portal, fee/payment/discount/expense/inventory records, whole-database backups, school profile settings and staff role management are blocked on the server, including old direct URLs. The Registry dashboard contains only academic counts and links. Role and account status are checked from the database on each request; role changes take effect for existing sessions.

The new **Academics → Scores** page supports CA/exam scores and weekly scores in the active term. It reuses existing limits, roster checks, concurrency detection, publication locks and score audit records. Reopen score entry through Result settings before changing published results. Blank fields preserve existing values; use zero to record a zero mark.

Teachers create a draft, add questions, then select **Publish assessment**. Eligible students see the assessment immediately and can attempt it during its scheduled window. Existing papers awaiting pre-approval can be published by their assigned teacher. Existing drafts and pending papers are not published automatically. Completed CBT results still require academic approval before publication or score transfer; both the main administrator and Registry Admin can review and approve completed results.

Deploy the changed PHP files, `admin/nav.html`, new shared classes, `config/staff_permissions.php`, new dashboard/score page and migration together. Run `php database/migrate.php` to apply `20261004_teacher_cbt_publishing.sql`; it only changes the default for new CBT drafts. The existing text staff-role column already supports `r`, so no staff-account schema change is needed.

To roll back: first change Registry Admin accounts to an appropriate existing staff role or disable them, then restore the previous code. Restore the assessment column default with `ALTER TABLE cbt_assessments ALTER COLUMN require_approval SET DEFAULT 1;`. Existing assessments and scores remain intact. A rollback must not rely on older code understanding the new `r` role.

Local verification: `php tests/registry_access_http_test.php`, `php tests/cbt_authoring_test.php` and `php tests/academic_creation_http_test.php`. The HTTP test uses temporary accounts and records and removes them in `finally`; the authoring test uses a disposable isolated database schema.
