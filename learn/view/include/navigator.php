<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">

        <div class="row">
            <div class="col-12">
                <div class="page_title_box d-flex flex-wrap align-items-center justify-content-between">
                    <div class="page_title_left">
                        <h3 class="f_s_25 f_w_700 dark_text">LearnAble</h3>
                        <ol class="breadcrumb page_bradcam mb-0">
                            <li class="breadcrumb-item"><a href="../../app/router.php?pageid=index">Home</a></li>
                            <li class="breadcrumb-item"><a href="
                                <?php
                                $previous = "javascript:history.go(-1)";
                                if (isset($_SERVER['HTTP_REFERER'])) {
                                    $previous = $_SERVER['HTTP_REFERER'];
                                }
                                echo $previous;
                                ?>">Back</a></li>
                            <li class="breadcrumb-item active"><?php echo $_SESSION['user_type']; ?> Portal</li>
                        </ol>
                    </div>
                    <div class="page_title_right">
                            <?php
                            if ($_SESSION['user_type'] === "Instructor") {
                                echo '
                                    <div class="page_date_button" data-bs-toggle="modal" data-bs-target="#resources">    
                                        Add Learning resources :'.$active_term['term'];
                            } elseif ($_SESSION['user_type'] === "Learner") {

                                echo '
                                <div class="page_date_button">
                                    Active Term :'.$active_term['term']. ' :: '. $learner_class['classname'];
                            }
                            ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>