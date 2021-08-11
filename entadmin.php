<?php

include "conf.php";
$type = "Administrator";
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
if(isset($_POST['but_admn'])){

    $uname = mysqli_real_escape_string($con,$_POST['aaname']);
    $password = mysqli_real_escape_string($con,$_POST['aapwd']);
	

    if ($uname != "" && $password != ""){

        $sql_query = "select count(*) as cntUser from 123admin where dname='".$uname."'" ;
        $result = mysqli_query($con,$sql_query);
        $row = mysqli_fetch_array($result);

        $count = $row['cntUser'];
		
		

        if($count == 1){

            $sql_query = "select count(*) as cntUser from 123admin where dname='".$uname."' and dpwd='".$password."'" ;
        $result = mysqli_query($con,$sql_query);
        $row = mysqli_fetch_array($result);

        $count = $row['cntUser'];
		
		

        if($count == 1){

            $sql= "INSERT INTO log  (uname,utype,stat,uip) VALUES ('$uname','$type',1, '$uip')";
		    if(mysqli_query($con, $sql)){
			session_start();
			$_SESSION['unamed'] = $uname;
            header('Location: admin/dashboard.php');
        }
        }
        else{
            $sql= "INSERT INTO log  (uname,utype,stat, uip) VALUES ('$uname','$type',3, '$uip' )";
            if(mysqli_query($con, $sql)){ 
            session_start();
             $messagef = "Incorrect Password - Contact School";
            $_SESSION['messagef'] = $messagef;
        	header("Location: admin.php");
            }
        }
    }
    else{
        $sql= "INSERT INTO log  (uname,utype,stat, uip) VALUES ('$uname','$type',4 , '$uip')";
        if(mysqli_query($con, $sql)){
        session_start();
         $messagef = "Invalid Username - Contact School";
        $_SESSION['messagef'] = $messagef;
        header("Location: admin.php");
        }
    }

    }
    else{
        
         session_start();
         $messagef = "Invalid Log in Credentials";
        $_SESSION['messagef'] = $messagef;
        header("Location: admin.php");
    }

}
else{
        
    session_start();
    $messagef = "Unauthorised Access";
   $_SESSION['messagef'] = $messagef;
   header("Location: admin.php");
}
?>