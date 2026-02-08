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
?>


<!-- ===================HEADER==================== -->
<?php include"include/header.php"; ?>
<!-- ===================MAIN====================== -->
<section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-12">
          <div class="row">

            <!-- Recent Sales -->
            <div class="col-12">

            <?php if(isset($message)) { echo "$message"; } ?>

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Update Tenant Details Here</h5>
              <?php 
              if(isset($_GET['id']))
              {
                $id = $_GET['id'];
                $query = "SELECT * FROM Tenant WHERE id='$id' LIMIT 1";
                $result = mysqli_query($con, $query);

                if(mysqli_num_rows($result) > 0)
                {
                  $row = mysqli_fetch_array($result);
                  ?>
                  <!-- <form name="form" method="post" action="updateEventBackend.php" enctype="multipart/form-data">  -->
                  <form action="updateTenantBackend.php" enctype="multipart/form-data" method="POST">
                    <input name="id" type="hidden" value="<?=$row['id'];?>" />
                    <div class=" row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Total Rooms</label>
                      <div class="col-sm-10">
                      <input type="text" name="totalRooms" value="<?=$row['total_rooms'];?>" class="form-control" required>
                      </div>
                    </div>
                    <div class=" row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Total Cost</label>
                      <div class="col-sm-10">
                      <input type="text" name="totalCost" value="<?=$row['cost'];?>" class="form-control" required>
                      </div>
                    </div>
                    <div class=" row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Address</label>
                      <div class="col-sm-10">
                      <input type="text" name="address" value="<?=$row['address'];?>" class="form-control" required>
                      </div>
                    </div>
                    <div class=" row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Bedroom</label>
                      <div class="col-sm-10">
                      <input type="text" name="bedroom" value="<?=$row['bedroom'];?>" class="form-control" required>
                      </div>
                    </div>
                    <div class=" row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Kitchen</label>
                      <div class="col-sm-10">
                      <input type="text" name="kitchen" value="<?=$row['kitchen'];?>" class="form-control" required>
                      </div>
                    </div>
                    <div class=" row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Hall</label>
                      <div class="col-sm-10">
                      <input type="text" name="hall" value="<?=$row['hall'];?>" class="form-control" required>
                      </div>
                    </div>
                    <div class=" row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">City</label>
                      <div class="col-sm-10">
                      <input type="text" name="city" value="<?=$row['city'];?>" class="form-control" required>
                      </div>
                    </div>
                    <div class=" row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Tenant Type</label>
                      <div class="col-sm-10">
                      <input type="text" name="tenantType" value="<?=$row['tenant_type'];?>" class="form-control" required>
                      </div>
                    </div>
                    <div class=" row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Contact Name</label>
                      <div class="col-sm-10">
                      <input type="text" name="contactName" value="<?=$row['contact_name'];?>" class="form-control" required>
                      </div>
                    </div>
                    <div class=" row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Contact Number</label>
                      <div class="col-sm-10">
                      <input type="text" name="contactNumber" value="<?=$row['contact_number'];?>" class="form-control" required>
                      </div>
                    </div>
                    <div class=" row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Contact Email</label>
                      <div class="col-sm-10">
                      <input type="text" name="contactEmail" value="<?=$row['contact_email'];?>" class="form-control" required>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="inputNumber" class="col-sm-2 col-form-label">Upload Image</label>
                      <div class="col-sm-10">
                        <input type="file" name="updatedImageName" value="<?=$row['image_name'];?>" class="form-control" accept=".png" required>
                        <div id="errorMessage" style="color: red; display: none;">Only PNG files are allowed.</div>
                      </div>
                    </div>
                      <div class="mb-3">
                        <hr/>
                        <button type="submit" name="updateData" class="btn btn-primary">Update Data</button>
                        <a href="tenant.php"><label type=" " class="btn btn-primary">Cancel</lebal></a></div>
                      </div>
                </form>
                
                <?php
                }
                else 
                {
                  echo "<h4>No Record Found</h4>";
                }
              }
              else 
              {
                echo "<h4>No ID Found</h4>";
              }
              ?>
             <script>
              document.addEventListener('DOMContentLoaded', function () {
                const fileInput = document.querySelector('input[name="updatedPdfName"]');
                const errorMessage = document.getElementById('errorMessage');

                fileInput.addEventListener('change', function () {
                  const file = fileInput.files[0];
                  const fileType = file.type.toLowerCase();

                  if (fileType !== 'image/png') {
                    errorMessage.style.display = 'block';
                    fileInput.value = ''; // Clear the file input
                  } else {
                    errorMessage.style.display = 'none';
                  }
                });
              });
            </script>
     
           
          </div>

        </div>            
            
        </div><!-- End Left side columns -->

 

      </div>
    </section>

<!-- ===========Footer================ -->
   <?php include"include/footer.php" ?>