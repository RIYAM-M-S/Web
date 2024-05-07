<?php
session_start();

$host = "localhost";
$dbname = "lingumatesdb";
$username = "root";
$password = "";

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection error: " . $mysqli->connect_errno);
}

$firstName = $lastName = '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

// Sign out logic
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["signout"])) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to homepage after sign out
    header("Location: Homepage.php");
    exit;
}

if (!empty($email)) {
    $query = "SELECT firstName, lastName , photo FROM languagepartners WHERE email = '$email'";
    $result = $mysqli->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $firstName = $row['firstName'];
        $lastName = $row['lastName'];
        $photo = $row['photo'];
    } else {
        echo "Error: No results found for the given email.";
    }
} else {
    echo "Error: Session email is not set.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Native speaker home page</title>
    <link rel="stylesheet" href="LHP.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-GLhlTQ8i1IIVoaFaLcl5wjKOBn0Kk/RUdFEae83a6ho1NFwnfA5qBLF85fwApQ" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/9eeac525af.js" crossorigin="anonymous"></script>
    <style>
        .round-image {
            width: 150px;
            height: 150px;
            border-radius: 50%; /* This will make the image round */
            border: 2px solid #333; /* This will create a dark outline */            
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="NS_homepage.html">
                    <img src="logo.png" alt="Logo">
                </a>
            </div>
          
            <div class="links">
                <ul>
                    <li>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return confirm('Are you sure you want to sign out?');">
                            <input type="hidden" name="signout" value="true">
                            <a href="#" class="signout" onclick="this.closest('form').submit();">Sign out</a>
                        </form>
                    </li>
                </ul>
            </div>
    </div>

    </header>

    <header class="hero-section">
        <a href="NativeProfilePage.html">
        <img src="images/<?php echo $photo; ?>" alt="User" class="round-image">
          </a>
          <h2 class="hero-title"><?php echo $firstName . ' ' . $lastName; ?></h2>
      <h1 class="hero-title">Welcome Back to Your Language Tutoring Dashboard!</h1>
      <p class="hero-description">Ready to take your language tutoring journey to the next level? Check out your language learning request now to start your journey!</p>
      <button class="hero-btn">
          <a href="NS_request.html">View Requests</a>
      </button>
  </header>
  <div class="time-load-section">
    <div class="container">
      <h1 class="section-title">
        Enjoy Your Journey of teaching a New Language!
    </h1>
    <p class="section-description">
        Connect with your students and join in interactive sessions!
    </p>

        <div class="row">
            <div class="card">
                <h1 class="card-timing">
                     Rating & Reviews
                </h1>
                <p class="card-description">
                  Check out therating and reviews from your students!
                </p>
                <button class="hero-btn">
                  <a href="Reviews.php">View </a>
                </button>
            </div>

            <div class="card">
                <h1 class="card-timing">
                     Current Sessions
                </h1>
                <p class="card-description">
                    Check out your upcoming language learning sessions!
                </p>
                <button class="hero-btn">
                  <a href="CurrentSessionsPage.html">View</a>
                </button>
            </div>
            <div class="card">
              <h1 class="card-timing">
                Previous Sessions
              </h1>
              <p class="card-description">
                  Check out your Previous language learning sessions!
              </p>
              <button class="hero-btn">
                <a href="PreviousSessionsPage.php">View</a>
              </button>
          </div>
            
        </div>
    </div>
</div>

  
    

    <footer>
        <div class="footerContainer">
            <div class="socialicon">
                <a href="https://facebook.com"><i class="fab fa-facebook"></i></a>
                <a href="https://www.instagram.com/?hl=ar"><i class="fab fa-instagram"></i></a>
                <a href="https://twitter.com"><i class="fab fa-twitter"></i></a>
                <a href="https://www.youtube.com/"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
        
        <div class="footerNav">
            <ul>
                <li><a href="aboutus.html">About us</a></li>
                <li><a href="mailto:lingumates@gmail.com">Contact us</a></li>
            </ul>
        </div>
    </footer>
         <div class="footerBottom">
          <p>&copy; LinguMates, 2024;  </p>
      </div>
 <!-- Back to Top Button -->
 <button id="back-to-top" onclick="backToTop()"><i class="fas fa-arrow-up"></i></button>

<!-- JavaScript section -->
<script>
  // Display current date and time
  function displayDateTime() {
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: true };
    const dateTimeString = now.toLocaleDateString('en-US', options);
    document.getElementById('dateTime').textContent = dateTimeString;
  }

  // Redirect to Partner Page
  function redirectToPartnerPage() {
    window.location.href = "viewPartner.html";
  }

  // Back to Top Button
  window.onscroll = function() {scrollFunction()};

  function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
      document.getElementById("back-to-top").style.display = "block";
    } else {
      document.getElementById("back-to-top").style.display = "none";
    }
  }

  function backToTop() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
  }


  // Call displayDateTime function
  displayDateTime();
  // Refresh date and time every second
  setInterval(displayDateTime, 1000);
</script>


    </body>
    
    </html>
