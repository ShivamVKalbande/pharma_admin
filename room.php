<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urban Nest</title>
    <link rel="stylesheet" href="styleroom.css">
    <link rel="stylesheet" href="utils.css">
    <link rel="stylesheet" href="search.css">
    <link rel="stylesheet" href="bk.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <link rel="stylesheet" href="2slide.css">
    <link rel="stylesheet" href="room7.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-VoPFIEf0xDz5Zg1eW6b7dAwpM3ViN8xq7ZpUCh1QlA0I/GzXgBvThXt2+IiU1/3j" crossorigin="anonymous">


   <!-- main css -->
   <link href="myapp/assets/css/style.css" rel="stylesheet">


    


    <script src="https://kit.fontawesome.com/353456fb3e.js" crossorigin="anonymous"></script>

    <style>

      @import url('https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2&family=Cedarville+Cursive&family=Damion&family=Lato:wght@300&family=PT+Serif&display=swap');
      @import url('https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2&family=Cedarville+Cursive&family=Damion&family=Lato:wght@300&family=PT+Serif&display=swap');
 
 
         .text-center {
             text-align: center;
         }
 
         .statement{
           font-size: 40px;
           color: #303030;
           padding-bottom: 1px;
           text-align: center;
           font-weight: 400;
           line-height: normal;
           height: 30px;
           line-height: 70px;
           font-family: 'Baloo Bhaijaan 2', sans-serif;
         }
           
         .statement span{
           font-family: 'Damion', cursive;
           font-size: 65px;
         }
         .slide4{
           background-image: url(https://images.unsplash.com/photo-1610569244414-5e7453a447a8?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D);
           background-size: contain;
           background-repeat: no-repeat;
           width: 400%;
           height: 100%;
         }
       .slide4 h1{
         text-align: right;
         padding: 45px;
         font-size: 31px;
         font-family: 'Baloo Bhaijaan 2', sans-serif;
       }
       .para{
         font-size: 23px;
        line-height: 33px;
        font-family: Arial, Helvetica, sans-serif;
       text-align: right;
       padding: 0px 51px 40px 30px;
       font-family: 'Baloo Bhaijaan 2', sans-serif;
       }
      .btn4 button{
       padding: 4px 11px 6px 15px;
       margin: 81px 35px 13px 10px;
       background-color: #2cbc2c;
      }
      .btn4 button:hover{
       background-color: darkgreen;
      }
      
    </style>
   


</head>

<body>
    <header>
        <nav>
            <div class="logo">
              <a href="index.php"><img src="logo.png" alt=""></a>
            </div>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about2.html">About</a></li>
                <li><a href="contact2.html">Contact</a></li>
            </ul>


            <ul class="login">
              <a href="myapp/myapp.php" class="loginbtn" id="loginButton">Sign in</a>
            </ul>
           </nav>
           </header>
  


   <div class="container" id="abcd">
    <!--<div class="room-picture">
      <div class="wishlist-icon">&#x2665;</div>
      <img src="room7.png" alt="Room Picture" width="500px" height="300px">
    </div>-->
    <?php include "myapp/db.php"; ?>
    <?php 
              if(isset($_GET['id']))
              {
                $id = $_GET['id'];
                $query = "SELECT * FROM Tenant WHERE id='$id'";
                $result = mysqli_query($con, $query);

                if(mysqli_num_rows($result) > 0)
                {
                  $row = mysqli_fetch_array($result);
                  $fileName = $row["image_name"];
                  ?>
    <div class="room-picture"></div>
    <div class="containe">
      <div class="carousel">
          <div class="slider">
              <section>
                <!-- <img src="room4.png" width="546px" height="400px"> -->
                <img src="myapp/uploads/<?php echo $fileName; ?>" alt="Room Picture"width="546px" height="400px">
              </section>
              <section><img src="myapp/uploads/<?php echo $fileName; ?>" alt="Room Picture"width="546px" height="400px"></section>
              <section><img src="myapp/uploads/<?php echo $fileName; ?>" alt="Room Picture"width="546px" height="400px"></section>
              </div>
           


          <div class="controls">
              <!-- <span class="arrow left"><i class="material-symbols-outlined">
                  arrow_back_ios
              </i></span>
              <span class="arrow right"><i class="material-symbols-outlined">
                  arrow_forward_ios
              </i></span> -->
              <ul>
                  <li class="selected"></li>
                  <li></li>
                  <li></li>
              </ul>
          </div>

      </div>
 </div>
        
      
      
  
        <div class="room-description">
          <div class="new">New to UrbanNest.com</div>
          <div class="heart"><i class="heart-icon fas fa-heart" onclick="toggleHeartIcon(this)"></i>
          <span class="save-msg">Save</span></div>
          <div class="share"><i class="share-icon fas fa-share-alt"></i>
            <span class="share-msg">Share this property</span>
          </div>
          <div class="discount-tag">10% OFF!</div>
          <p2><h2><?php echo $row["total_rooms"]; ?> </h2><span> for rent</span></p2>
          <div id="map">
            <a href="javascript:void(0)" onclick="initMap()" ><i class="fas fa-map-marker-alt location-icon"></i><?php echo $row["address"]; ?></a></div>
          <p><strong><?php echo $row["bedroom"]; ?> Bedroom | <?php echo $row["hall"]; ?> Hall <?php echo $row["kitchen"]; ?> Kitchen</strong></p>
          <p1>₹<?php echo $row["cost"]; ?> <span>per month</span></p1>
          <!-- <div class="contact"><button onclick="bookNow()">Contact Agent</button></div> -->
           <br>
           <div class="btn btn-primary btn-logout">
           <button  
          
           >Contact Agent</button>
           </div>

          </div>
          
      </div>
      <!-- Logout Confirmation Modal -->
<div id="logoutModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <p>Agent Name: <?php echo $row["contact_name"]; ?></p>
    <p>Agent Number: <?php echo $row["contact_number"]; ?></p>
    <p>Agent Email: <?php echo $row["contact_email"]; ?></p>
    <div class="modal-buttons">
      <a  class="btn btn-primary cancel-btn">Okay</a>
      <!-- <button class="btn btn-secondary cancel-btn">Cancel</button> -->
    </div>
  </div>
</div>
<!-- End Logout Confirmation Modal -->
            
      <script>
        function bookNow() {
           
                alert("Login First");
            
        }
    </script>
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
      <!-- <script>
        function bookNow() {
           
                alert("Login First");
            
        }
    </script> -->
      <script src="room7.js"></script>
      <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" async defer></script>
      <script src="map.js"></script>

</body>
</html>