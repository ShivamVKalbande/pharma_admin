<?php
session_start();
include("db.php");
if(isset($_REQUEST['sub']))
{
    $a = $_REQUEST['uname'];
    $b = $_REQUEST['upassword'];

    $res = mysqli_query($con,"select* from user where username='$a'and password='$b'");
    $result=mysqli_fetch_array($res);

    if($result)
    {
        $_SESSION["user"] = $a;
        header("location: dashboard.php");
    }
    else
    {
        header("location: myapp.php?err=1");
    }

};
?>