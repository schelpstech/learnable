<div class="main_content_iner overly_inner ">
            <div class="container-fluid p-0 ">

                <div class="row">
                    <div class="col-12">
                        <div class="page_title_box d-flex align-items-center justify-content-between">
                            <div class="page_title_left">
                                <h3 class="f_s_30 f_w_700 dark_text"><?php echo ucwords($view_note['sbjname']) ?></h3>
                                <ol class="breadcrumb page_bradcam mb-0">
                                    <li class="breadcrumb-item"><a href="javascript:void(0);">Subject Teacher </a></li>
                                    <li class="breadcrumb-item active"><?php echo ucwords($view_note['staffname']) ?></li>
                                </ol>
                                
                                <ol class="breadcrumb page_bradcam mb-0">
                                    <li class="breadcrumb-item"><a href="javascript:void(0);">Week  </a></li>
                                    <li class="breadcrumb-item active"><?php echo ucwords($view_note['week']) ?></li>
                                </ol>
                            </div>
                            <a href="#" onclick="window.print();" class="white_btn3">Print</a>
                        </div>
                    </div>
                </div>
                <div class="row ">
                    <div class="col-12 QA_section">
                        <div class="card QA_table ">
                            <div class="card-header">
                                Topic : 
                                <strong><?php echo ucwords($view_note['topic']) ?></strong>
                                <span class="float-end"> <strong>Date:</strong> <?php echo ucwords($view_note['rectime']) ?></span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive-sm">
                                    <?php
                                        if($view_note['type']=='text'){
                                            echo ucwords($view_note['content']);  
                                        }elseif($view_note['type']=='file'){
                                            echo '<iframe width="100%" height="600" src="https://dwatschools.com.ng/learnable/instructor/noteoflesson/'.$view_note['content'].'" title="'.$view_note['topic'].'"frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                                        }else{
                                            echo '<iframe width="100%" height="600" src="'.$view_note['content'].'" title="'.$view_note['topic'].'"frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                                        }
                                     ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>