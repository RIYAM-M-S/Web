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

$sql_sessions = "SELECT s.*, l.firstName AS learnerFirstName, l.lastName AS learnerLastName 
                FROM sessions s
                JOIN learners l ON s.learnerID = l.learnerID
                WHERE s.learnerID = ? AND s.status = 'scheduled'";
$stmt = $mysqli->prepare($sql_sessions);
$stmt->bind_param("i", $_SESSION['learnerID']);
$stmt->execute();
$result = $stmt->get_result();

$sessions = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $session = [];
        $session['language'] = $row['language'];
        $session['learnerName'] = $row['learnerFirstName'] . ' ' . $row['learnerLastName'];
        $session['proficiency'] = $row['proficiency'];
        $session['scheduledTime'] = $row['scheduledTime'];
        $session['duration'] = $row['duration'];
        $session['payment'] = $row['payment'];

        $sessions[] = $session;
    }
}
$stmt->close();
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
                                
                                    <a href="learnerProfile.php">
                                    <img src="images/user.png" alt="User" class="round-image">
                                    </a>
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
                            <td>
                                <?php if ($session['payment'] == 'paid') : ?>
                                    <a href="StartingLivePage.php" id="save-btn">Go to Session</a>
                                <?php else : ?>
                                    Waiting for Payment
                                <?php endif; ?>
                            </td>
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