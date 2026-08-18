<form method="post" action="../../app/cbt_action.php" class="cbt-form cbt-question-form" data-cbt-question-form>
    <input type="hidden" name="csrf_token" value="<?php echo cbt_h($cbtCsrf); ?>">
    <input type="hidden" name="cbt_action" value="create_question">
    <?php if (!empty($assessment)): ?><input type="hidden" name="assessment_id" value="<?php echo (int) $assessment['id']; ?>"><?php endif; ?>
    <input type="hidden" name="class_id" value="<?php echo (int) $questionClassId; ?>">
    <input type="hidden" name="subject_id" value="<?php echo (int) $questionSubjectId; ?>">
    <?php if (!empty($questionSchemeOptions)): ?>
        <label class="cbt-context-topic"><span>Scheme-of-work topic</span><select name="scheme_id" required><?php foreach ($questionSchemeOptions as $schemeOption): ?><option value="<?php echo (int) $schemeOption['id']; ?>" <?php echo (int) $questionSchemeId === (int) $schemeOption['id'] ? 'selected' : ''; ?>><?php echo cbt_h($schemeOption['week'] . ' · ' . $schemeOption['topic']); ?></option><?php endforeach; ?></select></label>
    <?php else: ?>
        <input type="hidden" name="scheme_id" value="<?php echo (int) $questionSchemeId; ?>">
    <?php endif; ?>
    <input type="hidden" name="status" value="active">
    <div class="cbt-form-grid cbt-form-grid--3">
        <label><span>Question type</span><select name="question_type" data-cbt-question-type required><?php foreach (CbtService::questionTypes() as $type): ?><option value="<?php echo cbt_h($type); ?>"><?php echo cbt_h(cbt_label($type)); ?></option><?php endforeach; ?></select></label>
        <label><span>Difficulty</span><select name="difficulty"><option value="easy">Easy</option><option value="medium" selected>Medium</option><option value="hard">Challenging</option></select></label>
        <label><span>Marks</span><input type="number" name="marks" min="0.25" max="1000" step="0.25" value="1" required></label>
        <label><span>Negative marks</span><input type="number" name="negative_marks" min="0" max="1000" step="0.25" value="0"></label>
        <label><span>Bank visibility</span><select name="visibility"><option value="private">Only me</option><option value="school">School question bank</option></select></label>
        <label class="cbt-check-inline"><input type="checkbox" name="allow_partial" value="1"><span>Allow partial marks</span></label>
        <label class="cbt-field-wide"><span>Learning objective</span><input type="text" name="learning_objective" maxlength="255" placeholder="What should a learner demonstrate?"></label>
        <label class="cbt-field-wide"><span>Question</span><textarea name="prompt_html" rows="5" maxlength="20000" required placeholder="Write the question clearly. Simple tables, lists, subscripts and superscripts are supported."></textarea></label>
        <label><span>Optional media type</span><select name="media_type"><option value="">No media</option><option value="image">Image or diagram</option><option value="audio">Audio</option><option value="video">Video</option></select></label>
        <label class="cbt-field-grow"><span>Media URL or local path</span><input type="text" name="media_url" maxlength="500" placeholder="https://... or assets/uploads/..."></label>
    </div>

    <div class="cbt-answer-panel" data-cbt-options-panel>
        <div class="cbt-answer-panel__heading"><h3>Answer options</h3><p>Use the radio for one correct answer or check every correct answer for multiple choice. For matching, enter the matching value. For ordering, enter 1, 2, 3… in the final column.</p></div>
        <div class="cbt-option-table">
            <div class="cbt-option-row cbt-option-row--head"><span>Single</span><span>Multiple</span><span>Option or item</span><span>Match / order</span></div>
            <?php for ($optionIndex = 0; $optionIndex < 6; $optionIndex++): ?>
                <div class="cbt-option-row"><label><input type="radio" name="correct_option" value="<?php echo $optionIndex; ?>"><span class="sr-only">Correct single answer</span></label><label><input type="checkbox" name="correct_options[]" value="<?php echo $optionIndex; ?>"><span class="sr-only">Correct multiple answer</span></label><input type="text" name="option_text[]" maxlength="5000" placeholder="Option <?php echo chr(65 + $optionIndex); ?>"><input type="text" name="match_key[]" maxlength="64" placeholder="Target or <?php echo $optionIndex + 1; ?>"></div>
            <?php endfor; ?>
        </div>
    </div>
    <div class="cbt-answer-panel" data-cbt-true-panel hidden><label><span>Correct answer</span><select name="true_false_answer"><option value="true">True</option><option value="false">False</option></select></label></div>
    <div class="cbt-answer-panel" data-cbt-text-panel hidden><label><span>Accepted answer variations</span><textarea name="accepted_answer" rows="4" placeholder="Enter one acceptable answer per line"></textarea></label></div>
    <div class="cbt-form-grid">
        <label class="cbt-field-wide"><span>Model answer (kept from learners until released)</span><textarea name="model_answer" rows="3" maxlength="20000"></textarea></label>
        <label class="cbt-field-wide"><span>Marking guide</span><textarea name="marking_guide" rows="3" maxlength="20000"></textarea></label>
        <label class="cbt-field-wide"><span>Answer explanation</span><textarea name="explanation" rows="3" maxlength="20000"></textarea></label>
    </div>
    <footer class="cbt-form-footer"><span>Question snapshots preserve completed scripts even if the bank copy changes later.</span><button class="cbt-btn cbt-btn--primary" type="submit">Save question<?php echo !empty($assessment) ? ' & add to paper' : ''; ?></button></footer>
</form>
