<?php
include '../app/query.php';
$valErr = '';
//LOGOUT
if (isset($_POST['logout']) && $_POST['logout'] === 'logout') {
    $model->log_out_user();
    session_start();
    $_SESSION['msg'] =
                        '<div class="alert text-white bg-info d-flex align-items-center justify-content-between" role="alert">
                            <div class="alert-text">Bye! <b>Log out successful</b>!</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
    $model->redirect('../view/index.php');
}

// Check if log-in form is submitted from website

elseif (isset($_POST['log_in']) && $_POST['log_in'] !== 'Log in') {
    $valErr .= 'Invalid Login request. You are attempting login from an unsecured page!.<br/>';
} elseif(isset($_POST['log_in']) && $_POST['log_in'] == 'Log in') {


    // Retrieve form input
    if (isset($_POST["userid"])) {
        if (!isset($_POST["userid"])) {
            $valErr .= 'Username field must not be empty!.<br/>';
        } else {
            $userid = htmlspecialchars($_POST["userid"]);
        }
    }

    if (isset($_POST["userpwd"])) {
        if (!isset($_POST["userpwd"])) {
            $valErr .= 'Password field must not be empty!.<br/>';
        } else {
            $userpwd = htmlspecialchars($_POST["userpwd"]);
        }
    }

    //check if username exist 
    $tblName = 'lhpuser';
    $conditions = array(
        'return_type' => 'count',
        'where' => array(
            'uname' => $userid,
        )
    );
    $confirm_learner = $model->getRows($tblName, $conditions);

    //check if username exist 
    $tblName = 'lhpstaff';
    $conditions = array(
        'return_type' => 'count',
        'where' => array(
            'sname' => $userid,
        )
    );
    $confirm_staff = $model->getRows($tblName, $conditions);

    if ($confirm_learner == 1) {
        $usertype = 'Learner';
        //select password 
        $tblName = 'lhpuser';
        $conditions = array(
            'return_type' => 'single',
            'where' => array(
                'uname' => $userid,
            )
        );
        $login_details = $model->getRows($tblName, $conditions);
    } elseif ($confirm_staff == 1) {
        $usertype = 'Instructor';
        //select password 
        $tblName = 'lhpstaff';
        $conditions = array(
            'return_type' => 'single',
            'where' => array(
                'sname' => $userid,
            )
        );
        $login_details = $model->getRows($tblName, $conditions);
    } else {
        $valErr .= 'Invalid Login Credentials!.<br/>';
        $tablename = 'log';
        $logindata = array(
            'uname' => $userid,
            'utype' => 'unknown',
            'uip' => $_SERVER['REMOTE_ADDR'],
            'stat' => 4,
        );
        $insert = $model->insert_data($tablename, $logindata);
        $_SESSION['msg'] =
            '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                    <div class="alert-text">Error! <br>' . $valErr . '</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        $model->redirect('../view/index.php');
    }
    //Check Password

    
        if(isset($login_details['upwd'])){
            $password = $login_details['upwd'];
        }elseif(isset($login_details['spwd'])){
            $password = $login_details['spwd'];
        }
        if ($password == $userpwd ) {
        //Check Active Status
        if (isset($login_details['status']) && $login_details['status'] == 1) {

            // Record Log Access

            $tablename = 'log';
            $logindata = array(
                'uname' => $userid,
                'utype' => $usertype,
                'uip' => $_SERVER['REMOTE_ADDR'],
                'stat' => 1,
            );
            $insert = $model->insert_data($tablename, $logindata);

            $_SESSION['msg'] =
                '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                        <div class="alert-text">Welcome! <b>Log in successful</b>!</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            if ($usertype == 'Learner') {
                $_SESSION['active'] = $userid;
                $_SESSION['user_type'] = $usertype;
                $model->redirect('../view/learner/index.php');
            } elseif ($usertype == 'Instructor') {
                $_SESSION['active'] = $userid;
                $_SESSION['user_type'] = $usertype;
                $model->redirect('../view/instructor/index.php');
            }
        } else {
            $valErr .= 'Access Denied! Contact school administrator.<br/>';
            $tablename = 'log';
            $logindata = array(
                'uname' => $userid,
                'utype' => $usertype,
                'uip' => $_SERVER['REMOTE_ADDR'],
                'stat' => 2,
            );
            $insert = $model->insert_data($tablename, $logindata);
            $_SESSION['msg'] =
                '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                        <div class="alert-text">Error! <br>' . $valErr . '</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            $model->redirect('../view/index.php');
        }
    } else {
        // Record Log Access
        $valErr .= 'Invalid Login Credentials!.<br/>';
        $tablename = 'log';
        $logindata = array(
            'uname' => $userid,
            'utype' => $usertype,
            'uip' => $_SERVER['REMOTE_ADDR'],
            'stat' => 3,
        );
        $insert = $model->insert_data($tablename, $logindata);
        $_SESSION['msg'] =
            '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                                <div class="alert-text">Error! <br>' . $valErr . '</div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
        $model->redirect('../view/index.php');
    }
}
