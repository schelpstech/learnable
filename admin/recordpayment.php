<?php
session_start();
ob_start();
include 'conf.php';

// Enable error reporting (disable on production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function sanitize($con, $value) {
    return mysqli_real_escape_string($con, trim($value));
}

$feemessage = 'Status: Unable to process request.';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['paybill']) && trim($_POST['paybill']) === 'Record Payment for  Selected Learner') {
        
        $classname = sanitize($con, $_POST['classname']);
        $learner = sanitize($con, $_POST['learner']);
        $term = sanitize($con, $_POST['term']);
        $mode = sanitize($con, $_POST['mode']);
        $reference = sanitize($con, $_POST['payref']);
        $dated = sanitize($con, $_POST['paydate']);
        $status = sanitize($con, $_POST['status']);
        $amount = sanitize($con, $_POST['amountpaid']);

        if ($classname && $learner && $term && $mode && $reference && $dated && $status !== '' && $amount !== '') {
            
            // Check if reference already exists (regardless of status)
            $checkStmt = $con->prepare("SELECT COUNT(*) FROM lhptransaction WHERE term = ? AND classid = ? AND stdid = ? AND reference = ?");
            $checkStmt->bind_param("ssss", $term, $classname, $learner, $reference);
            $checkStmt->execute();
            $checkStmt->bind_result($count);
            $checkStmt->fetch();
            $checkStmt->close();

            if ($count == 0) {
                $insertStmt = $con->prepare("INSERT INTO lhptransaction (term, classid, stdid, reference, mode, paydate, amount, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $insertStmt->bind_param("ssssssdi", $term, $classname, $learner, $reference, $mode, $dated, $amount, $status);

                if ($insertStmt->execute()) {
                    $feemessage = 'Status: Successfully recorded payment for the selected learner';
                } else {
                    error_log("Insert Error: " . $insertStmt->error);
                    $feemessage = 'Status: Unable to record payment. DB Error.';
                }
                $insertStmt->close();
            } else {
                $feemessage = 'Status: Transaction reference has already been used.';
            }

        } else {
            $feemessage = 'Status: Kindly provide all required fields.';
        }

    } elseif (isset($_POST['updatepaybill']) && trim($_POST['updatepaybill']) === "Modify Record Payment for  Selected Learner") {

        if (!isset($_SESSION['transref'])) {
            $feemessage = 'Status: Transaction reference missing from session.';
        } else {
            $reference = $_SESSION['transref'];
            $status = sanitize($con, $_POST['status']);
            $amount = sanitize($con, $_POST['amountpaid']);

            if ($amount !== '' && $status !== '') {
                $checkStmt = $con->prepare("SELECT COUNT(*) FROM lhptransaction WHERE transid = ?");
                $checkStmt->bind_param("s", $reference);
                $checkStmt->execute();
                $checkStmt->bind_result($count);
                $checkStmt->fetch();
                $checkStmt->close();

                if ($count == 1) {
                    $updateStmt = $con->prepare("UPDATE lhptransaction SET amount = ?, status = ? WHERE transid = ?");
                    $updateStmt->bind_param("dis", $amount, $status, $reference);

                    if ($updateStmt->execute()) {
                        $feemessage = 'Status: Successfully updated payment record for the selected learner';
                    } else {
                        error_log("Update Error: " . $updateStmt->error);
                        $feemessage = 'Status: Unable to update payment record. DB Error.';
                    }
                    $updateStmt->close();
                } else {
                    $feemessage = 'Status: Transaction record not found.';
                }
            } else {
                $feemessage = 'Status: Kindly provide all required fields.';
            }
        }

    } else {
        $feemessage = 'Status: No valid action requested.';
    }

} else {
    $feemessage = 'Status: Invalid request method.';
}

// Save message and redirect
$_SESSION['feemessage'] = $feemessage;
header('Location: payrecord.php');
exit();
