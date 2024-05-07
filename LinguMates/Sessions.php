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

$sql_sessions = "SELECT * FROM sessions WHERE learnerID = ?";
$stmt = $mysqli->prepare($sql_sessions);
$stmt->bind_param("i", $_SESSION['learnerID']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $sessions = [];

    while ($row = $result->fetch_assoc()) {
        $sessions[] = $row;
    }
}
$stmt->close();

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
                            <a href="LearnerHP.html">
                                <img src="logo.png" alt="Logo">
                            </a>
                        </div>
                    
                        <div class="links">
                            <ul>
                                <li>
                                
                                    <a href="learnerProfile.html">
                                    <img src="images/user.png" alt="User" class="round-image">
                                    </a>
                                </li>
                                <li><a href="Homepage.html">Sign out</a></li>
                            </ul>
                        </div>
                    </div>
                </header>
                
<div class="container">
    <h1>Your current Sessions</h1>
    <h1><br></h1>

    <div class="requestsboard">

    <?php if (empty($sessions)) : ?>
            <p>You do not have any current sessions.</p>
        <?php else : ?>
            <table>
                <tr id="catigory">
                    <th></th>
                    <th>Language</th>
                    <th>Partner Name</th>
                    <th>Proificency</th>
                    <th>Schedule</th>
                    <th>Duration</th>
                    <th>Payment Status</th>
                    <th></th>
                </tr>

                <?php foreach ($sessions as $session) : ?>
                    <?php
                    // Extract session information
                    $language = $session['language'];
                    $partnerID = $session['partnerID'];
                    $proficiency = $session['proficiency'];
                    $schedule = $session['scheduledTime'];
                    $duration = $session['duration'];
                    $paymentStatus = $session['payment'];

                    // Retrieve partner's name from languagePartners table based on partnerID
                    $sql_partner = "SELECT firstName, lastName FROM languagePartners WHERE partnerID = $partnerID";
                    $result_partner = mysqli_query($mysqli, $sql_partner);
                    if ($row_partner = mysqli_fetch_assoc($result_partner)) {
                        $partnerName = $row_partner['firstName'] . ' ' . $row_partner['lastName'];
                        
                    } else {
                        $partnerName = 'N/A';
                    }
                    if ($session['status'] == 'scheduled') :
                    ?>

                    <tr>
                        <td><img src="u.png" alt="request Icon" class="requestIcon"></td>
                        <td><?php echo $language; ?></td>
                        <td><?php echo $partnerName; ?></td>
                        <td><?php echo $proficiency; ?></td>
                        <td><?php echo $schedule; ?></td>
                        <td><?php echo $duration; ?> min</td>
                        <td><?php echo $paymentStatus; ?></td>
                        <td>
                        <?php if ($paymentStatus == 'paid') : ?>
    <a href="StartingLivePage.html" id="save-btn">Go to Session</a>
<?php else : ?>
    Payment Required
<?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
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