<!--viewPrtner.php-->
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
                        <a href="learnerProfile.html">
                            <img src="user.png" alt="User" class="round-image">
                        </a>
                    </li>
                    <li><a href="Homepage.php">Sign out</a></li>
                </ul>
            </div>
        </div>
    </header>
    <div class="row1">
        <?php
        // Establish database connection
        $connection = mysqli_connect('localhost', 'root', '', 'lingumatesdb');

        // Check connection
        if (!$connection) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Query to fetch all language partners
        $query = $query = "SELECT lp.*, rr.rating ,pl.language, pl.proficiency
        FROM languagePartners lp
        JOIN partner_languages pl ON lp.partnerID = pl.partnerID
          LEFT JOIN reviews_ratings rr ON lp.partnerID = rr.partnerID";
        $result = mysqli_query($connection, $query);

        // Check if language partners are found
        if (mysqli_num_rows($result) > 0) {
            // Loop through each language partner
            while ($row = mysqli_fetch_assoc($result)) {
                // Extract partner's specific information
                $name = $row['firstName'];
                $targetLanguage = $row['language'];
                $rating = $row['rating'];
                $pricePerHour = $row['sessionPricePerHour'];
                $partnerEmail = $row['email']; // Retrieve partner's email
                // Output partner's information
                echo '<div class="box1">';
                echo '<div class="div-6">';
                echo '<img src="u.png" alt="User" style="width: 90px; height: 90px; position: relative; border-radius: 90px; border-color: rgb(60, 59, 59);">';
                echo '<h2 class="h2">' . $name . '</h2>';
                echo '<p class="p">Target language: ' . $targetLanguage . '</p>';
                echo '<p class="p">Rating: ' . $rating . ' / 5</p>';
                echo '<p class="p">Price per Hour: $' . $pricePerHour . '</p>';
                echo '</div>';
                // Pass partner's email as a query parameter
               echo '<a class="button" href="mailto:' .$partnerEmail .'">Contact</a>';
               echo '<a class="button" href="Request.php">Make a Request</a>';
               echo '<a class="button" href="edit_request.php">Edit Request</a>';
               echo '</div>';
            }
        } else {
            // No language partners found
            echo "No language partners found.";
        }

        // Close database connection
        mysqli_close($connection);
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