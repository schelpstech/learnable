<?php
include '../../app/query.php';
if(!isset($_SESSION['active'])){
    $model->redirect('../index.php');
}
?>
<!DOCTYPE html>
<html lang="zxx">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title><?php echo $sch_details['schname'] ?> :: LearnAble</title>
    <link rel="icon" href="../../asset/img/school/<?php echo $sch_details['logo'] ?>" type="image/jpg">
    <link rel="stylesheet" href="../../asset/css/bootstrap1.min.css" />

    <link rel="stylesheet" href="../../asset/vendors/themefy_icon/themify-icons.css" />

    <link rel="stylesheet" href="../../asset/vendors/niceselect/css/nice-select.css" />

    <link rel="stylesheet" href="../../asset/vendors/owl_carousel/css/owl.carousel.css" />

    <link rel="stylesheet" href="../../asset/vendors/gijgo/gijgo.min.css" />

    <link rel="stylesheet" href="../../asset/vendors/font_awesome/css/all.min.css" />
    <link rel="stylesheet" href="../../asset/vendors/tagsinput/tagsinput.css" />

    <link rel="stylesheet" href="../../asset/vendors/datepicker/date-picker.css" />
    <link rel="stylesheet" href="../../asset/vendors/vectormap-home/vectormap-2.0.2.css" />

    <link rel="stylesheet" href="../../asset/vendors/scroll/scrollable.css" />

    <link rel="stylesheet" href="../../asset/vendors/datatable/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="../../asset/vendors/datatable/css/responsive.dataTables.min.css" />
    <link rel="stylesheet" href="../../asset/vendors/datatable/css/buttons.dataTables.min.css" />

    <link rel="stylesheet" href="../../asset/vendors/text_editor/summernote-bs4.css" />

    <link rel="stylesheet" href="../../asset/vendors/morris/morris.css">

    <link rel="stylesheet" href="../../asset/vendors/material_icon/material-icons.css" />

    <link rel="stylesheet" href="../../asset/css/metisMenu.css">

    <link rel="stylesheet" href="../../asset/css/style1.css" />
    <link rel="stylesheet" href="../../asset/css/colors/default.css" id="colorSkinCSS">
    <?php
    if ($_SESSION['user_type'] === "Instructor") {
        echo '<script src="../../asset/js/auxilliary.js"></script>';
    }
    ?>
</head>