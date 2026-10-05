<?php
// The containing controller supplies the assessment, escaping helper, and CSRF token.
?>
<?php if (in_array($assessment['status'], array('draft', 'pending_approval', 'paused'), true) && (int) $assessment['attempt_count'] === 0): ?>
<section class="cbt-board">
<details><summary>Edit assessment settings</summary>
<form method="post" action="<?php echo $settingsH($settingsAction); ?>" class="cbt-form">
<input type="hidden" name="csrf_token" value="<?php echo $settingsH($settingsCsrf); ?>">
<input type="hidden" name="cbt_action" value="update_assessment">
<input type="hidden" name="assessment_id" value="<?php echo (int) $assessment['id']; ?>">
<div class="cbt-form-grid">
<label class="cbt-field-wide"><span>Title</span><input name="title" maxlength="190" required value="<?php echo $settingsH($assessment['title']); ?>"></label>
<label class="cbt-field-wide"><span>Instructions</span><textarea name="instructions" maxlength="5000" rows="3"><?php echo $settingsH($assessment['instructions']); ?></textarea></label>
<?php foreach (array('assessment_type' => CbtService::assessmentTypes(), 'result_treatment' => CbtService::resultTreatments(), 'navigation_mode' => array('free', 'linear')) as $key => $options): ?>
<label><span><?php echo $settingsH(ucwords(str_replace('_', ' ', $key))); ?></span><select name="<?php echo $key; ?>"><?php foreach ($options as $option): ?><option value="<?php echo $settingsH($option); ?>" <?php echo $assessment[$key] === $option ? 'selected' : ''; ?>><?php echo $settingsH(ucwords(str_replace('_', ' ', $option))); ?></option><?php endforeach; ?></select></label>
<?php endforeach; ?>
<label><span>Pass mark (paper total: <?php echo $settingsH($assessment['total_marks']); ?>)</span><input name="pass_mark" type="number" min="0" max="<?php echo $settingsH($assessment['total_marks']); ?>" step="0.25" value="<?php echo $settingsH($assessment['pass_mark']); ?>" required></label>
<?php foreach (array('start_at' => 'Opens', 'close_at' => 'Closes') as $key => $label): ?>
<label><span><?php echo $label; ?></span><input name="<?php echo $key; ?>" type="datetime-local" value="<?php echo date('Y-m-d\TH:i', strtotime($assessment[$key])); ?>" required></label>
<?php endforeach; ?>
<label><span>Time allowed (minutes)</span><input name="duration_minutes" type="number" min="1" max="720" value="<?php echo (int) $assessment['duration_minutes']; ?>" required></label>
<label><span>Attempts permitted</span><input name="max_attempts" type="number" min="1" max="5" value="<?php echo (int) $assessment['max_attempts']; ?>" required></label>
</div><div class="cbt-check-grid">
<?php foreach (array('allow_backtrack' => 'Allow return to earlier questions', 'randomize_questions' => 'Randomize question order', 'shuffle_options' => 'Shuffle answer options', 'show_score' => 'Show score after publication', 'allow_review' => 'Allow script review', 'show_correct_answers' => 'Release correct answers after closing', 'late_entry' => 'Allow late entry', 'late_submission' => 'Allow attempts to finish after closing', 'fullscreen_mode' => 'Offer fullscreen mode', 'monitor_tab_switch' => 'Record tab switches', 'restrict_clipboard' => 'Restrict copying') as $key => $label): ?>
<label><input type="hidden" name="<?php echo $key; ?>" value="0"><input type="checkbox" name="<?php echo $key; ?>" value="1" <?php echo (int) $assessment[$key] ? 'checked' : ''; ?>><span><?php echo $label; ?></span></label>
<?php endforeach; ?>
</div><p>Questions determine the total marks. Settings lock when the first student starts.</p>
<button type="submit" class="cbt-btn cbt-btn--primary">Save settings</button>
</form></details></section>
<?php endif; ?>
