<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">

        <div class="row">
            <div class="col-12">
                <div class="page_title_box d-flex flex-wrap align-items-center justify-content-between">
                    <div class="page_title_left">
                        <h3 class="f_s_25 f_w_700 dark_text">LearnAble</h3>
                        <ol class="breadcrumb page_bradcam mb-0">
                            <li class="breadcrumb-item"><a href="../../app/router.php?pageid=index">Home</a></li>
                            <li class="breadcrumb-item"><button type="button" class="btn btn-link p-0" onclick="history.back()">Back</button></li>
                            <li class="breadcrumb-item active"><?php echo $_SESSION['user_type']; ?> Portal</li>
                        </ol>
                    </div>
                    <div class="page_title_right">
                        <?php if ($_SESSION['user_type'] === 'Instructor'): ?>
                            <button type="button" class="page_date_button border-0" data-bs-toggle="modal" data-bs-target="#resources" aria-label="Add teaching materials for <?php echo htmlspecialchars($active_term['term'], ENT_QUOTES, 'UTF-8'); ?>">
                                Add teaching materials · <?php echo htmlspecialchars($active_term['term'], ENT_QUOTES, 'UTF-8'); ?>
                            </button>
                        <?php elseif ($_SESSION['user_type'] === 'Learner'): ?>
                            <div class="page_date_button">
                                Active Term: <?php echo htmlspecialchars($active_term['term'], ENT_QUOTES, 'UTF-8'); ?> · <?php echo htmlspecialchars($learner_class['classname'], ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
