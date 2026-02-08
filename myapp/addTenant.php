
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

           

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Insert Tenant Here</h5>

              <!-- General Form Elements -->
              <form name="form" method="post" action="addTenantBackend.php" enctype="multipart/form-data"> 
                <input type="hidden" name="new" value="1" />
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Total Room</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="totalRooms" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Tenant Address</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="address" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Total Cost</label>
                  <div class="col-sm-10">
                    <input type="number" class="form-control" name="totalCost" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Bedroom</label>
                  <div class="col-sm-10">
                    <input type="number" class="form-control" name="bedroom" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Kitchen</label>
                  <div class="col-sm-10">
                    <input type="number" class="form-control" name="kitchen" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Hall</label>
                  <div class="col-sm-10">
                    <input type="number" class="form-control" name="hall" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Tenant Type</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="tenantType" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">City</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="city" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Contact Name</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="contactName" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Contact Number</label>
                  <div class="col-sm-10">
                    <input type="number" class="form-control" name="contactNumber" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Contact Email</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="contactEmail" required>
                  </div>
                </div>
                
                
             
                <div class="row mb-3">
                  <label for="inputNumber" class="col-sm-2 col-form-label">Upload Image</label>
                  <div class="col-sm-10">
                    <input class="form-control" type="file" id="formFile" name="imageName" accept=".png" required>
                    <div id="errorMessage" style="color: red; display: none;">Only PNG files are allowed.</div>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label"></label>
                  <div class="col-sm-10">
                  <button type="submit" class="btn btn-primary" onclick="submitForm()">Submit</button>
                    <script>
                      document.querySelector('form').onsubmit = e => {
                        e.target.submit();
                        e.target.reset();
                        return false;
                      };

                        document.addEventListener('DOMContentLoaded', function ()
                        {
                          const fileInput = document.getElementById('formFile');
                          const errorMessage = document.getElementById('errorMessage');

                          fileInput.addEventListener('change', function () 
                          {
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
                    <a href="tenant.php"><label type=" " class="btn btn-primary">Cancel</lebal></a></div>
                  </div>
                </div>

              </form><!-- End General Form Elements -->
            
           
          </div>

        </div>            
            
        </div><!-- End Left side columns -->

 

      </div>
    </section>

<!-- ===========Footer================ -->
  <?php include"include/footer.php" ?>