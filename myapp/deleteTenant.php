<?php 
session_start();
?>
<?php
if(! isset($_SESSION["user"]))
{
    header("Location:myapp.php");
}
else{
    // echo $_SESSION["user"];
}
?>
<?php

require('db.php');
$id=$_REQUEST['id'];

// Fetch the file name from the database
$deleteFileName = "SELECT image_name FROM tenant WHERE id=$id";
$FileResult = mysqli_query($con, $deleteFileName);

if($FileResult){
    $row = mysqli_fetch_assoc($FileResult);
    $fileName = $row['image_name'];

    // Specify the file path
    $path = 'uploads/' . $fileName;

    //Check if the file exists before attempting to delete it
    if(file_exists($path)){

        // Delete the file
        if(unlink($path)){
            echo "File deleted successfully";
        } else {
            echo "Error deleting file";
        }
    } else {
        echo "File does not exist";
    }
    mysqli_free_result($FileResult);
} else {
    echo "Error executing querry : ".mysqli_error($con);
}

$query = "DELETE FROM tenant WHERE id=$id";
$result = mysqli_query($con, $query) or die(mysqli_error($con));
header("Location: tenant.php");
exit();
?>