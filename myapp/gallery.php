<?php 
session_start();
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
<!-- =================== Header include ==================== -->
<?php include"include/header.php"; ?>
<!-- =================== Main Content ====================== -->
<section class="section dashboard">
      <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Gallery Table </h5>
                  <table class="table datatable">
                    <thead>
                    <tr>
                      <th scope="col">Sr.No</th>
                      <th scope="col">Event</th>
                      <th scope="col">Delete</th>                
                    </tr>
                    </thead>
                    <tbody>
                      <?php 
                      $count=1;
                      $sel_query="select * from gallery ORDER BY id DESC;";
                      $result = mysqli_query($con, $sel_query);
                      while($row = mysqli_fetch_array($result)){ ?>
                      <tr><th scope="row"><?php echo $count; ?></th>
                      <td scope="col"><?php echo $row["event"]; ?></td>
                      <td><a href="#" class="btn btn-primary btn-delete" data-id="<?php echo $row["id"]; ?>">Delete</a></td>
                    </tr>
                      <?php $count++; } ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div><a href="addGallery.php"><h2 class="ri-add-circle-fill">Add New</h2></a></div>
            
        </div>
    </div>
</section>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Are you sure you want to delete ?</p>
        <div class="modal-buttons">
            <a id="deleteConfirmBtn" class="btn btn-primary">Yes, Delete</a>
            <button class="btn btn-secondary cancel-btn">Cancel</button>
        </div>
    </div>
</div>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Are you sure you want to logout?</p>
        <div class="modal-buttons">
            <a href="logout.php" class="btn btn-primary">Yes, Logout</a>
            <button class="btn btn-secondary cancel-btn">Cancel</button>
        </div>
    </div>
</div>

<div class="text-center">
    <button class="btn btn-primary btn-logout">Logout</button>
</div>
<!-- =================== Script ========================= -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // logout button modal javaScript content
    const logoutBtn = document.querySelector('.btn-logout');
    const logoutModal = document.getElementById('logoutModal');
    const logoutOverlay = document.querySelector('.logout-overlay');
    const closeLogoutModalBtn = logoutModal.querySelector('.close');
    const cancelLogoutBtn = logoutModal.querySelector('.cancel-btn');

    // Show the logout modal
    logoutBtn.addEventListener('click', function () {
        logoutModal.style.display = 'block';
        logoutOverlay.style.display = 'block';
    });

    // Close the logout modal
    closeLogoutModalBtn.addEventListener('click', function () {
        logoutModal.style.display = 'none';
        logoutOverlay.style.display = 'none';
    });

    cancelLogoutBtn.addEventListener('click', function () {
        logoutModal.style.display = 'none';
        logoutOverlay.style.display = 'none';
    });

    // Close logout modal when clicking outside of it
    window.addEventListener('click', function (event) {
        if (event.target === logoutModal) {
            logoutModal.style.display = 'none';
            logoutOverlay.style.display = 'none';
        }
    });

    // Delete Confirmation Modal
    const deleteModal = document.getElementById('deleteModal');
    const deleteConfirmBtn = document.getElementById('deleteConfirmBtn');
    const deleteBtns = document.querySelectorAll('.btn-delete');
    const deleteOverlay = document.querySelector('.delete-overlay');

    // Show the delete modal
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const articleId = this.getAttribute('data-id');
            const deleteLink = 'deleteGallery.php?id=' + articleId;
            deleteConfirmBtn.href = deleteLink;
            deleteModal.style.display = 'block';
            deleteOverlay.style.display = 'block';
        });
    });

    // Close the delete modal
    const closeDeleteModalBtn = deleteModal.querySelector('.close');
    const cancelDeleteBtn = deleteModal.querySelector('.cancel-btn');

    closeDeleteModalBtn.addEventListener('click', function () {
        deleteModal.style.display = 'none';
        deleteOverlay.style.display = 'none';
    });

    cancelDeleteBtn.addEventListener('click', function () {
        deleteModal.style.display = 'none';
        deleteOverlay.style.display = 'none';
    });

    // Close delete modal when clicking outside of it
    window.addEventListener('click', function (event) {
        if (event.target === deleteModal) {
            deleteModal.style.display = 'none';
            deleteOverlay.style.display = 'none';
        }
    });    
});
</script>
<!-- =================== End Main ========================== -->
<!-- =================== Footer Include ==================== -->
<?php include"include/footer.php"; ?>