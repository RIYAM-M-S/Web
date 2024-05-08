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

$rating = '';
$review = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rating = $_POST["rating"];
    $review = $mysqli->real_escape_string($_POST["review"]);

    // Check if learnerID and partnerID are set
    if (isset($_POST['learnerID']) && isset($_POST['partnerID'])) {
        $learnerID = $_POST['learnerID']; // Access learnerID from POST data
        $partnerID = $_POST['partnerID']; // Access partnerID from POST data

        $stmt = $mysqli->prepare("INSERT INTO RatePartners (learnerID, partnerID, rating, review) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("iiis", $learnerID, $partnerID, $rating, $review);

            if ($stmt->execute()) {
                // Review saved successfully
                echo "<script>alert('Your rating and review have been submitted successfully.');</script>";
            } else {
                // Error occurred while saving review
                echo "<script>alert('There was an error submitting your rating and review. Please try again later.');</script>";
            }
        }
    } else {
        // Handle the case where learnerID and/or partnerID are not provided
        echo "<script>alert('Error: LearnerID or PartnerID is missing.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/9eeac525af.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="LHP.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-GLhlTQ8i1IIVoaFaLcl5wjKOBn0Kk/RUdFEae83a6ho1NFwnfA5qBLF85fwApQ" crossorigin="anonymous">
    <link rel="stylesheet" href="confirmation.css">
    <title>Rate and Review Partner</title>
</head>
<body>
    
<div class="big-wrapper light">
    <header>
        <div class="container">
            <div class="logo">
                <a href="LearnerHP.php">
                    <img src="logo.png" alt="Logo">
                </a>
            </div>
          
            <div class="links">
                <ul>
                    <li>
                      <style>
                        .round-image {
                            width: 60px;
                            height: 60px;
                            border-radius: 50%; /* This will make the image round */
                            border: 2px solid #333; /* This will create a dark outline */
                        }
                      </style>
                      
                        <a href="learnerProfile.php">
                          <img src="user.png" alt="User" class="round-image">
                        </a>
                    </li>
                    <li><a href="Homepage.php">Sign out</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="container1">
        <h1>Leave a Review</h1>
        <label>Rate:</label>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <!-- Pass learnerID and partnerID as hidden fields -->
            <input type="hidden" name="learnerID" value="<?php echo $_POST['learnerID']; ?>">
            <input type="hidden" name="partnerID" value="<?php echo $_POST['partnerID']; ?>">
            <div class="form-group" data-rating-stars>
                <!-- Input for rating -->
                <input type="radio" name="rating" value="5" id="5">
                <label for="5">☆</label>
                <input type="radio" name="rating" value="4" id="4">
                <label for="4">☆</label>
                <input type="radio" name="rating" value="3" id="3">
                <label for="3">☆</label>
                <input type="radio" name="rating" value="2" id="2">
                <label for="2">☆</label>
                <input type="radio" name="rating" value="1" id="1">
                <label for="1">☆</label>
            </div>
            <div class="form-group">
                <label for="review">Review:</label>
                <textarea id="review" name="review"></textarea>
            </div>
            <button id="button1" type="submit">Submit</button>
        </form>
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

</div>

<!-- Link to jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<!-- custom js file link  -->
<script src="RatePartner.js"></script>
<script src="js/script.js"></script>

<div class="footerBottom">
    <p>&copy; LinguMates, 2024;</p>
</div>
   
</body>
</html>
