<?php 
session_start ();
?>
<?php
if(!isset($_SESSION["user"]))

header("location:myapp.php"); 
else
// echo $_SESSION["user"];
?>

<?php 
    require('db.php');
    $status = "";
        
    if(isset($_POST['updateData']))
    {
        $id=$_POST['id'];
        $totalRooms = $_POST['totalRooms'];
        $totalCost = $_POST['totalCost'];
        $address = $_POST['address'];
        $bedroom = $_POST['bedroom'];
        $kitchen = $_POST['kitchen'];
        $hall = $_POST['hall'];
        $city = $_POST['city'];
        $tenantType = $_POST['tenantType'];
        $contactName = $_POST['contactName'];
        $contactNumber = $_POST['contactNumber'];
        $contactEmail = $_POST['contactEmail'];
        $updatedImageName = $_FILES['updatedImageName']['name'];

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
        
        
        if($updatedImageName != ''){
            $ext = pathinfo($updatedImageName, PATHINFO_EXTENSION);
            $allowed = ['png'];
            if(in_array($ext, $allowed))
            {
                //get last record id
                $sql = 'SELECT MAX(id) AS id FROM tenant';
                $result = mysqli_query($con, $sql);
                if($result)
                {
                    $row = mysqli_fetch_array($result);
                    $updatedImageName = ($row['id']+1) . '-' . $updatedImageName;
                }
                else{
                    $updatedImageName = '1'.'-'. $updatedImageName;
                }
            //set target directory
            $path = 'uploads/';
            // //set date 
            // $date = date("Y-m-d H:i:s");
            move_uploaded_file($_FILES['updatedImageName']['tmp_name'],($path . $updatedImageName));
            $update = " UPDATE tenant SET
            total_rooms='$totalRooms', 
            address='$address',
            cost='$totalCost',
            bedroom='$bedroom',
            kitchen='$kitchen',
            hall='$hall',
            city='$city',
            tenant_type='$tenantType',
            contact_name='$contactName',
            contact_number='$contactNumber',
            contact_email='$contactEmail',
            image_name='$updatedImageName' 
            WHERE id='$id'";
            $result = mysqli_query($con, $update);
            header("Location:tenant.php");
            }
        else {
            error_reporting(E_ALL);
        }
    }
    else {
        header("Location: updateTenant.php?st=error");
    }
}
   
?>
