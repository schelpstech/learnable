<?php

include "conf.php";
$type = "Learner";
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
            //check username
        $sql_query = "select count(*) as cntUser from lhpuser where uname='".$uname."' " ;
        $result = mysqli_query($con,$sql_query);
        $row = mysqli_fetch_array($result);

        $count = $row['cntUser'];
        if($count == 1){
            //check username, password 
            $sql_query = "select count(*) as cntUser from lhpuser where uname='".$uname."' and upwd='".$password."'  " ;
            $result = mysqli_query($con,$sql_query);
            $row = mysqli_fetch_array($result);
    
            $count = $row['cntUser'];
            if($count == 1){
                //check username, password and status
                $sql_query = "select count(*) as cntUser from lhpuser where uname='".$uname."' and upwd='".$password."' AND status= '1' " ;
                $result = mysqli_query($con,$sql_query);
                $row = mysqli_fetch_array($result);
        
                $count = $row['cntUser'];
                if($count == 1){
                        //if successfull insert into log
                    $sql= "INSERT INTO log  (uname,utype,stat, uip) VALUES ('$uname','$type',1, '$uip' )";
                    if(mysqli_query($con, $sql)){
                      //select classid and fullname
                        $sql_query = "select classid from lhpuser where uname ='$uname' " ;
                          $result = mysqli_query($con,$sql_query);
                            $row = mysqli_fetch_array($result);
                            $classid = $row["classid"];
                            $fullname = $row["fname"];
			                session_start();
		                	$_SESSION['classd'] = $classid;
		                	$_SESSION['studnamed'] = $uname;
                            $messagef = "Welcome ".$fullname;
                             $_SESSION['messagef'] = $messagef;
                             header('Location:learner/profile.php');
                           }
                        else{
                                                          
                                $messagef = "Sorry! We can't Log you in at this time";
                                $_SESSION['messagef'] = $messagef;
                                header("Location: student.php");
                              }
                            }
                  //if status is inactive
                            else{
                         $sql= "INSERT INTO log  (uname,utype,stat, uip) VALUES ('$uname','$type',2, '$uip' )";
                    if(mysqli_query($con, $sql)){
                      //status error
                      $messagef = "Sorry! Your account has been deactivated. Kindly contact school";
                      $_SESSION['messagef'] = $messagef;
                      header("Location: student.php");
                           }
                        }
                    }
                    else{
                         //if password is wrong
                        $sql= "INSERT INTO log  (uname,utype,stat, uip) VALUES ('$uname','$type',3, '$uip' )";
                   if(mysqli_query($con, $sql)){
                     //password error
                     $messagef = "Sorry! You have entered an incorrect password. Kindly contact school";
                     $_SESSION['messagef'] = $messagef;
                     header("Location: student.php");
                          }
                       }

                    }
                    else{
                         //if username is incorrect
                        $sql= "INSERT INTO log  (uname,utype,stat, uip) VALUES ('$uname','$type',4, '$uip' )";
                   if(mysqli_query($con, $sql)){
                     //password error
                     $messagef = "Sorry! Your username is incorrect. Kindly, check again";
                     $_SESSION['messagef'] = $messagef;
                     header("Location: student.php");
                          }
                       }
                    }
                       else{
                        //empty entry
                      
                    $messagef = "Username or Password cannot be blank";
                    $_SESSION['messagef'] = $messagef;
                    header("Location: student.php");
                         }
                        }
                        else{
                         //empty entry
                       
                     $messagef = "Unauthorised access";
                     $_SESSION['messagef'] = $messagef;
                     header("Location: student.php");
                          }
                          ?>