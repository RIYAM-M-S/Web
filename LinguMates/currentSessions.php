<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lingumatesdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch current sessions
$sql = "SELECT 
        sessions.sessionID, 
        sessions.scheduledTime, 
        sessions.duration, 
        requests.language, 
        requests.proficiencyLevel,
        requests.status,
        languagePartners.firstName, 
        languagePartners.lastName, 
        languagePartners.photo,
        languagePartners.partnerID
        FROM 
        sessions
        INNER JOIN 
        requests ON sessions.requestID = requests.requestID
        INNER JOIN 
        languagePartners ON requests.partnerID = languagePartners.partnerID
        WHERE 
        sessions.learnerID = ? 
        AND requests.preferredSchedule > CURDATE()
        AND requests.time > CURTIME()
        ORDER BY 
        sessions.scheduledTime DESC";

// Prepare and bind
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION["userID"]);

// Execute the query
$stmt->execute();

// Get result
$result = $stmt->get_result();

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

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <script src="https://kit.fontawesome.com/9eeac525af.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="footer.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-GLhlTQ8i1IIVoaFaLcl5wjKOBn0Kk/RUdFEae83a6ho1NFwnfA5qBLF85fwApQ" crossorigin="anonymous">      
    <title>Current Sessions</title>
    <link rel="stylesheet" href="Langrequest.css">
    <style>
        .round-image {
            width: 60px;
            height: 60px;
            border-radius: 50%; /* This will make the image round */
            border: 2px solid #333; /* This will create a dark outline */
        }
        .partner-photo {
            width: 60px; /* Adjust the width as needed */
            height: 60px; /* Adjust the height as needed */
            border-radius: 50%; /* This will make the image round */
            border: 2px solid #333; /* This will create a dark outline */
        }
        .no-session {
            text-align: center;
            margin-top: 20px;
        }
        h2 {
            text-align: center;
            font-weight: 500;
            color:#333;
            font-family: Arial, sans-serif; /* Font declaration */
        }

    </style>
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
                            <a href="learnerProfile.php" style="margin-right: 20px;">profile</a>
                                
                            
                            
                        </li>
                      
                        <li><a href="SignOut.php">Sign out</a></li>
                     
                    </ul>
                </div>
            </div>
        </header>
        
        <!-- Current Sessions UI -->
        <div class="container">    
            <?php if ($result->num_rows > 0) { ?>
                <h1>Your current Sessions</h1>
                <div>
                    <table>
                        <tr id="catigory">
                            <th>Partner</th>
                            <th>Language</th>
                            <th>Proficiency</th>
                            <th>Schedule</th>
                            <th>Duration</th> 
                            <th>Action</th> 
                            <th>Action</th>
                        </tr> 

                        <?php
                            // Output data of each row
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                // Retrieve partner's photo from the database
                                $query_photo = "SELECT photo FROM languagePartners WHERE partnerID = ?";
                                $stmt_photo = $conn->prepare($query_photo);
                                if ($stmt_photo) {
                                    $partnerID = $row["partnerID"];
                                    $stmt_photo->bind_param("i", $partnerID);
                                    $stmt_photo->execute();
                                    $result_photo = $stmt_photo->get_result();
                                    if ($result_photo && $result_photo->num_rows > 0) {
                                        $row_photo = $result_photo->fetch_assoc();
                                        $photo = $row_photo['photo'];
                                        // Display partner's photo
                                        echo "<td><img src='images/$photo' alt='Partner' class='partner-photo'></td>";
                                    } else {
                                        // If photo not found, display a default image or text
                                        echo "<td>Photo not available</td>";
                                    }
                                    $stmt_photo->close();
                                }

                                echo "<td>" . $row["language"] . "</td>";
                                echo "<td>" . $row["proficiencyLevel"] . "</td>";
                                echo "<td>" . $row["scheduledTime"] . "</td>";
                                echo "<td>" . $row["duration"] . " min</td>";
                                if($row["status"] == 'accepted') {
                                    echo "<td><a href='payment' id='save-btn'>pay</a></td>";
                                }
                                echo "<td><a href='StartingLivePage.html' id='save-btn'>Go to Session</a></td>";
                                echo "</tr>";
                            }
                        ?>
                    </table>
                </div>
            <?php } else {
                echo "<h2 class='no-session'>No current sessions found.</h2>";
            } ?>
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
    </div>
</body>
</html>
