<?php
include "conf.php";
if (!isset($_SESSION['unamed'])) {
    header('Location: ../index.php');
  }
  
require_once("DBController.php");
include 'headerresult.php';
$db_handle = new DBController();
if(!empty($_GET['class_id'])) {
        $coun_id = $_GET["class_id"]; 
}
if(!empty($_GET['term'])) {
    $term = $_GET["term"]; 
}
$query ="SELECT DISTINCT lid FROM `lhpresultrecord` WHERE classid = '$coun_id' AND term =  '$term'  ";
	$results = $db_handle->runQuery($query);
    foreach($results as $student_list) {
        $lname = $student_list["lid"] ;
        $sql = "SELECT * FROM `lhpuser` WHERE `uname` = '$lname'";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while ($row = mysqli_fetch_assoc($result)) {

    $stname = $row["fname"];
    $gender = $row["gender"];
    $dob = $row["dob"];

    $cclass = $row["classid"];
    $pix = $row["picture"];
  }
}


$sql = "SELECT classid FROM lhpresultrecord WHERE lid = '$lname' AND term = '$term'";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while ($row = mysqli_fetch_assoc($result)) {



    $cclass = $row["classid"];
  }
}

$sql = "SELECT classname FROM `lhpclass` WHERE `classid` = '$cclass'";;
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while ($row = mysqli_fetch_assoc($result)) {

    $dclass = $row["classname"];
  }
}

// Class Population
$sql = "SELECT COUNT( DISTINCT lid) AS pop FROM lhpresultrecord WHERE `classid` = '$cclass' AND term = '$term'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result);

$pop = $row["pop"];

//Result Configuration
$sql = "SELECT * FROM lhpresultconfig WHERE term = '$term' ";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result);

$resumedate = $row["resumption"];
$opendays = $row["sch_open"];
$sign = $row["signature"];
$ca_obtainable = $row["ca_score"];
$exam_obtainable = $row["exam_score"];

//School Information
$sql = "SELECT * FROM lhpschool ";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result);

$schname = $row["schname"];
$schmotto = $row["motto"];
$schyear = $row["founded"];
$schphone = $row["phone"];
$schemail = $row["email"];
$schweb = $row["website"];
$schaddress = $row["address"];
$schlogo = $row["logo"];
$schowner = $row["proprietor"];

//Get Affective Domain
$sql = "SELECT *  from lhpaffective WHERE uname = '$lname' AND term = '$term'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result);


$present =  $row["total_present"];
$lead =  $row["rating1"];
$eloq =  $row["rating2"];
$neat =  $row["rating3"];
$create =  $row["rating4"];
$response =  $row["rating5"];
$comment =  $row["comment"];


//Get Class Teacher's name
$sql = "SELECT * FROM `lhpclassalloc` WHERE term = '$term' and classid = '$cclass'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result);
$tutor = $row["tutorid"];

$sql = "SELECT * FROM `lhpstaff` WHERE  sname = '$tutor'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result);
$tutorname = $row["staffname"];

include 'template.php';

    }
    include 'footerrsult.php';
