<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="col-xl-12">
            <div class="white_card card_height_100 mb_30 ">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="white_card_header">
                            <div class="box_header m-0">
                                <div class="main-title">
                                    <h3 class="m-0">Transaction History</h3>
                                    <span><strong><?php echo $learner_profile['uname'] ?? ' - '; ?></strong></span>
                                    <span><strong><?php echo $learner_profile['fname'] ?? ' - '; ?></strong></span>
                                    <span>as at <strong><?php echo date("d-m-Y") ?></strong></span>
                                </div>
                            </div>
                        </div>
                        <div class="white_card_body QA_section">
                            <div class="QA_table ">

                                <table class="table lms_table_active2 p-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">Status</th>
                                            <th scope="col">Term</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Mode</th>
                                            <th scope="col">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($history)) {
                                            foreach ($history as $history) {
                                        ?>
                                                <tr>
                                                    <td>
                                                        <?php
                                                        if ($history['status'] == 1) {
                                                            echo '<div class="customer d-flex align-items-center">
                                                            <div class="social_media">
                                                                <i class="ti-thumb-up"></i>
                                                            </div>
                                                            <div class="ml_18">
                                                                <h4 class="f_s_18 f_w_900 mb-0">verified
                                                                </h4>
                                                                <span class="f_s_12 f_w_700 text_color_8">' . $history["rectime"] . '</span>
                                                            </div>
                                                        </div>';
                                                        } elseif ($history['status'] == 0) {
                                                            echo '<div class="customer d-flex align-items-center">
                                                            <div class="social_media youtube_bg">
                                                                <i class="ti-thumb-down"></i>
                                                            </div>
                                                            <div class="ml_18">
                                                                <h4 class="f_s_18 f_w_900 mb-0">declined
                                                                </h4>
                                                                <span class="f_s_12 f_w_700 text_color_8">' . $history["rectime"] . '</span>
                                                            </div>
                                                        </div>';
                                                        } elseif ($history['status'] == 2) {
                                                            echo '<div class="customer d-flex align-items-center">
                                                            <div class="social_media twitter_bg">
                                                                <i class="ti-hand-open"></i>
                                                            </div>
                                                            <div class="ml_18">
                                                                <h4 class="f_s_18 f_w_900 mb-0">pending
                                                                </h4>
                                                                <span class="f_s_12 f_w_700 text_color_8">' . $history["rectime"] . '</span>
                                                            </div>
                                                        </div>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <div class="customer d-flex align-items-center">
                                                            <div class="ml_18">
                                                                <h3 class="f_s_18 f_w_900 mb-0"><?php echo $history['term']; ?>
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <h3 class="f_s_18 f_w_900 mb-0"><small><?php echo $history['paydate']; ?></small></h3>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <h3 class="f_s_18 f_w_800 mb-0"><?php echo $history['mode']; ?></h3>
                                                            <span class="f_s_12 f_w_500 color_text_3"><?php echo $history['reference']; ?></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <h3 class="f_s_18 f_w_800 mb-0"><?php echo $utility->money($history['amount']) ?? 0; ?></h3>
                                                        </div>
                                                    </td>

                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo 'No Transaction yet';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 ">
                        <div class="white_card card_height_100 mb_30 sales_card_wrapper">
                            <div class="white_card_header d-flex justify-content-end">
                                <button class="export_btn">Payment Card</button>
                            </div>

                            <div class="sales_card_body">
                                <div class="single_sales">
                                    <span>Total Bill</span>
                                    <h3><?php echo $utility->money($bill_sum['schfee'] - $bill_discount['discount']); ?></h3>
                                </div>
                                <div class="single_sales">
                                    <span>Total Paid</span>
                                    <h3><?php echo $utility->money($bill_paid['paid']) ?? 0 ?></h3>
                                </div>
                                <div class="single_sales">
                                    <span>Total Balance</span>
                                    <h3><?php echo $utility->money($bill_paid['paid'] - ($bill_sum['schfee'] - $bill_discount['discount'])); ?></h3>
                                </div>
                                <div class="single_sales">
                                    <span>Num. of Payments</span>
                                    <h3><?php echo $bill_transaction ?? 0 ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>