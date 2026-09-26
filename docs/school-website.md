# School website and profile

The public `index.php` reads the first `lhpschool` record (ordered by `schid`). School Profile is available from the administrator sidebar. Name, motto, logo, founding year and contact details are reused; the proprietor stays private. The four website content additions and the separate WhatsApp number are optional. A WhatsApp-enabled number must include its international country code, for example +2348012345678; leaving it blank hides the floating chat button. The ordinary school phone number is never used as a WhatsApp fallback. Empty content sections are hidden, and a missing photograph uses the existing classroom image.

## Deployment

1. Back up `lhpschool` and `learn/asset/img/school/` using the normal deployment backup procedure.
2. Run `php database/migrate.php` before deploying the updated profile editor. The tracked `20260926_school_website` migration adds four fields without rewriting existing values. `20260927_school_whatsapp` adds the separate, initially blank WhatsApp number. Run both migrations on each deployment database.
3. Deploy `index.php`, `css/landing-modern.css`, `classes/SchoolProfile.php`, `admin/profile.php`, `admin/schprofile.php`, and `assets/css/school-profile.css` together.
4. Ensure PHP can write to `learn/asset/img/school/`. JPG and PNG uploads use unique names and are validated as images; the displayed limit respects PHP's upload limit, capped at 6 MB per image. PHP's `post_max_size` must accommodate the combined size if uploading both logo and photograph together.
5. Open School Profile, add the desired website content, save, and use **View school website**.

The public page reads without making schema changes and continues to show portal entrances if the database is unavailable. Existing learner, staff and administrator routes are preserved.

## Validation

- `php -l index.php`
- `php -l admin/profile.php`
- `php -l admin/schprofile.php`
- `php -l classes/SchoolProfile.php`
- `php tests/school_profile_http_test.php`

The HTTP test requires the existing `codex_demo_admin` and `E2E_DEMO_PASSWORD`, and refuses non-local APP_URL hosts. It temporarily changes the school website fields and restores the original row in a `finally` block; run it on local development data, not during concurrent profile editing. It covers authenticated saving, CSRF, upload validation, rendering, optional-section visibility and portal entrances.

## Rollback

Restore the previous application files. Leave the additive database columns in place so saved website content remains available if the update is redeployed. Older images are retained when replaced or removed from the page; a normal backup policy can manage unused assets separately.
