<?php
session_start();
?>
<?php
if (!isset($_SESSION["user"])) {
    header("location:myapp.php");
    exit; // Stop further execution of the script
} else 
    // echo $_SESSION["user"];
?>
<!-- ===================HEADER==================== -->
<?php include "include/header.php"; ?>
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
                                <h5 class="card-title">Insert New Gallery Data Here</h5>

                                <!-- General Form Elements -->
                                <form name="form" method="post" action="addGalleryBackend.php" enctype="multipart/form-data">
                                    <input type="hidden" name="new" value="1" />
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-2 col-form-label">Event Name</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="event" required>
                                        </div>
                                    </div>                                    

                                    <div class="row mb-3">
                                        <label for="inputNumber" class="col-sm-2 col-form-label">Upload Images</label>
                                        <div class="col-sm-10">
                                        <div id="fileInputsContainer">
                                            <div class="fileInputContainer">
                                                <input class="form-control fileInput" type="file" name="image1" accept=".jpeg, .jpg" required>
                                                <div class="errorMessage" style="color: red; display: none;">Only JPEG files are allowed.</div>
                                            </div>
                                        </div>

                                        <button type="button" class="btn btn-primary mt-3" onclick="addFileInput()">Add Another Image</button>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label"></label>
                                        <div class="col-sm-10">
                                            <button type="submit" name="addData" class="btn btn-primary" onclick="submitForm()">Submit</button>
                                            <script>
                                                document.querySelector('form').onsubmit = e => {
                                                    e.target.submit();
                                                    e.target.reset();
                                                    return false;
                                                };
                                                document.addEventListener('DOMContentLoaded', function () {
                                                    const fileInputsContainer = document.getElementById('fileInputsContainer');

                                                    fileInputsContainer.addEventListener('change', function (event) {
                                                        if (event.target.classList.contains('fileInput')) {
                                                            const fileInput = event.target;
                                                            const file = fileInput.files[0];
                                                            const errorMessage = fileInput.nextElementSibling;

                                                            const fileType = file.type.toLowerCase();
                                                            if (fileType !== 'image/jpeg') {
                                                                errorMessage.style.display = 'block';
                                                                fileInput.value = ''; // Clear the file input
                                                            } else {
                                                                errorMessage.style.display = 'none';
                                                            }
                                                        }
                                                    });
                                                });
 
                                                function addFileInput() {
                                                    var container = document.getElementById('fileInputsContainer');
                                                    var fileInputIndex = container.childElementCount + 1;
                                                    if (fileInputIndex <= 10) {
                                                        var newInputContainer = document.createElement('div');
                                                        newInputContainer.className = 'fileInputContainer';
                                                        newInputContainer.innerHTML = '<input class="form-control fileInput" type="file" name="image' + fileInputIndex + '" accept=".jpeg,.jpg" required><div class="errorMessage" style="color: red; display: none;">Only JPEG files are allowed.</div>';
                                                        container.appendChild(newInputContainer);
                                                    } else {
                                                        alert('You can only add up to 10 images.');
                                                    }
                                                }
                                            </script>
                                            <a href="gallery.php"><label type="" class="btn btn-primary">Cancel</lebal></a>
                                        </div>
                                    </div>

                                </form><!-- End General Form Elements -->


                            </div>
                        </div>

                    </div>

                </div>
            </div><!-- End Left side columns -->

        </div>
    </section>

<!-- ===========Footer================ -->
<?php include "include/footer.php" ?>
