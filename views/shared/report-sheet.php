<?php
if (!isset($report) || !is_array($report)) {
    throw new RuntimeException('Report data is unavailable.');
}

if (!function_exists('report_escape')) {
    function report_escape($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('report_number')) {
    function report_number($value, $decimals = 1)
    {
        return number_format((float) $value, $decimals, '.', '');
    }
}

$subjectCount = count($report['cumulative'] ? $report['cumulative_subjects'] : $report['subjects']);
$density = $subjectCount > 20 ? ' report-density-ultra' : ($subjectCount > 15 ? ' report-density-compact' : '');
$reportSheetId = isset($reportSheetId) && is_string($reportSheetId) && $reportSheetId !== '' ? $reportSheetId : 'report-sheet';
$reportShowToolbar = !isset($reportShowToolbar) || (bool) $reportShowToolbar;
$change = $report['change'];
$changeClass = $change === null ? 'is-neutral' : ($change > 0.05 ? 'is-up' : ($change < -0.05 ? 'is-down' : 'is-neutral'));
$changeLabel = $change === null ? 'First recorded term' : (($change > 0 ? '+' : '') . report_number($change) . ' pts vs previous');
$affectiveLabels = array('Leadership', 'Eloquence', 'Neatness', 'Creativity', 'Responsiveness');
?>

<?php if ($reportShowToolbar): ?>
<div class="report-toolbar report-no-print" aria-label="Report actions">
    <div>
        <span class="report-toolbar-kicker">Learner report</span>
        <strong><?php echo report_escape($report['learner']['name']); ?></strong>
        <span class="report-page-assurance"><i class="fa fa-check-circle" aria-hidden="true"></i> Optimised for one A4 page</span>
    </div>
    <div class="report-toolbar-actions">
        <button type="button" data-report-print><i class="fa fa-print" aria-hidden="true"></i> Print / Save PDF</button>
        <button type="button" class="is-primary" data-report-download data-report-target="<?php echo report_escape($reportSheetId); ?>"><i class="fa fa-download" aria-hidden="true"></i> Download PDF</button>
    </div>
</div>
<?php endif; ?>

<article class="report-sheet<?php echo $density; ?>" id="<?php echo report_escape($reportSheetId); ?>" data-report-file="<?php echo report_escape($report['file_name']); ?>">
    <div class="report-sheet-content">
        <header class="report-head">
            <img class="report-school-logo" src="<?php echo report_escape($report['school']['logo_url']); ?>" alt="<?php echo report_escape($report['school']['name']); ?> logo">
            <div class="report-school-copy">
                <span class="report-document-label">Academic performance report</span>
                <h1><?php echo report_escape($report['school']['name']); ?></h1>
                <p><?php echo report_escape($report['school']['address']); ?></p>
                <small><?php echo report_escape(trim($report['school']['phone'] . ' · ' . $report['school']['email'], ' ·')); ?></small>
            </div>
            <img class="report-passport" src="<?php echo report_escape($report['learner']['passport_url']); ?>" alt="<?php echo report_escape($report['learner']['name']); ?> passport">
        </header>

        <section class="report-identity" aria-label="Learner and report details">
            <div class="report-learner-name"><span>Learner</span><strong><?php echo report_escape($report['learner']['name']); ?></strong></div>
            <dl><div><dt>Learner ID</dt><dd><?php echo report_escape($report['learner']['id']); ?></dd></div><div><dt>Class</dt><dd><?php echo report_escape($report['learner']['class_name']); ?></dd></div></dl>
            <dl><div><dt>Term</dt><dd><?php echo report_escape($report['term']); ?></dd></div><div><dt>Session</dt><dd><?php echo report_escape($report['session']); ?></dd></div></dl>
            <div class="report-generated"><span>Generated</span><strong><?php echo report_escape($report['generated_at']); ?></strong></div>
        </section>

        <section class="report-metrics" aria-label="Performance summary">
            <div><span>Overall average</span><strong><?php echo report_number($report['summary']['average']); ?><small>%</small></strong><em class="<?php echo $changeClass; ?>"><?php echo report_escape($changeLabel); ?></em></div>
            <div><span>Overall grade</span><strong><?php echo report_escape($report['summary']['grade']); ?></strong><em><?php echo report_escape($report['summary']['subject_count']); ?> subjects assessed</em></div>
            <div><span>Attendance</span><strong><?php echo (int) ($report['affective']['total_present'] ?? 0); ?><small>/<?php echo (int) $report['config']['school_open']; ?></small></strong><em>days present</em></div>
        </section>

        <?php if (!empty($report['history'])): ?>
            <section class="report-progress" aria-labelledby="progress-title">
                <div class="report-section-title"><h2 id="progress-title">Progress across terms</h2><span>Average score trend</span></div>
                <table>
                    <thead><tr><?php foreach ($report['history'] as $history): ?><th<?php echo $history['term'] === $report['term'] ? ' class="is-current"' : ''; ?>><?php echo report_escape($history['term']); ?></th><?php endforeach; ?></tr></thead>
                    <tbody><tr><?php foreach ($report['history'] as $history): $bar = max(4, min(100, (float) $history['average'])); ?><td<?php echo $history['term'] === $report['term'] ? ' class="is-current"' : ''; ?>><strong><?php echo report_number($history['average']); ?>%</strong><span><i style="width:<?php echo report_number($bar, 0); ?>%"></i></span></td><?php endforeach; ?></tr></tbody>
                </table>
            </section>
        <?php endif; ?>

        <section class="report-results" aria-labelledby="results-title">
            <div class="report-section-title"><h2 id="results-title"><?php echo $report['cumulative'] ? 'Cumulative academic performance' : 'Academic performance'; ?></h2><span>Scores are shown as percentages</span></div>
            <table>
                <thead>
                    <?php if ($report['cumulative']): ?>
                        <tr><th class="report-col-number">#</th><th>Subject</th><?php foreach ($report['cumulative_terms'] as $termColumn): ?><th><?php echo report_escape(preg_replace('/\s+20\d{2}\s*\/\s*20\d{2}$/', '', $termColumn)); ?></th><?php endforeach; ?><th>Cum. avg</th><th>Remark</th></tr>
                    <?php else: ?>
                        <tr><th class="report-col-number">#</th><th>Subject</th><th>CA<br><small>/<?php echo (int) $report['config']['ca_score']; ?></small></th><th>Exam<br><small>/<?php echo (int) $report['config']['exam_score']; ?></small></th><th>Total<br><small>/100</small></th><th>Grade</th><th>Class avg</th><th>Remark</th></tr>
                    <?php endif; ?>
                </thead>
                <tbody>
                    <?php if ($report['cumulative']): ?>
                        <?php foreach ($report['cumulative_subjects'] as $index => $subject): ?>
                            <tr><td><?php echo $index + 1; ?></td><td><?php echo report_escape($subject['subject_name']); ?></td><?php foreach ($report['cumulative_terms'] as $termColumn): ?><td><?php echo isset($subject['scores'][$termColumn]) ? report_number($subject['scores'][$termColumn], 0) : '—'; ?></td><?php endforeach; ?><td class="is-total"><?php echo report_number($subject['average']); ?></td><td><span class="report-remark"><?php echo report_escape($subject['remark']); ?></span></td></tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php foreach ($report['subjects'] as $index => $subject): ?>
                            <tr><td><?php echo $index + 1; ?></td><td><?php echo report_escape($subject['subject_name']); ?></td><td><?php echo report_number($subject['score'], 0); ?></td><td><?php echo report_number($subject['examscore'], 0); ?></td><td class="is-total"><?php echo report_number($subject['totalscore'], 0); ?></td><td><strong><?php
                                $score = (float) $subject['totalscore'];
                                echo $score >= 75 ? 'A' : ($score >= 65 ? 'B' : ($score >= 50 ? 'C' : ($score >= 45 ? 'D' : ($score >= 40 ? 'E' : 'F'))));
                            ?></strong></td><td><?php echo report_number($subject['average_score']); ?></td><td><span class="report-remark"><?php echo report_escape($subject['remark']); ?></span></td></tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if ($subjectCount === 0): ?><tr><td colspan="9" class="report-empty">No published scores are available for this term.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </section>

        <section class="report-closing">
            <div class="report-observation">
                <div class="report-section-title"><h2>Development & observation</h2><span>5 is the highest rating</span></div>
                <div class="report-ratings">
                    <?php foreach ($affectiveLabels as $index => $label): ?>
                        <div><span><?php echo report_escape($label); ?></span><strong><?php echo (int) ($report['affective']['rating' . ($index + 1)] ?? 0); ?><small>/5</small></strong></div>
                    <?php endforeach; ?>
                </div>
                <p><strong>Teacher's comment:</strong> <?php echo report_escape($report['affective']['comment'] ?? $report['summary']['remark']); ?></p>
            </div>
            <div class="report-summary-note">
                <span>Performance insight</span>
                <strong><?php echo report_escape($report['summary']['remark']); ?></strong>
                <dl><div><dt>CA avg</dt><dd><?php echo report_number($report['summary']['ca_average']); ?>%</dd></div><div><dt>Exam avg</dt><dd><?php echo report_number($report['summary']['exam_average']); ?>%</dd></div><div><dt>Next term</dt><dd><?php echo $report['config']['resumption'] ? report_escape(date('j M Y', strtotime($report['config']['resumption']))) : 'To be announced'; ?></dd></div></dl>
            </div>
        </section>

        <footer class="report-signatures">
            <div><span><?php echo report_escape($report['teacher_name'] ?: 'Class teacher'); ?></span><i></i><small>Class teacher</small></div>
            <p><?php echo report_escape($report['school']['motto']); ?></p>
            <div><span>School administration</span><i></i><small>Authorised signature</small></div>
        </footer>
    </div>
</article>
