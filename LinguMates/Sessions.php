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

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    $photo = '';

    if (!empty($email)) {
        $query = "SELECT photo FROM languagepartners WHERE email = ?";
        $stmt = $mysqli->prepare($query);
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $photo = $row['photo'];
            }
            $stmt->close();
        }
    }
}

$query = "SELECT partnerID FROM languagepartners WHERE email=?";
$stmt = $mysqli->prepare($query);
if ($stmt) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $partnerID = $row['partnerID'];

        $currentDateTime = date("Y-m-d H:i:s");

        $querySessions = "SELECT s.*, r.language, r.proficiencyLevel, l.firstName, l.lastName 
                  FROM sessions s 
                  JOIN requests r ON s.sessionID = r.sessionID 
                  JOIN learners l ON s.learnerID = l.learnerID 
                  WHERE s.partnerID = ? 
                  AND r.status = 'accepted' 
                  AND CONCAT(r.preferredSchedule, ' ', r.time) < ?";

                          
        $stmtSessions = $mysqli->prepare($querySessions);
        if ($stmtSessions) {
            $stmtSessions->bind_param("is", $partnerID, $currentDateTime);
            $stmtSessions->execute();
            $resultSessions = $stmtSessions->get_result();

            $sessions = [];

            if ($resultSessions->num_rows > 0) {
              while ($rowSession = $resultSessions->fetch_assoc()) {
                $session = [];
                $session['language'] = $rowSession['language'];
                $session['learnerName'] = $rowSession['firstName'] . ' ' . $rowSession['lastName'];
                $session['proficiency'] = $rowSession['proficiencyLevel'];
                $session['scheduledTime'] = $rowSession['scheduledTime'];
                $session['duration'] = $rowSession['duration'];
            
                $sessions[] = $session;
            }
            
            } else {
                echo "<h2>You don't have any current sessions</h2>";
            }
        }
    }
    $stmt->close();
} else {
    echo "<h2>Session ID not set</h2>";
}

$mysqli->close();
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
      <title>Sessions</title>
      <link rel="stylesheet" href="Langrequest.css">
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
                                <a href="learnerProfile.php"> Profile  </a>
                                </li>
                                <li><a href="Homepage.php">Sign out</a></li>
                            </ul>
                        </div>
                    </div>
                </header>
    <body>

    <div class="container">
        <h1>Your current Sessions</h1>
        <h1>  </h1>


        <div class="requestsboard">
            <?php if (empty($sessions)) : ?>
                <p>You do not have any current sessions</p>
            <?php else : ?>
                <table>
                    <tr id="catigory">
                        <th>Language</th>
                        <th>Learner Name</th>
                        <th>Proificency</th>
                        <th>Schedule</th>
                        <th>Duration</th>
                        <th></th>
                    </tr>
                    <?php foreach ($sessions as $session) : ?>
                        <tr>
                            <td><?php echo $session['language']; ?></td>
                            <td><?php echo $session['learnerName']; ?></td>
                            <td><?php echo $session['proficiency']; ?></td>
                            <td><?php echo $session['scheduledTime']; ?></td>
                            <td><?php echo $session['duration']; ?> min</td>
                            <td><?php echo '<a href="StartingLivePage.html" id="save-btn">Go to Session</a>'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
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
        </div>
    </body>
</html>