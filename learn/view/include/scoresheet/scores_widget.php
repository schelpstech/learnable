<div class="white_card card_height_100 mb_30 social_media_card">
    <div class="white_card_header">
        <div class="main-title">
            <h3 class="m-0"> Score Sheets </h3>
        </div>
    </div>
    <div class="media_thumb ml_25">
        <img src="../../asset/img/media.svg" alt="">
    </div>
    <div class="media_thumb ml_25" id="loader" style="display: none;">
        <img src="../../asset/img/app/giphy.gif" alt="">
    </div>
    <div class="media_card_body" id="board">
        <div class="media_card_list">
            <div class="single_media_card" onclick="ca_score_manager()">
                <span>CA Scoresheet</span>
                <h3><?php echo $scores_recorded['ca_score'] ?? 0; ?> / <?php echo $class_population ?? 0; ?> </h3>
            </div>
            <div class="single_media_card" onclick="exam_score_manager()">
                <span>Exam Scoresheet </span>
                <h3><?php echo $scores_recorded['exam_score'] ?? 0; ?> / <?php echo $class_population ?? 0; ?></h3>
            </div>
            <div class="single_media_card" onclick="weekly_score_manager()">
                <span>Weekly Assessment</span>
                <h3><?php echo $scores_recorded['week_score'] ?? 0; ?> / <?php echo $class_population ?? 0; ?></h3>
            </div>
            <div class="single_media_card" onclick="total_score_manager()">
                <span>Cumulative Scoresheet</span>
                <h3><?php echo $scores_recorded['total_score'] ?? 0; ?> / <?php echo $class_population ?? 0; ?></h3>
            </div>
        </div>
    </div>
</div>