<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: myapp.php");
    exit;
} else 
    // echo $_SESSION["user"];
require('db.php');
$id = $_REQUEST['id'];

// Fetch all file names from the database
$query = "SELECT image1, image2, image3, image4, image5, image6, image7, image8, image9, image10  FROM gallery WHERE id=$id";
$result = mysqli_query($con, $query);

if ($result && $row = mysqli_fetch_assoc($result))
 {
    $fileNames = [
        $row['image1'],
        $row['image2'],
        $row['image3'],
        $row['image4'],
        $row['image5'],
        $row['image6'],
        $row['image7'],
        $row['image8'],
        $row['image9'],
        $row['image10'],
    ];

    // Loop through each file name and delete if it is not 'NULL'
    foreach ($fileNames as $fileName) {
        if ($fileName !== 'NULL') {
            $path = 'uploads/gallery/' . $fileName;

            unlink($path);
                    
        }
    }

    mysqli_free_result($result);

    // Delete the record from the database
    $queryDelete = "DELETE FROM gallery WHERE id=$id";
    $resultDelete = mysqli_query($con, $queryDelete);

} else {
    echo "Error fetching file names: " . mysqli_error($con) . "<br>";
}

header("Location: gallery.php");
exit();
?>
