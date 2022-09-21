<?php
include '../app/query.php';
?>
<!DOCTYPE html>
<html lang="zxx">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title><?php echo $sch_details['schname'] ?> :: LearnAble</title>
    <link rel="icon" href="../asset/img/school/<?php echo $sch_details['logo'] ?>" type="image/jpg">
    <link rel="stylesheet" href="../asset/css/bootstrap1.min.css" />
    <link rel="stylesheet" href="../asset/vendors/morris/morris.css">
    <link rel="stylesheet" href="../asset/vendors/material_icon/material-icons.css" />
    <link rel="stylesheet" href="../asset/css/style1.css" />
    <link rel="stylesheet" href="../asset/css/colors/default.css" id="colorSkinCSS">
</head>
<body>
    <div class="erroe_page_wrapper">
        <div class="errow_wrap">
            <div class="container text-center">
                <img src="../asset/img/school/<?php echo $sch_details['logo'] ?>" alt="<?php echo $sch_details['schname'] ?>">

                <div class="col-md-8 offset-md-2 text-center">

                    <div class="col-lg-12">
                        <div class="white_box mb_30">
                            <div class="row justify-content-center">
                                <div class="col-lg-6">
                                    <?php
                                        if (isset($_SESSION['msg'])) {
                                            printf('<b>%s</b>', $_SESSION['msg']);
                                            unset($_SESSION['msg']);
                                        }
                                    ?>
                                    <div class="modal-content cs_modal">
                                        <div class="modal-header justify-content-center theme_bg_1">
                                            <h5 class="modal-title text_white">Log in</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="../app/useracces.php" method="POST" enctype="multipart/form-data">
                                                <div class="">
                                                    <input type="text" class="form-control" placeholder="Enter your username" name="userid" autocomplete="new-email" tabindex="1" required="yes" autofocus>
                                                </div>
                                                <div class="">
                                                    <input type="password" class="form-control" placeholder="Password" name="userpwd" autocomplete="new-userpwd" tabindex="2" required="yes">
                                                </div>
                                                <button name="log_in" type="submit" tabindex="3" class="btn_1 full_width text-center" value="Log in">Log in</button>

                                                <div class="text-center">
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#forgot_password" data-bs-dismiss="modal" class="pass_forget_btn">Forget Password?</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="error_btn  text-center">
                    <div class="col-lg-12">
                        <div class="footer_iner text-center">
                            <p>
                                <a href="<?php echo $sch_details['schname'] ?>">LearnAble v 1.1 :: <?php echo date("Y") ?> © :: developed for <?php echo $sch_details['schname'] ?></a>
                                <a href="https://learnable.schelps.com.ng"> by SCHELPS</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../asset/js/jquery1-3.4.1.min.js"></script>
    <script src="../asset/js/popper1.min.js"></script>
    <script src="../asset/js/bootstrap1.min.js"></script>

</body>
</html>