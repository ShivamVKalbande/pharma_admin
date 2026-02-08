<?php 
session_start();
require('db.php');
?>
<!-- ===================HEADER==================== -->
<?php include "include/header.php"; ?>
<!-- ===================MAIN====================== -->
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tenant Table </h5>
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th scope="col">Sr.No</th>
                                <th scope="col">Tenat Address</th>
                                <th scope="col">Delete</th>
                                <th scope="col">Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $count = 1;
                            $sel_query = "SELECT * FROM tenant ORDER BY id DESC;";
                            $result = mysqli_query($con, $sel_query);
                            while ($row = mysqli_fetch_array($result)) {
                            ?>
                            <tr>
                                <th scope="row"><?php echo $count; ?></th>
                                <td><?php echo $row["address"]; ?></td>
                                <td><a href="#" class="btn btn-primary btn-delete" data-id="<?php echo $row["id"]; ?>">Delete</a></td>
                                <td><a href="updateTenant.php?id=<?php echo $row["id"]; ?>"><h2 class="btn btn-primary">Update</h2></a></td>
                            </tr>
                            <?php 
                                $count++;
                            } 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div><a href="addTenant.php"><h2 class="ri-add-circle-fill">Add New Tenant</h2></a></div>
        </div>
    </div>
</section>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Are you sure you want to delete this Tenant Details?</p>
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

    // Delete Confirmation Modal
    const deleteModal = document.getElementById('deleteModal');
    const deleteConfirmBtn = document.getElementById('deleteConfirmBtn');
    const deleteBtns = document.querySelectorAll('.btn-delete');

    // Show the delete modal
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const articleId = this.getAttribute('data-id');
            const deleteLink = 'deleteTenant.php?id=' + articleId;
            deleteConfirmBtn.href = deleteLink;
            deleteModal.style.display = 'block';
            overlay.style.display = 'block';
        });
    });

    // Close the delete modal
    const closeModal = () => {
        deleteModal.style.display = 'none';
        overlay.style.display = 'none';
    };

    deleteConfirmBtn.addEventListener('click', closeModal);
    document.querySelector('.cancel-btn').addEventListener('click', closeModal);
    document.querySelector('.close').addEventListener('click', closeModal);

    // Close modal when clicking outside of it
    window.addEventListener('click', function (event) {
        if (event.target === deleteModal) {
            closeModal();
        }
    });
});
</script>

<!-- ===========Footer================ -->
<?php include "include/footer.php" ?>
