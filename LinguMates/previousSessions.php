<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Previous Sessions</title>
    <link rel="stylesheet" href="PreviousSessionsPage.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="footer.css?v=<?php echo time(); ?>">
    <script src="https://kit.fontawesome.com/9eeac525af.js" crossorigin="anonymous"></script>
</head>
<body>
<div class="big-wrapper light">
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
                        <a href="NativeProfilePage.php"> Profile</a>
                            <?php 
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

                            }
                            ?>
                    </li>
                    <li><a href="SignOut.php">Sign out</a></li>
                </ul>
            </div>
        </div>
    </header>
    <div class="table-container">
        <?php
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

                $querySessions = "SELECT s.*, r.language, l.firstName, l.lastName 
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
                
                    if ($resultSessions->num_rows > 0) {
        ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Language of the Lesson</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Learner</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($rowSession = $resultSessions->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($rowSession['language']); ?></td>
                                        <td><?= htmlspecialchars($rowSession['scheduledTime']); ?></td>
                                        <td><?= htmlspecialchars($rowSession['duration']); ?></td>
                                        <td><?= htmlspecialchars($rowSession['firstName'] . ' ' . $rowSession['lastName']); ?></td>

                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    <?php
                    } else {
                        echo "<h2>You don't have any previous sessions</h2>";
                    }
                }
            } else {
                echo "<h2>Partner not found</h2>";
            }
            $stmt->close();
        } else {
            echo "<h2>Session ID not set</h2>";
        }
        ?>
    </div>
    <footer>
        <div class="footerContainer">
            <div class="socialicon">
                <a href="https://facebook.com"><i class="fab fa-facebook"></i></a>
                <a href="https://www.instagram.com/?hl=ar"><i class="fab fa-instagram"></i></a>
                <a href="https://twitter.com"><i class="fa-brands fa-x-twitter"></i></a>
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
