<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("location:myapp.php");
   }

require('db.php');
$status = "";

if (isset($_POST['new']) && $_POST['new'] == 1) {
    $destination = $_REQUEST['event'];
    // Array to hold uploaded file names
    $uploadedImages = [];

    // Loop through uploaded files
    for ($i = 1; $i <= 10; $i++) {
        $fieldName = 'image' . $i;
        $fileName = $_FILES[$fieldName]['name'];

        if (!empty($fileName)) {
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $allowed = ['jpg', 'jpeg'];

            // Check if file type is valid
            if (in_array($ext, $allowed)) {
                //get last record id
                    $sql = 'SELECT MAX(id) AS id FROM gallery';
                    $result = mysqli_query($con, $sql);
                    if($result)
                    {
                        $row = mysqli_fetch_array($result);
                        $newFileName = ($row['id']+1) . '-' . $fileName;
                    }
                    else{
                        $newFileName = '1'.'-'. $fileName;
                        }
                // Set target directory
                $path = 'uploads/gallery/';
                
                move_uploaded_file($_FILES[$fieldName]['tmp_name'], ($path . $newFileName));
                // Add uploaded file name to array
                $uploadedImages[$fieldName] = $newFileName;
            } else {
                // Handle invalid file type error
                error_reporting(E_ALL);
            }
        } else {
            // Handle empty file input if needed
           
        }
    }

    // Prepare SQL query
        $sql = "INSERT INTO gallery (`event`, `image1`, `image2`, `image3`, `image4`, `image5`, `image6`, `image7`, `image8`, `image9`, `image10`) 
        VALUES ('$destination'";

        // Add uploaded file names to SQL query
        foreach ($uploadedImages as $field => $file) {
        $sql .= ", '$file'";
        }

        // Add remaining NULL values to SQL query
        for ($i = count($uploadedImages) + 1; $i <= 10; $i++) {
        $sql .= ", 'NULL'";
        }

        $sql .= ")";

    // Execute SQL query
    mysqli_query($con, $sql) or die(mysqli_error($con));
    header("Location:gallery.php");
} else {
    header("Location: addGallery.php?st=error");
    exit();
}
?>
