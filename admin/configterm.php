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
}
$_SESSION['remessage'] = $remessage;
header("Location: mgterm.php");
