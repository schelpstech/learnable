<?php
$count = 1;
if (!empty($list_note)) {
    foreach ($list_note as $list_note) {
        if ($list_note['type'] == 'text') {
            $icon =  '<i class="far fa-file-word" style="font-size:108px; color:indigo; text-align:center;"></i>';
        } elseif ($list_note['type'] == 'online') {
            $icon =  '<i class="fas fa-globe" style="font-size:108px; color:indigo; text-align:center;"></i>';
        } elseif ($list_note['type'] == 'file') {
            $icon =  '<i class="fas fa-file-pdf" style="font-size:108px; color:indigo; text-align:center;"></i>';
        } elseif ($list_note['type'] == 'media') {
            $icon =  '<i class="far fa-file-image" style="font-size:108px; color:indigo; text-align:center;"></i>';
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
                            <a href="#" class="f_w_400 color_text_3 f_s_14 d-block"><?php echo ucwords($list_note['topic']) ?></a>
                        </div>
                        <div class="col-auto">
                            <h4 class="text-dark mt-0"><small class="text-muted font-14"><?php echo ucwords($list_note['week']) ?> </small></h4>
                        </div>
                    </div>
                    <div class="row my-4">
                        <div class="col">
                            <span>created:<?php echo $list_note['rectime'] ?></span>
                        </div>

                    </div>
                    <div class="d-grid">
                        <a href="../../app/router.php?pageid=note&ref=<?php echo $list_note['noteid'] ?>" class="btn_3" style=" text-align:center;">Read this e-Note</a>
                    </div>
                    <?php
                    if ((isset($_SESSION['user_type']) && $_SESSION['user_type'] === "Instructor")) {
                        echo '
                            <div class="d-grid">
                                <a href="../../app/router.php?pageid=resources&item=modify_note&item_ref='.$list_note['noteid'].'" class="btn_2" style=" text-align:center;">Modify this e-Note</a>
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
                        <h4 class="text-dark mt-0">No Note Created for this subject Yet</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>';
}
?>