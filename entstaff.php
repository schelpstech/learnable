<?php
include "conf.php";
$type = "Instructor";
function getUserIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
$uip = getUserIpAddr();
if(isset($_POST['but_submit'])){

    $uname = mysqli_real_escape_string($con,$_POST['uname']);
    $password = mysqli_real_escape_string($con,$_POST['upass']);
	

    if ($uname != "" && $password != ""){

        $sql_query = "select count(*) as cntUser from lhpstaff where sname ='".$uname."'" ;
        $result = mysqli_query($con,$sql_query);
        $row = mysqli_fetch_array($result);

        $count = $row['cntUser'];
		
		

        if($count == 1){

            $sql_query = "select count(*) as cntUser from lhpstaff where sname='".$uname."' and spwd='".$password."'" ;
        $result = mysqli_query($con,$sql_query);
        $row = mysqli_fetch_array($result);

        $count = $row['cntUser'];
		
		

        if($count == 1){

            $sql_query = "select count(*) as cntUser from lhpstaff where sname='".$uname."' and spwd='".$password."' and status = 1" ;
        $result = mysqli_query($con,$sql_query);
        $row = mysqli_fetch_array($result);

        $count = $row['cntUser'];
		
		

        if($count == 1){

            $sql_query = "select role  from lhpstaff where sname='".$uname."' and spwd='".$password."' and status = 1" ;
            $result = mysqli_query($con,$sql_query);
            $row = mysqli_fetch_array($result);
    
            $role = $row['role'];


            $sql= "INSERT INTO log  (uname,utype,stat,uip) VALUES ('$uname','$type',1, '$uip')";
		    if(mysqli_query($con, $sql)){
		    
		    }
		   if ($role == "b"){
			$_SESSION['unamed'] = $uname;
            header('Location: bursar/profile.php');
           }
           else if ($role == "t"){
			$_SESSION['stnamed'] = $uname;
            header('Location: instructor/profile.php');
           }
        }  
    
    else{
        $sql= "INSERT INTO log  (uname,utype,stat, uip) VALUES ('$uname','$type',2, '$uip')";
        if(mysqli_query($con, $sql)){ 
        session_start();
         $messagef = "Ooops! Your account has been de-activated, kindly contact school";
        $_SESSION['messagef'] = $messagef;
        header("Location: staff.php");
        }
    }
}
        else{
            $sql= "INSERT INTO log  (uname,utype,stat, uip) VALUES ('$uname','$type',3, '$uip' )";
            if(mysqli_query($con, $sql)){ 
            session_start();
             $messagef = "Incorrect Password - Contact School";
            $_SESSION['messagef'] = $messagef;
        	header("Location: staff.php");
            }
        }
    }
    else{
        $sql= "INSERT INTO log  (uname,utype,stat, uip) VALUES ('$uname','$type',4 , '$uip')";
        if(mysqli_query($con, $sql)){
        session_start();
         $messagef = "Invalid Username - Contact School";
        $_SESSION['messagef'] = $messagef;
        header("Location: staff.php");
        }
    }

    }
    else{
        
         session_start();
         $messagef = "Invalid Log in Credentials";
        $_SESSION['messagef'] = $messagef;
        header("Location: staff.php");
    }

}
else{
        
    session_start();
    $messagef = "Unauthorised Access";
   $_SESSION['messagef'] = $messagef;
   header("Location: staff.php");
}
?>