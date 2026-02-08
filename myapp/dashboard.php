<?php

session_start();
?>
<?php

if (!isset($_SESSION["user"]))

  header("location:myapp.php");
else
// echo $_SESSION["user"];

?>

<?php

require('db.php');
?>
<!-- ===================HEADER==================== -->
<?php include "include/header.php"; ?>
<!-- ============================================= -->
<section class="section dashboard">
  <div class="row">

    <!-- Left side columns -->
    <!-- <div class="col-lg-8">
          <div class="row"> -->

    <!-- News Card -->
    <div class="col-xxl-4 col-md-4">
      <div class="card info-card sales-card">
        <div class="card-body">
          <a href="tenant.php">
            <h5 class="card-title">Tenant</h5>
          </a>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
            </div>
            <div class="ps-3">
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End News Card -->
   

  </div>
</section>
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
<!-- End Logout Confirmation Modal -->


<div class="text-center">
  <button class="btn btn-primary btn-logout">Logout</button>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const logoutBtn = document.querySelector('.btn-logout');
    const modal = document.getElementById('logoutModal');
    const overlay = document.querySelector('.overlay');
    const closeModalBtn = modal.querySelector('.close');
    const cancelBtn = modal.querySelector('.cancel-btn');

    // Show the modal
    logoutBtn.addEventListener('click', function () {
      modal.style.display = 'block';
      overlay.style.display = 'block';
    });

    // Close the modal
    closeModalBtn.addEventListener('click', function () {
      modal.style.display = 'none';
      overlay.style.display = 'none';
    });

    cancelBtn.addEventListener('click', function () {
      modal.style.display = 'none';
      overlay.style.display = 'none';
    });

    // Close modal when clicking outside of it
    window.addEventListener('click', function (event) {
      if (event.target === modal) {
        modal.style.display = 'none';
        overlay.style.display = 'none';
      }
    });
  });
</script>




<!-- ===========Footer================ -->
<?php include "include/footer.php" ?>