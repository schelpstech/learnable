# School website and profile

The public `index.php` reads the first `lhpschool` record (ordered by `schid`). School Profile is available from the administrator sidebar. Name, motto, logo, founding year and contact details are reused; the proprietor stays private. The four website content additions and the separate WhatsApp number are optional. A WhatsApp-enabled number must include its international country code, for example +2348012345678; leaving it blank hides the floating chat button. The ordinary school phone number is never used as a WhatsApp fallback. Empty content sections are hidden, and a missing photograph uses the existing classroom image.

## Deployment

1. Back up `lhpschool` and `learn/asset/img/school/` using the normal deployment backup procedure.
2. Run `php database/migrate.php` before deploying the updated profile editor. The tracked `20260926_school_website` migration adds four fields without rewriting existing values. `20260927_school_whatsapp` adds the separate, initially blank WhatsApp number. Run both migrations on each deployment database.
3. Deploy `index.php`, `css/landing-modern.css`, `classes/SchoolProfile.php`, `admin/profile.php`, `admin/schprofile.php`, and `assets/css/school-profile.css` together.
4. Ensure PHP can write to `learn/asset/img/school/`. JPG and PNG uploads use unique names and are validated as images; the displayed limit respects PHP's upload limit, capped at 6 MB per image. PHP's `post_max_size` must accommodate the combined size if uploading both logo and photograph together.
5. Open School Profile, add the desired website content, save, and use **View school website**.

The public page reads without making schema changes and continues to show portal entrances if the database is unavailable. Existing learner, staff and administrator routes are preserved.

## Gallery, events and announcements

Use **Workspace → School website**, or **Gallery, events & announcements** from School Profile. Website publishing follows the same administrator-only access as School Profile. Registry and Bursary accounts cannot open the editor or submit its endpoint. Public website records are separate from private portal notices, learner information and the academic calendar.

- **Gallery:** upload a JPG/PNG and optionally add a caption. Publish it to include it in the latest 30 photographs. The slider advances every 10 seconds, loops, and includes previous/next and play/pause controls. Automatic motion pauses for hover, keyboard focus and hidden tabs; reduced-motion preferences start it paused. A single photo needs no slideshow controls, and photos remain visible without JavaScript.
- **Events:** add a title and event date, with optional venue/details. The public page shows the next six upcoming events (including today's events). Past events remain in the editor for records and can be edited or removed.
- **Announcements:** add a title and message, with an optional last visible date. Up to six recent published announcements appear. Expiry includes the selected date and follows Africa/Lagos calendar dates.

Uncheck **Publish on the school website** to save a draft or hide an existing item. Use **Edit** to change it, or **Remove → Confirm removal** to delete it. Replacing/removing a gallery photo deletes only its generated gallery image; profile logos and the main school photograph are preserved. No sample content is installed. Empty public sections and their navigation links are hidden.

Deployment: back up `school_website_posts` (once installed) and `learn/asset/img/school/`, then run `php database/migrate.php` to apply the additive `20261022_school_website_content.sql` migration. Deploy `classes/SchoolWebsite.php`, `admin/website.php`, the updated profile/navigation/routes, `index.php`, both updated stylesheets and `assets/js/school-website.js` together. Gallery uploads reuse the profile's validated image handling and its per-file PHP size limit. Missing optional content tables do not interrupt public portal access; the administrator editor explains when the migration is needed.

To roll back this feature, restore these application files together and leave the additive table and uploaded photos in place for a later redeployment.

## Validation

- `php -l index.php`
- `php -l admin/profile.php`
- `php -l admin/schprofile.php`
- `php -l classes/SchoolProfile.php`
- `php tests/school_profile_http_test.php`
- `php tests/school_website_http_test.php`
- `node tests/school_website_slider_test.js`

The HTTP test requires the existing `codex_demo_admin` and `E2E_DEMO_PASSWORD`, and refuses non-local APP_URL hosts. It temporarily changes the school website fields and restores the original row in a `finally` block; run it on local development data, not during concurrent profile editing. It covers authenticated saving, CSRF, upload validation, rendering, optional-section visibility and portal entrances.

The content HTTP test creates only uniquely named QA website records, photos and staff accounts, then removes those fixtures in `finally`; it leaves the existing school profile untouched. For a local visual check, `--preview` retains only the owned website fixtures temporarily. Run `php tests/school_website_http_test.php --cleanup-preview` afterwards; its guarded manifest is stored at `tasks/_website_preview.json`. The slider test checks the 10-second interval, looping, manual navigation, pause states, reduced motion and single-photo behavior with simulated timers.

## Rollback

Restore the previous application files. Leave the additive database columns in place so saved website content remains available if the update is redeployed. Older images are retained when replaced or removed from the page; a normal backup policy can manage unused assets separately.
