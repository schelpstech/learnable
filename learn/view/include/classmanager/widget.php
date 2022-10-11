<div class="white_card card_height_100 mb_30 social_media_card">
    <div class="white_card_header">
        <div class="main-title">
            <h3 class="m-0"> Class Demography</h3>
        </div>
    </div>
    <div class="media_thumb ml_25">
        <img src="../../asset/img/media.svg" alt="">
    </div>
    <div class="media_card_body" id="board">
        <div class="media_card_list">
            <div class="single_media_card" onclick="show_learners()">
                <span>Class Population</span>
                <h3><?php echo $class_allocated['population'] ?? 0; ?></h3>
            </div>
            <div class="single_media_card" onclick="show_subjects()">
                <span>Subjects Offered </span>
                <h3><?php echo $class_allocated['subject'] ?? 0; ?></h3>
            </div>
            <div class="single_media_card" onclick="show_fully_paid()">
                <span>Paid in Full</span>
                <h3><?php echo $class_allocated['paid'] ?? 0; ?></h3>
            </div>
            <div class="single_media_card" onclick="show_fully_paid()">
                <span>Outstanding Balance</span>
                <h3><?php echo $class_allocated['debtor'] ?? 0; ?></h3>
            </div>
        </div>
    </div>
</div>