<?php
$count = 1;
if (!empty($list_task)) {
    foreach ($list_task as $list_task) {
        if ($list_task['type'] == 'text') {
            $icon =  '<i class="far fa-file-word" style="font-size:108px; color:blue; text-align:center;"></i>';
        } elseif ($list_task['type'] == 'online') {
            $icon =  '<i class="fas fa-globe" style="font-size:108px; color:blue; text-align:center;"></i>';
        } elseif ($list_task['type'] == 'file') {
            $icon =  '<i class="fas fa-file-pdf" style="font-size:108px; color:blue; text-align:center;"></i>';
        } elseif ($list_task['type'] == 'media') {
            $icon =  '<i class="far fa-file-image" style="font-size:108px; color:blue; text-align:center;"></i>';
        }
?>
        <div class="col-md-3">
            <div class="white_card position-relative mb_20 ">
                <div class="card-body">
                    <div class="ribbon1 rib1-primary"><span class="text-white text-center rib1-primary"><?php echo $count++ ?></span></div>
                    <?php echo $icon ?>
                    <div class="row my-4">
                        <div class="col">
                            <span class="badge_btn_3  mb-1">Topic</span>
                            <a href="#" class="f_w_400 color_text_3 f_s_14 d-block"><?php echo ucwords($list_task['topic']) ?></a>
                        </div>
                        <div class="col-auto">
                            <h4 class="text-dark mt-0"><small class="text-muted font-14"><?php echo ucwords($list_task['week']) ?> </small></h4>
                        </div>
                    </div>
                    <div class="row my-4">
                        <div class="col">
                            <strong><span>Start date :</strong><br> <?php echo $list_task['rectime'] ?></span>
                        </div>
                        <div class="col">
                            <strong><span>Due date :</strong> <br><?php echo $list_task['deadline'] ?></span>
                        </div>
                        <div class="col">
                            <strong><span>Grade : </strong><br><?php echo $list_task['grade'] ?> marks</span>
                        </div>

                    </div>
                    <div class="d-grid">
                        <a href="../../app/router.php?pageid=task&ref=<?php echo $list_task['questid'] ?>" class="btn_3" style=" text-align:center;">View this Assignment</a>
                    </div>
                    <?php
                    if ((isset($_SESSION['user_type']) && $_SESSION['user_type'] === "Instructor")) {
                        echo '
                            <div class="d-grid">
                                <a href="../../app/router.php?pageid=resources&item=modify_task&item_ref=' . $list_task['questid'] . '" class="btn_2" style=" text-align:center;">Modify this Assignment</a>
                                </a>
                             </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
<?php
    }
} else {
    echo '    <div class="col-md-3">
        <div class="white_card position-relative mb_20 ">
            <div class="card-body">
            <i class="fas fa-ban " style="font-size:108px; color:red; text-align: center;"></i>
                <div class="row my-4">
                   <div class="col-auto">
                        <h4 class="text-dark mt-0">No Assessment created for this subject Yet </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>';
}
?>