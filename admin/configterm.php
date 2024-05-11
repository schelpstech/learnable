<?php

include "conf.php";

$message = "no action";
if (isset($_POST['configterm']) && $_POST['configterm'] == 'Create Term Record') {
    $term = mysqli_real_escape_string($con, $_POST['term']);
    $actSession = mysqli_real_escape_string($con, $_POST['actSession']);

    //Check if term exist
    $termname = $term . " " . $actSession;
    $sql_query = "select count(*) as cntconfig from lpterm where term ='$termname' ";
    $result = mysqli_query($con, $sql_query);
    $row = mysqli_fetch_array($result);
    $count = $row['cntconfig'];
    if ($count == 0) {

        //Create new term
        $sql = "INSERT INTO lpterm  (term, status) VALUES ('$termname','0')";
        if (mysqli_query($con, $sql)) {
            $remessage = 'Status : Term configuration record successfully saved.';
        } else {
            $remessage = 'Status : There was some error creating new term record';
        }
    } else {
        $remessage = 'Status : Duplicate Record :: A configuration record has been saved for this term already.';
    }
} elseif (isset($_POST['modifyTerm']) && $_POST['modifyTerm'] == 'Change Term Status') {

    $term = $_SESSION['termref'];
    $action = mysqli_real_escape_string($con, $_POST['action']);

    //Check if term exist
    $sql_query = "select count(*) as cntconfig from lpterm where tid ='$term' ";
    $result = mysqli_query($con, $sql_query);
    $row = mysqli_fetch_array($result);
    $count = $row['cntconfig'];

    if ($count == 1 & $action == 1) {
        //Deactivate selected term
        $sql = "UPDATE lpterm set status = 0";
        if (mysqli_query($con, $sql)) {
            //Activate selected term
            $sql = "UPDATE lpterm set status = '$action' where tid = '$term'";
            if (mysqli_query($con, $sql)) {
                $remessage = 'Status : Selected Term  has been successfully activated.';
            } else {
                $remessage = 'Status : There was some error activating selected term ';
            }
        } else {
            $remessage = 'Error : There was some error changing active term ';
        }
    } else {
        $remessage = 'Status : Error :: Term record not found.';
    }
}
$_SESSION['remessage'] = $remessage;
header("Location: mgterm.php");
