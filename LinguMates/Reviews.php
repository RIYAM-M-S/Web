<?php
session_start();

define("DBHOST", "localhost");
define("DBUSER", "root");
define("DBPWD", "");
define("DBNAME", "lingumatesdb");

$conn = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

$partner_photo = '';
$partner_name = '';
$partner_description = '';
$partner_proficiency = '';
if (!empty($email)) {
    $query = "SELECT photo, firstName, lastName, bio, partnerID FROM languagepartners WHERE email = ?";
    $stmt_partner = mysqli_prepare($conn, $query);
    if ($stmt_partner) {
        mysqli_stmt_bind_param($stmt_partner, "s", $email);
        mysqli_stmt_execute($stmt_partner);
        $result_partner = mysqli_stmt_get_result($stmt_partner);
        if ($result_partner && mysqli_num_rows($result_partner) > 0) {
            $row_partner = mysqli_fetch_assoc($result_partner);
            $partner_photo = $row_partner['photo'];
            $partner_name = $row_partner['firstName'] . ' ' . $row_partner['lastName'];
            $partner_description = $row_partner['bio'];
            $partnerID = $row_partner['partnerID'];
            $bio = $partner_description; 
        }
        
        mysqli_stmt_close($stmt_partner);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rating & Reviews</title>
    <link rel="stylesheet" href="LHP.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-GLhlTQ8i1IIVoaFaLcl5wjKOBn0Kk/RUdFEae83a6ho1NFwnfA5qBLF85fwApQ" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/9eeac525af.js" crossorigin="anonymous"></script>

    <style>
        .round-image {
            width: 60px;
            height: 60px;
            border-radius: 50%; 
            border: 2px solid #333; 
            
        }
        .round-image2 {
            width: 150px;
            height: 150px;
            border-radius: 50%; 
            border: 2px solid #333; 
            
        }
        h2 {
            text-align: center;
            font-weight: 500;
            color:#333
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="NS_homepage.php">
                    <img src="logo.png" alt="Logo">
                </a>
            </div>
          
            <div class="links">
                <ul>
                    <li>
                        <a href="NativeProfilePage.html">
                            <?php 
                            if (!empty($partner_photo)) {
                                echo "<img src='images/" . htmlspecialchars($partner_photo) . "' alt='User' class='round-image'>";
                            }
                            ?>
                        </a>
                    </li>
                    <li><a href="LinguMates/LinguMates/signOut.php">Sign out</a></li>
                </ul>
            </div>
        </div>
    </header>

    <header>
        <div>
            <a href="learnerProfile.html">
                <?php 
                if (!empty($partner_photo)) {
                    echo "<img src='images/" . htmlspecialchars($partner_photo) . "' alt='User' class='round-image'>";
                }
                ?>
            </a>
        </div>
        <h1 class="hero-title"><?php echo htmlspecialchars($partner_name); ?></h1>
        <p class="hero-description"><?php echo htmlspecialchars($bio); ?></p>
        <h4 class="hero-description">Proficiency Level in English: Advanced</h4>
        <h4 class="hero-description">Rating:</h4>
        
        <?php
            
            $sql = "SELECT AVG(rating) AS avg_rating FROM reviews_ratings  WHERE partnerID = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $partnerID); 
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            $roundedRating = floor($row['avg_rating']);

            
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $roundedRating) {
                    echo '<i class="fa-solid fa-star" style="color: #FFD43B;"></i>'; 

                } else {
                    echo '<i class="fa-regular fa-star" style="color: #FFD43B;"></i>';
                }
            }

            mysqli_stmt_close($stmt);
        ?>
    </header>

    <div class="time-load-section">
        <div class="container">
           
            <h1 class="section-title">See some Reviews of <?php echo htmlspecialchars($partner_name); ?>!</h1>
            <p class="section-description">Read about the experience of <?php echo htmlspecialchars($partner_name); ?>'s students</p>

            <div class="row">
                <?php
                   
                    $sql = "SELECT r.learnerID, r.rating, r.review, l.firstName, l.lastName
                            FROM reviews_ratings r
                            JOIN learners l ON r.learnerID = l.learnerID";
                    $result = mysqli_query($conn, $sql);

                    
                    if(mysqli_num_rows($result) > 0) {
                        echo "<div class='row'>";
                        while ($row = mysqli_fetch_assoc($result)) {
                            $roundedRating = floor($row['rating']);
                            echo "<div class='card'>";
                            echo "<h1 class='card-timing'>" . $row['firstName'] . " " . $row['lastName'] . "</h1>";
                            echo "<p class='card-description'>";
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $roundedRating) {
                                    echo '<i class="fa-solid fa-star" style="color: #FFD43B;"></i>';
                                } else {
                                    echo '<i class="fa-regular fa-star" style="color: #FFD43B;"></i>';
                                }
                            }
                            echo "</p>";
                            echo "<p class='card-description'>" . $row['review'] . "</p>";
                            echo "</div>";
                        }
                        echo "</div>";
                    } else {
                        echo "<h2>No reviews available</h2>";
                    }
                ?>
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
</body>
</html>
