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
    
    if(isset($_POST['new']) && $_POST['new']==1)
    {
        $totalRooms = $_REQUEST['totalRooms'];
        $address = $_REQUEST['address'];
        $totalCost = $_REQUEST['totalCost'];
        $bedroom = $_REQUEST['bedroom'];
        $kitchen = $_REQUEST['kitchen'];
        $hall = $_REQUEST['hall'];
        $tenantType = $_REQUEST['tenantType'];
        $city = $_REQUEST['city'];
        $contactName = $_REQUEST['contactName'];
        $contactNumber = $_REQUEST['contactNumber'];
        $contactEmail = $_REQUEST['contactEmail'];
        $imageName = $_FILES['imageName']['name'];

            if($imageName != ''){
            $ext = pathinfo($imageName, PATHINFO_EXTENSION);
            $allowed = ['png'];
            //check if file type is valid
                if(in_array($ext, $allowed))
                {
                    //get last record id
                    $sql = 'SELECT MAX(id) AS id FROM tenant';

                    $result = mysqli_query($con, $sql);
                        if($result)
                        {
                            $row = mysqli_fetch_array($result);
                            $imageName = ($row['id']+1) . '-' . $imageName;
                        }
                        else{
                            $imageName = '1'.'-'. $imageName;
                        }
                    //set target directory
                    $path = 'uploads/';
                    // //set date 
                    // $date = date("Y-m-d H:i:s");
                    move_uploaded_file($_FILES['imageName']['tmp_name'],($path . $imageName));
                    //insert file details into database
                    $sql="insert into tenant (`total_rooms`, `address`, `cost`, `bedroom`, `kitchen`, `hall`, `contact_name`, `contact_number`, `contact_email`, `image_name`, `city`, `tenant_type`) values ('$totalRooms', '$address', '$totalCost' , '$bedroom', '$kitchen', '$hall', '$contactName', '$contactNumber', '$contactEmail', '$imageName', '$city', '$tenantType' )";
                    mysqli_query($con, $sql) or die(mysqli_error($con));
                    header("Location:tenant.php");
                }
                else {
                    error_reporting(E_ALL);
                }
            }
            else {
                header("Location: addTenant.php?st=error");
            }
    }
?>