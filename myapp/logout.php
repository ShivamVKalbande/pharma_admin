<?php
session_start();
session_destroy();
header("location: myapp.php");
?>