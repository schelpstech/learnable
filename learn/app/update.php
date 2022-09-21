<?php
include '../app/query.php';

//update profile

if ($_POST['update'] === 'update_profile') {

    if (isset($_SESSION['active'])) {
        $userid =$_SESSION['active'];

        if (isset($_POST["phone"])&& strlen($_POST["phone"]) == 11) {
            $phone = htmlspecialchars($_POST["phone"]);
        }else{
                $valErr .= 'Phonumber field must be at least 11 digits!.<br/>';
            }

            if (isset($_POST["password"]) && strlen($_POST["password"]) == 8) {
                $userpwd = htmlspecialchars($_POST["password"]);
            }else{
                $valErr .= 'Password field must not be empty and  8 characters!.<br/>';
            }
            if($valErr ==''){
                $tblName = 'lhpuser';
                $profiledata = array(
                  'numb' => $phone,
                  'upwd' => $userpwd,
                );
                $conditons = array(
                    'uname' => $userid,
                );
                $update = $model->upDate($tblName, $profiledata, $conditons);
            
                if ($update) {
                    $_SESSION['msg'] =
                            '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                                    <div class="alert-text">Success! <b>Profile updated successfully</b>!</div>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
                    $model->redirect('../view/learner/index.php');
                }else{
                    $_SESSION['msg'] =
                            '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                                    <div class="alert-text">Error! <br>'.$valErr.'</div>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
                    $model->redirect('../view/learner/index.php');
                }
            }else{
                $_SESSION['msg'] =
                '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                        <div class="alert-text">Error! <br>'.$valErr.'</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
                $model->redirect('../view/learner/index.php');
            }
        }else{
            $valErr .= 'Access Denied! You must be logged in to continue.<br/>';
            $_SESSION['msg'] =
                '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                        <div class="alert-text">Error! <br>'.$valErr.'</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
                $model->redirect('../view/index.php');
        }
}else{
        $_SESSION['msg'] =
        '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                <div class="alert-text">Error! <br>Invalid request. You request was sent from an unsecured page!</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
         $model->redirect('../view/index.php');
    }
