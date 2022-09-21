<div class="main_content_iner overly_inner ">
        <div class="container-fluid p-0 ">

            <div class="row">
                <div class="col-12">
                    <div class="page_title_box d-flex flex-wrap align-items-center justify-content-between">
                        <div class="page_title_left">
                            <h3 class="f_s_25 f_w_700 dark_text">LearnAble</h3>
                            <ol class="breadcrumb page_bradcam mb-0">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="
                                <?php 
                                $previous = "javascript:history.go(-1)";
                                if(isset($_SERVER['HTTP_REFERER'])) {
                                    $previous = $_SERVER['HTTP_REFERER'];
                                }
                                echo $previous;
                                ?>">Back</a></li>
                                <li class="breadcrumb-item active">Learner Portal</li>
                            </ol>
                        </div>
                        <div class="page_title_right">
                            <div class="page_date_button">
                                Active Term : <?php echo $active_term['term'] ?> - <?php echo $learner_class['classname'] ?? ' - '; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>