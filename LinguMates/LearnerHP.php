<?php
// Session start
session_start();

// Database connection
$host = "localhost";
$dbname = "lingumatesdb";
$username = "root";
$password = "";
$mysqli = new mysqli($host, $username, $password, $dbname);
if ($mysqli->connect_error) {
    die("Connection error: " . $mysqli->connect_errno);
}

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

// Initialize variables
$firstName = $lastName = $photo = '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

if (!empty($email)) {
    $query = "SELECT firstName, lastName, photo FROM learners WHERE email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

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
    <title>Language learner homepage</title>
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

        /* Back to Top Button CSS */
        #back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #333;
            color: #fff;
            width: 50px;
            height: 50px;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: none;
        }
       
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="LearnerHP.html">
                    <img src="logo.png" alt="Logo">
                </a>
            </div>
          
            <div class="links">
                <ul>
                    <li>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return confirm('Are you sure you want to sign out?');">
                            <input type="hidden" name="signout" value="true">
                            <input type="submit" class="signout" value="Sign out">
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <header class="hero-section">
        <a href="learnerProfile.php"> 
            <img src="<?php echo $photo; ?>" alt="User" class="round-image">
        </a>
        
        <h2 class='hero-title'><?php echo "$firstName $lastName"; ?></h2>
          
        <h1 class="hero-title">Welcome Back to Your Language Learning Dashboard!</h1>
        <p class="hero-description">Ready to take your language learning journey to the next level? Find your perfect language partner or tutor and post your language learning request now to start your journey!</p>
        <button class="hero-btn" onclick="redirectToPartnerPage()">
            Find a Partner
        </button>
    </header>
    <div class="time-load-section">
        <div class="container">
            <h1 class="section-title">
                Enjoy Your Journey of Learning a New Language!
            </h1>
            <p class="section-description">
                Connect with language partners and join interactive sessions tailored to your learning goals.
            </p>

            <div class="row">
                <div class="card">
                    <h1 class="card-timing">
                        Requests
                    </h1>
                    <p class="card-description">
                        Streamline your language learning journey where you can view, edit, and cancel your requests.
                    </p>
                    <button class="hero-btn">
                        <a href="LanguageRequests.html">View </a>
                    </button>
                </div>

                <div class="card">
                <h1 class="card-timing">
                     current Sessions
                </h1>
                <p class="card-description">
                    Check out our upcoming language learning sessions and join one to enhance your language skills.
                </p>
                <button class="hero-btn">
                    <a href="currentSessions.php">View </a>
                </button>
            </div>

            <div class="card">
                <h1 class="card-timing">
                      previous Sessions
                </h1>
                <p class="card-description">
                    Check out our previous language learning sessions.
                </p>
                <button class="hero-btn">
                    <a href="previousSessions.php">View </a>
                </button>
            </div>

                
            </div>
        </div>
    </div>

    
    <div class="statics-section">
        <div class="container">

            <section class="statics-section">
                <div class="container">
                    <h1 class="statics-title">
                        You've Come A Long Way!
                    </h1>
                    <p class="statics-description">
                        "Every step you take toward learning a new language brings you closer to a world of endless opportunities and connections."
                    </p>
                </div>
            </section>

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

    // Sign out function with confirmation
    function signOut() {
      if (confirm("Are you sure you want to sign out?")) {
        window.location.href = "Homepage.html";
      }
    }

    // Call displayDateTime function
    displayDateTime();
    // Refresh date and time every second
    setInterval(displayDateTime, 1000);
  </script>
</body>
</html>
