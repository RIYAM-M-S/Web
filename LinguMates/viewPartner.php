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

$sql_partners = "SELECT languagepartners.*,
                AVG(RatePartners.rating) AS avg_rating,
                GROUP_CONCAT(partner_languages.language SEPARATOR ', ') AS languages
                FROM languagepartners
                LEFT JOIN RatePartners ON languagepartners.partnerID = RatePartners.partnerID
                LEFT JOIN partner_languages ON languagepartners.partnerID = partner_languages.partnerID
                GROUP BY languagepartners.partnerID";


$result_partners = $mysqli->query($sql_partners);
$partners = [];

if ($result_partners->num_rows > 0) {
    while ($row = $result_partners->fetch_assoc()) {
        $partners[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    <link rel="stylesheet" href="Homepage.css" />
    <link rel="stylesheet" href="menu.css" />
    <link rel="stylesheet" href="LHP.css" />
    <link rel="stylesheet" href="viewPartner.css" />
    <link rel="stylesheet" href="footer.css" />
    <link rel="stylesheet" href="footer.css?v=<?php echo time(); ?>">
    <style>
        .round-image {
            width: 60px;
            height: 60px;
            border-radius: 50%; /* This will make the image round */
            border: 2px solid #333; /* This will create a dark outline */
        }
    </style>
</head>
<body>
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
                        <a href="learnerProfile.php">
                        <?php echo '<img src="' . $partner['profile_pic'] . '" alt="User" class="round-image">'; ?>
                        </a>
                    </li>
                    <li><a href="Homepage.php">Sign out</a></li>
                </ul>
            </div>
        </div>
    </header>

<div class="row1">
        <?php
        if (!empty($partners)) {
            foreach ($partners as $partner): {
                // Extract partner's specific information
                $name = $partner['firstName'];
                $targetLanguage = $partner['languages'];
                $avgRating = $partner['avg_rating']; // Average rating
                $pricePerHour = $partner['sessionPricePerHour'];
                $partnerEmail = $partner['email']; // Retrieve partner's email

                // Output partner's information
                echo '<div class="box1">';
                echo '<div class="div-6">';
                echo '<img src="images/' . $partner['photo'] . '" alt="User" style="width: 90px; height: 90px; position: relative; border-radius: 90px; border-color: rgb(60, 59, 59);">';
                echo '<h2 class="h2">' . $name . '</h2>';
                echo '<p class="p">Target language: ' . $targetLanguage . '</p>';
                echo '<p class="p">Average Rating: ' . number_format($avgRating, 1) . ' / 5</p>'; // Display average rating
                echo '<p class="p">Price per Hour: $' . $pricePerHour . '</p>';
                echo '</div>';

                echo '<a class="button" href="mailto:' . $partnerEmail . '">Contact</a>';
                echo '<a class="button" href="Request.php">Make a Request</a>';
                echo '<a class="button" href="edit_request.php">Edit Request</a>';
                echo '</div>';
            } endforeach;
          } else {
            echo "No language partners found.";
        }
        ?>
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
        <p>&copy; LinguMates, 2024;</p>
    </div>
</body>
</html>
