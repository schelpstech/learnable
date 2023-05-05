<div class="main_content_iner overly_inner" id="print_area">
    <div class="container-fluid p-0 ">

        <div class="row">
            <div class="col-12">
                <div class="page_title_box d-flex align-items-center justify-content-between">
                    <button class="btn_3 full_width text-center" onclick="printTable();">Print</button>
                    <div class="page_title_left">
                        <div class="card-profile"><img src="../../asset/img/school/<?php echo $sch_details['logo'] ?>"
                                style="display: block; margin-left: auto;margin-right: auto;" alt="" width="100"></div>
                        <h2 class="f_s_30 f_w_700 dark_text" style="text-align:center;">
                            <?php echo ucwords($sch_details['schname']) ?>
                        </h2>
                        <ol class="breadcrumb page_bradcam mb-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Address </a></li>
                            <li class="breadcrumb-item active">
                                <?php echo ucwords($sch_details['address']) ?>
                            </li>
                        </ol>
                        <ol class="breadcrumb page_bradcam mb-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Contact </a></li>
                            <li class="breadcrumb-item active">
                                <?php echo ucwords($sch_details['email'] . " | " . $sch_details['phone']) ?>
                            </li>
                        </ol>
                    </div>

                    <div class="page_title_left">
                        <div class="card-profile"><img class="rounded-circle" src="
                            <?php
                            if (isset($learner_profile['picture'])) {
                                echo '../../asset/img/passport/' . $learner_profile['picture'];
                            } else {
                                echo '../../asset/img/passport/nopix.jpg';
                            }
                            ?>" alt="" data-original-title="" title="" width="100"></div>
                        <h4 class="f_s_30 f_w_700 dark_text">
                            <?php echo ucwords($learner_profile['fname']) ?>
                        </h4>
                        <ol class="breadcrumb page_bradcam mb-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Learner ID </a></li>
                            <li class="breadcrumb-item active">
                                <?php echo ucwords($learner_profile['uname']) ?>
                            </li>
                        </ol>
                        <ol class="breadcrumb page_bradcam mb-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Class </a></li>
                            <li class="breadcrumb-item active">
                                <?php if (isset($show_affective['classname'])) {
                                    echo ucwords($show_affective['classname']);
                                } else {
                                    echo '';
                                } ?>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row ">
            <div class="col-12 QA_section">
                <div class="card QA_table ">
                    <div class="card-header" style="text-align:center;">
                        <h4> Academic Performance Report Sheet as at Mid Term,
                            <strong>
                                <?php echo ucwords($active_term['term']) ?>
                            </strong>
                        </h4>
                        <span class="float-end"> <strong>Generated:</strong>
                            <?php echo date("d-m-Y") ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive-sm">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="center">#</th>
                                        <th>Subject</th>
                                        <th class="center">CA Score -
                                            <?php echo $search_result['ca_score'] ?? ""; ?><br>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    if (!empty($show_report)) {
                                        foreach ($show_report as $show_result) {
                                            ?>
                                            <tr>
                                                <td class="center">
                                                    <?php echo $count++; ?>
                                                </td>
                                                <td class="center">
                                                    <?php echo $show_result['sbjname']; ?>
                                                </td>
                                                <td class="center">
                                                    <?php echo $show_result['score']; ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo 'No CA scores recorded';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function printTable() {


        // Print the table
        var printContents = document.getElementById("print_area").innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();

        // Restore the original page contents
        document.body.innerHTML = originalContents;
        elementsToHide.forEach(function (element) {
            element.style.display = '';
        });
    }
</script>