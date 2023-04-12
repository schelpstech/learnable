<?php
$count = 1;
if (!empty($available_result)) {
    foreach ($available_result as $view_result) {
        if ($view_result['status'] == 1) {
            $icon =  '<i class="far fa-id-card" style="font-size:108px; color:green; text-align:center;"></i>';
            $status = 'Published';
            $link = '<a href="../../app/router.php?pageid=result&ref=' . $view_result['term'] . '" class="btn_2" style=" text-align:center;">View ' . $view_result['term'] . ' Result</a>';
        } elseif ($view_result['status'] == 0) {
            $icon =  '<i class="fas fa-eye-slash" style="font-size:108px; color:red; text-align:center;"></i>';
            $status = 'Pending';
            $link = '<a href="#" class="btn_2" style=" text-align:center;"><small>' . $view_result['term'] . ' result is not available</small></a>';
        }
?>
        <div class="col-md-3">
            <div class="white_card position-relative mb_20 ">
                <div class="card-body">
                    <div class="ribbon1 rib1-primary"><span class="text-white text-center rib1-primary"><?php echo $count++ ?></span></div>
                    <?php echo $icon ?>
                    <div class="row my-4">
                        <div class="col">
                            <span class="badge_btn_3  mb-1">Term</span>
                        </div>
                        <div class="col-auto">
                            <h4 class="text-dark mt-0"><small class="text-muted font-14"><?php echo ucwords($view_result['term']) ?> </small></h4>
                        </div>
                        <div class="col">
                            <span class="badge_btn_3  mb-1">Status</span>
                        </div>
                        <div class="col-auto">
                            <h4 class="text-dark mt-0"><small class="text-muted font-14"><?php echo ucwords($status) ?> </small></h4>
                        </div>
                    </div>
                    <div class="d-grid">
                        <?php echo $link ?>
                    </div>
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
                        <h4 class="text-dark mt-0">No Result Published Yet </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>';
}
?>