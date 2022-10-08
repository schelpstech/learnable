<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-xl-4">
                    <div class="white_card card_height_100 mb_30">
                        <div class="white_card_header">
                            <div class="box_header m-0">
                                <div class="main-title">
                                    <h3 class="m-0">Make Payment</h3>
                                </div>
                            </div>
                        </div>
                        <div class="white_card_body">

                            <form>
                                <div class="input-group mb-3">
                                    <div class="input-group-text">
                                        <span class="" id="basic-addon1">Learner ID</span>
                                    </div>
                                    <input type="text" class="form-control" value="<?php echo $learner_profile['uname'] ?? ' - '; ?>" aria-label="Username" aria-describedby="basic-addon1" disabled>
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-text">
                                        <span class="" id="basic-addon1">Fullname</span>
                                    </div>
                                    <input type="text" class="form-control" value="<?php echo $learner_profile['fname'] ?? ' - '; ?>" aria-label="Username" aria-describedby="basic-addon1" disabled>
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-text">
                                        <span class="">Amount &#8358;</span>
                                    </div>
                                    <input type="number" required="yes" class="form-control" aria-label="Amount (to the nearest naira)">
                                    <div class="input-group-text">
                                        <span class="">.00</span>
                                    </div>
                                </div>
                                <button name="log_in" type="submit" tabindex="3" class="btn_1 full_width text-center" value="Log in">Pay Now</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="white_card card_height_100 mb_30">
                        <div class="white_card_header">
                            <div class="box_header m-0">
                                <div class="main-title">
                                    <h3 class="m-0">Record Transfer</h3>
                                </div>
                            </div>
                        </div>
                        <div class="white_card_body">
                            <form>
                                <div class="input-group mb-3">
                                    <div class="input-group-text">
                                        <span class="" id="basic-addon1">Learner ID</span>
                                    </div>
                                    <input type="text" class="form-control" value="<?php echo $learner_profile['uname'] ?? ' - '; ?>" aria-label="Username" aria-describedby="basic-addon1" disabled>
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-text">
                                        <span class="" id="basic-addon1">Fullname</span>
                                    </div>
                                    <input type="text" class="form-control" value="<?php echo $learner_profile['fname'] ?? ' - '; ?>" aria-label="Username" aria-describedby="basic-addon1" disabled>
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-text">
                                        <span class="">Amount Paid : &#8358;</span>
                                    </div>
                                    <input type="number" class="form-control" required="yes" aria-label="Amount (to the nearest naira)">
                                    <div class="input-group-text">
                                        <span class="">.00</span>
                                    </div>
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-text">
                                        <label class="" for="inputGroupSelect02">Mode of Transfer</label>
                                    </div>
                                    <select class="form-select" id="inputGroupSelect02" required="yes">
                                        <option value="">select</option>
                                        <option value="Mobile App">Mobile App</option>
                                        <option value="POS">POS</option>
                                        <option value="USSD">USSD</option>
                                        <option value="Bank">Bank</option>
                                    </select>
                                </div>
                                <div class="input-group mb-3">
                                    <label class="input-group-text" for="inputGroupFile01">Transaction Receipt</label>
                                    <input type="file" class="form-control" required="yes" id="inputGroupFile01">
                                </div>
                                <button name="log_in" type="submit" tabindex="3" class="btn_1 full_width text-center" value="Log in">Record Payment</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="white_card card_height_100 mb_30 ">

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