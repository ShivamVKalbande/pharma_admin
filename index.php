<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Urban Nest</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="utils.css">
  <link rel="stylesheet" href="search.css">
  <link rel="stylesheet" href="bk.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
  <link rel="stylesheet" type="text/css"
    href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />
  <link rel="stylesheet" href="2slide.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="slide1.css">
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />



  <script src="https://kit.fontawesome.com/353456fb3e.js" crossorigin="anonymous"></script>



  <style>
    @import url('https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2&family=Cedarville+Cursive&family=Damion&family=Lato:wght@300&family=PT+Serif&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2&family=Cedarville+Cursive&family=Damion&family=Lato:wght@300&family=PT+Serif&display=swap');

    main {
      background-color: rgb(82 161 235);
    }

    @media only screen and (max-width: 768px) {
      .statement {
        font-size: 30px;
        line-height: normal;
      }

      .slide4 {
        width: 100%;
        left: 0;
      }

      .slide4 h1 {
        font-size: 24px;
        padding: 25px;
      }

      .para {
        font-size: 18px;
        padding: 0px 25px 20px 15px;
      }

      /* Adjust other styles as needed */
    }

    /* Responsive adjustments for smaller screens */
    @media only screen and (max-width: 576px) {
      .statement {
        font-size: 24px;
      }

      .slide4 h1 {
        font-size: 20px;
      }

      .para {
        font-size: 16px;
      }

      /* Adjust other styles as needed */
    }

    .text-center {
      text-align: center;
    }

    .statement {
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

    .statement span {
      font-family: 'Damion', cursive;
      font-size: 65px;
    }

    .slide4 {
      background-color: white;
      background-image: url(lastslide.jpeg);
      background-size: contain;
      background-repeat: no-repeat;
      width: 972px;
      height: 100%;
      left: -763px;
      position: relative;
    }

    .slide4 h1 {
      text-align: right;
      padding: 45px 83px 15px 46px;
      font-size: 31px;
      font-family: 'Baloo Bhaijaan 2', sans-serif;
    }

    .para {
      font-size: 22px;
      line-height: 33px;
      font-family: Arial, Helvetica, sans-serif;
      text-align: right;
      padding: 0px 70px 40px 30px;
      font-family: 'Baloo Bhaijaan 2', sans-serif;
    }

    .btn4 button {
      padding: 4px 11px 6px 15px;
      margin: 81px 35px 13px 10px;
      background-color: #2cbc2c;
    }

    .btn6 button {
      padding: 10px;
      margin: 15px 73px 14px 11px;
    }

    .btn4 button:hover {
      background-color: rgb(12, 128, 12);
    }

    @media only screen and (max-width: 1300px) {

      .slide4 {
        background-color: white;
        background-image: url(lastslide.jpeg);
        background-size: contain;
        background-repeat: no-repeat;
        width: 972px;
        height: 100%;
        left: -770px;
        position: relative;
      }

      .slide4 h1 {
        text-align: right;
        padding: 45px 83px 15px 46px;
        font-size: 31px;
        font-family: 'Baloo Bhaijaan 2', sans-serif;
      }

      .para {
        font-size: 22px;
        line-height: 33px;
        font-family: Arial, Helvetica, sans-serif;
        text-align: right;
        padding: 0px 70px 40px 30px;
        font-family: 'Baloo Bhaijaan 2', sans-serif;
      }
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
                <li><a href="index.php">Home</a></li>"
        <li><a href="about2.html">About Us</a></li>
        <li><a href="contact2.html">Contact Us</a></li>
      </ul>



      <section class="search-section">

        <div class="search-bar">
          <select id="city-select" class="search-input">
            <option value="City">City</option>
            <option value="Mumbai">Mumbai</option>
            <option value="Pune">Pune</option>
            <option value="Nashik">Nashik</option>
          </select>
           
          <select id="rooms-select" class="search-input">
            <option value="Property">Property</option>
            <option value="1 Room">1 Room</option>
            <option value="2 Room">2 Room</option>
            <option value="1 bhk">1 BHK Flat</option>
            <option value="2 bhk">2 BHK Flat</option>
            <option value="3 bhk">3 BHK Flat</option>
            
          </select>
          <select id="tenant-select" class="search-input">
            <option value="Tenant Type">Tenant Type</option>
            <option value="Girls">Girls</option>
            <option value="Boys">Boys</option>
            <option value="Boys">Colive</option>
          </select>


          <select type="number" id="budget-input" class="search-input" placeholder="Enter budget">
            <option value="Budget">Budget</option>
            <option value="Below 5k">Below 5k</option>
            <option value="5k-10k">5k-10k</option>
            <option value="10k-15k">10k-15k</option>
            <option value="15k-20k">15k-20k</option>
            <option value="20k-25k">20k-25k</option>
          </select>
          <button onclick="openWebsite()" id="search-button" >Search</button>
        </div>
      </section>
      
      

      <ul class="login">
        <a href="myapp/myapp.php" class="loginbtn" id="loginButton">Sign in</a>
      </ul>
      

    </nav>
  </header>
  

  <main>
    <!--<div class="box">
            <div class="slider">
                <img src="https://source.unsplash.com/random/1100x400/?Room for Rent" alt="">
            </div>-->

    <div class="statement">
      Find a home you'll
      <span>Love</span>
    </div>


    <div class="contain">
      <div class="carousel">
        <div class="slider">


          <div class="slide1">
            <section>
              <img src="slide16.png" width="918px" height="390px">
              <div class="textContainer">
                <h3>
                  <li>
                    <span>Find </span>
                    <span class="animatedText">ROOMS </span>
                  </li>
                  <div class="stay">
                    <span> for your next stay... </span>
                  </div>
                </h3>
              </div>

              <div class="slide1btn"><button>Discover Homes</button></div>
          </div>

          <script src="slide1.js"></script>


          <section>
            <div class="slide3">
              <img src="lastsecond.jpeg" width="992px" height="470px" right="573">
              <div class="slide3btn"><button><span class="material-symbols-outlined">
                    diamond
                  </span> Premium</button></div>
              <h3>Olives Greenscapes</h3><br>
              <p>by Provident UrbanNest.com</p>
              <p>1, 2, 3 BHK Appartments</p>
              <p>Kondhwa Road,Pune</p>
            </div>
          </section>


          <!--<section>
                        <div class="slide2">
                          <img src="">
                        </div>
                      </section>-->


          <section>
            <div class="slide4">
              <h1>New Year, New Home!</h1>
              <div class="para">Save 15% or more when you book<br>and stay before 1 April 2024!<br>
                <div class="btn6"><button>Find Early 2024 Deals!</button></div>
              </div>
            </div>
        </div>


        <div class="controls">
          <span class="arrow left"><i class="material-symbols-outlined">
              arrow_back_ios
            </i></span>
          <span class="arrow right"><i class="material-symbols-outlined">
              arrow_forward_ios
            </i></span>
          <ul>
            <li class="selected"></li>
            <li></li>
            <li></li>

          </ul>
        </div>

      </div>
    </div>
    <script src="2slide.js"></script>

<!-- room start -->
<?php include "myapp/db.php"; ?>
<?php
              $count = 1;
              $sel_query = "SELECT * FROM tenant ORDER BY id DESC;";
              $result = mysqli_query($con, $sel_query);
              while ($row = mysqli_fetch_array($result)) {
                $fileName = $row["image_name"];
                ?>
    <div class="container">
      <div class="room-picture">

        <div class="wishlist"><i class="heart-icon fas fa-heart" onclick="toggleHeartIcon(this)"></i></div>
        <img src="myapp/uploads/<?php echo $fileName; ?>" alt="Room Picture" width="500px" height="300px">      </div>

      <div class="room-description">
        <div class="discount-tag">20% OFF!</div>
        <h2>Room Details</h2>
        <p>Number of Bedrooms:<strong><?php echo $row["total_rooms"]; ?></strong></p>
        <p>Cost:<strong>₹<?php echo $row["cost"] ?> per month</strong></p>
        <p>Address:<strong><?php echo $row["address"] ?></strong></p>
        <a href="room.php?id=<?php echo $row["id"]; ?>">Ready to Move</a>
      </div>
    </div>
    <?php $count++;
              } ?>
<!-- room end -->

  </main>

  <footer class="footer">
    <div class="socialmedia">
      <a href=""> <i class="fa-brands fa-facebook"></i> </a>
      <a href=""> <i class="fa-brands fa-instagram"></i> </a>
      <a href=""> <i class="fa-brands fa-twitter"></i> </a>
      <a href=""> <i class="fa-brands fa-youtube"></i> </a>
      <a href=""> <i class="fa-brands fa-linkedin"></i> </a>

    </div>
    &copy; 2023 Urban Nest. All rights reserved.

  </footer>
  <script src="wish.js"></script>
  <script src="index.js"></script>
  <script>
    function openWebsite() {
        // URL of the website you want to open
        var url = 'search1.html';
    
        // Open the website in a new tab
        window.open(url);
    }
    </script>

</body>

</html>