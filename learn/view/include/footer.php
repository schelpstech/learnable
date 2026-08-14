<div class="footer_part">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="footer_iner text-center">
                    <p>
                        <?php
                        $schoolWebsite = filter_var($sch_details['website'] ?? '', FILTER_VALIDATE_URL)
                            ? $sch_details['website']
                            : rtrim((string) app_env('APP_URL', '/'), '/');
                        ?>
                        <a href="<?php echo htmlspecialchars($schoolWebsite, ENT_QUOTES, 'UTF-8'); ?>">LearnAble v 1.1 :: <?php echo date("Y") ?> &copy; :: developed for <?php echo htmlspecialchars($sch_details['schname'], ENT_QUOTES, 'UTF-8'); ?></a>
                        <a href="https://schelps.com.ng"> by SCHELPS</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../../asset/js/jquery1-3.4.1.min.js"></script>

<script src="../../asset/js/popper1.min.js"></script>

<script src="../../asset/js/bootstrap1.min.js"></script>

<script src="../../asset/js/metisMenu.js"></script>

<script src="../../asset/vendors/count_up/jquery.waypoints.min.js"></script>

<script src="../../asset/vendors/chartlist/Chart.min.js"></script>

<script src="../../asset/vendors/count_up/jquery.counterup.min.js"></script>

<script src="../../asset/vendors/niceselect/js/jquery.nice-select.min.js"></script>

<script src="../../asset/vendors/owl_carousel/js/owl.carousel.min.js"></script>

<script src="../../asset/vendors/datatable/js/jquery.dataTables.min.js"></script>
<script src="../../asset/vendors/datatable/js/dataTables.responsive.min.js"></script>
<script src="../../asset/vendors/datatable/js/dataTables.buttons.min.js"></script>
<script src="../../asset/vendors/datatable/js/buttons.flash.min.js"></script>
<script src="../../asset/vendors/datatable/js/jszip.min.js"></script>
<script src="../../asset/vendors/datatable/js/pdfmake.min.js"></script>
<script src="../../asset/vendors/datatable/js/vfs_fonts.js"></script>
<script src="../../asset/vendors/datatable/js/buttons.html5.min.js"></script>
<script src="../../asset/vendors/datatable/js/buttons.print.min.js"></script>

<script src="../../asset/vendors/datepicker/datepicker.js"></script>
<script src="../../asset/vendors/datepicker/datepicker.en.js"></script>
<script src="../../asset/vendors/datepicker/datepicker.custom.js"></script>
<script src="../../asset/js/chart.min.js"></script>
<script src="../../asset/vendors/chartjs/roundedBar.min.js"></script>

<script src="../../asset/vendors/progressbar/jquery.barfiller.js"></script>

<script src="../../asset/vendors/tagsinput/tagsinput.js"></script>
<script src="../../asset/vendors/text_editor/summernote-bs4.js"></script>
<script src="../../asset/js/custom.js"></script>
<script src="../../asset/js/performance_chart.js"></script>
</body>

</html>
