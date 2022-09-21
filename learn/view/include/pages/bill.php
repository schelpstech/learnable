        <div class="main_content_iner overly_inner ">
            <div class="container-fluid p-0 ">
                <div class="row ">
                    <div class="col-xl-8">
                        <div class="col-12 QA_section">
                            <div class="card QA_table ">
                                <div class="card-header">
                                    <strong>School Bill </strong>
                                    <span class="float-end"> <strong>Status:</strong> Pending</span>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-sm-6">
                                            <h6 class="mb-3">Bill for:</h6>
                                            <div>
                                                <strong><?php echo $learner_profile['uname'] ?? ' - '; ?></strong><br>
                                                <strong><?php echo $learner_profile['fname'] ?? ' - '; ?></strong>
                                            </div>
                                            <div><?php echo $learner_class['classname'] ?? ' - '; ?></div>
                                            <div><?php echo $active_term['term'] ?? ' - '; ?></div>
                                            <div>Email: <?php echo $learner_class['email'] ?? ' - '; ?>
                                            </div>
                                            <div>Phone: <?php echo $learner_class['numb'] ?? ' - '; ?></div>
                                        </div>
                                        <div class="col-sm-6">
                                            <h6 class="mb-3">Payment Advice:</h6>
                                            <div>Account Number : </div>
                                            <div>Account Name : </div>
                                            <div>Receiving Bank : </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive-sm">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="center">#</th>
                                                    <th>Due Date</th>
                                                    <th>Fee Type</th>
                                                    <th>Description</th>
                                                    <th class="center">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                $count = 1 ;
                                                if (!empty($view_bill)) {
                                                    foreach ($view_bill as $bill) {
                                            ?>
                                                <tr>
                                                    <td class="center"><?php echo $count++; ?></td>
                                                    <td class="center"><?php echo $bill['due']; ?></td>
                                                    <td class="left strong"><?php echo $bill['type']; ?></td>
                                                    <td class="left"><?php echo $bill['feename']; ?></td>
                                                    <td class="right"><?php echo  $utility->money($bill['amount']); ?></td>
                                                </tr>
                                                <?php
                                                }
                                                } else {
                                            echo 'No bill assigned yet';
                                                }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4 col-sm-5">
                                        </div>
                                        <div class="col-lg-4 col-sm-5 ms-auto QA_section">
                                            <table class="table table-clear QA_table">
                                                <tbody>
                                                    <tr>
                                                        <td class="left">
                                                            <strong>Subtotal</strong>
                                                        </td>
                                                        <td class="right"><?php echo $utility->money($bill_sum['schfee']);?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left">
                                                            <strong>Discount (if available)</strong>
                                                        </td>
                                                        <td class="right"><?php echo $utility->money($bill_discount['discount']);?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left">
                                                            <strong>Total</strong>
                                                        </td>
                                                        <td class="right">
                                                            <strong><?php echo $utility->money($bill_sum['schfee'] - $bill_discount['discount']);?></strong>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 ">
                        <div class="white_card card_height_100 mb_30 sales_card_wrapper">
                            <div class="white_card_header d-flex justify-content-end">
                                <button class="export_btn">Export</button>
                            </div>

                            <div class="sales_card_body">
                                <div class="single_sales">
                                    <span>Total Bill</span>
                                    <h3><?php echo $utility->money($bill_sum['schfee'] - $bill_discount['discount']);?></h3>
                                </div>
                                <div class="single_sales">
                                    <span>Total Paid</span>
                                    <h3><?php echo $utility->money($bill_paid['paid']) ?? 0?></h3>
                                </div>
                                <div class="single_sales">
                                    <span>Total Balance</span>
                                    <h3><?php echo $utility->money($bill_paid['paid'] - ($bill_sum['schfee'] - $bill_discount['discount']));?></h3>
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