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


if (!isset($_SESSION['email'])) {
    header("Location: Homepage.php");
    exit;
}

$email = $_SESSION['email'];


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["signout"])) {
    $_SESSION = array();
    session_destroy();
    header("Location: Homepage.php");
    exit;
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete"])) {
    $delete_partner_languages = "DELETE FROM partner_languages WHERE partner_email = '$email'";
    $delete_languagepartners = "DELETE FROM languagepartners WHERE email = '$email'";
    
    if ($mysqli->query($delete_partner_languages) && $mysqli->query($delete_languagepartners)) {
        header("Location: Homepage.php");
        exit;
    } else {
        echo "Error deleting user's data.";
    }
}

$query = "SELECT firstName, lastName, photo, bio, age, gender, email, password, phone, city, sessionPricePerHour FROM languagepartners WHERE email = '$email'";
$result = $mysqli->query($query);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $firstName = $row['firstName'];
    $lastName = $row['lastName'];
    $photo = $row['photo'];
    $bio = $row['bio'];
    $age = $row['age'];
    $gender = $row['gender'];
    $email = $row['email'];
    $password = $row['password'];
    $phone = $row['phone'];
    $city = $row['city'];
    $sessionPricePerHour = $row['sessionPricePerHour'];
} else {
    echo "Error: No results found for the given email.";
}


$query_languages = "SELECT language, proficiency FROM partner_languages WHERE partner_email = '$email'";
$result_languages = $mysqli->query($query_languages);

$languages_data = array();

if ($result_languages && $result_languages->num_rows > 0) {
    while ($row = $result_languages->fetch_assoc()) {
        $row['sessionPricePerHour'] = $sessionPricePerHour; 
        $languages_data[] = $row;
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Profile</title>
    <link rel="stylesheet" href="footer.css" />
    <link rel="stylesheet" href="NativeProfilePage.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-GLhlTQ8i1IIVoaFaLcl5wjKOBn0Kk/RUdFEae83a6ho1NFwnfA5qBLF85fwApQ" crossorigin="anonymous">
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
                            <form method="POST">
                                <button type="submit" name="signout">Sign out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>
        <div id="profile-container">
            <div id="user-image">
            <img src="images/<?php echo $photo; ?>" alt="User" class="round-image">
            </div>
            <div id="bio-container">
                <label><b> My Bio: </b> </label>
                <p><?php echo $bio; ?></p>
            </div>
        </div>
        <div id="info-container">
    <div class="info-box">
        <strong>First Name:</strong> <?php echo strlen($firstName) > 40 ? substr($firstName, 0, 40) . '...' : $firstName; ?>
    </div>
    <div class="info-box">
        <strong>Last Name:</strong> <?php echo strlen($lastName) > 40 ? substr($lastName, 0, 40) . '...' : $lastName; ?>
    </div>
    <div class="info-box">
        <strong>Age:</strong> <?php echo strlen($age) > 40 ? substr($age, 0, 40) . '...' : $age; ?>
    </div>
    <div class="info-box">
        <strong>Gender:</strong> <?php echo strlen($gender) > 40 ? substr($gender, 0, 40) . '...' : $gender; ?>
    </div>
    <div class="info-box">
        <strong>Email:</strong> <?php echo strlen($email) > 40 ? substr($email, 0, 40) . '...' : $email; ?>
    </div>
    <div class="info-box">           
    <strong>Password:</strong> <?php echo strlen($password) > 40 ? substr($password, 0, 40) . '...' : $password; ?>
    </div>
    <div class="info-box">
        <strong>Phone:</strong> <?php echo strlen($phone) > 40 ? substr($phone, 0, 40) . '...' : $phone; ?>
    </div>
    <div class="info-box">
        <strong>City:</strong> <?php echo strlen($city) > 40 ? substr($city, 0, 40) . '...' : $city; ?>
    </div>

    <div id="teaching-languages-container" class="info-box" style="font-family: Arial, sans-serif; font-size: 16px; background-color: #f9f9f9; border: 0px solid #ccc; border-radius: 8px; padding: 20px;">
    <h2 style="font-size: 24px; color: #333; margin-top: 0; margin-bottom: 20px;">Teaching Languages</h2>
    <table style="width: 100%; border-collapse: collapse;">
        <tr style="background-color: #eee;">
            <th style="border-bottom: 1px solid #ccc; padding: 12px; text-align: center;">Language</th>
            <th style="border-bottom: 1px solid #ccc; padding: 12px; text-align: center;">Proficiency Level</th>
            <th style="border-bottom: 1px solid #ccc; padding: 12px; text-align: center;">Price per Hour</th>
        </tr>
        <?php foreach ($languages_data as $language_row): ?>
            <tr style="height: 40px;">
                <td style="border-bottom: 1px solid #ccc; padding: 12px; text-align: center;"><?php echo $language_row['language']; ?></td>
                <td style="border-bottom: 1px solid #ccc; padding: 12px; text-align: center;"><?php echo $language_row['proficiency']; ?></td>
                <td style="border-bottom: 1px solid #ccc; padding: 12px; text-align: center;"><?php echo $language_row['sessionPricePerHour']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    </div>
</div>

<div class="button-container" style="display: flex; justify-content: center; align-items: center; margin-top: 10px;">
    <a href="NativeProfileEditPage.php" class="edit-btn" style="padding: 10px 20px; background-color: #e7e7f0; color: rgb(22, 46, 86); border: none; border-radius: 5px; cursor: pointer; font-size: 15px; margin: 0 5px;" onmouseover="this.style.backgroundColor='#5bacdf'; this.style.color='white';" onmouseout="this.style.backgroundColor='#e7e7f0'; this.style.color='rgb(22, 46, 86)';">Edit</a>
    <form method="POST">
        <button type="submit" class="delete-btn" name="delete" style="padding: 10px 20px; background-color: #e7e7f0; color: rgb(22, 46, 86); border: none; border-radius: 5px; cursor: pointer; font-size: 15px; margin: 0 5px;" onmouseover="this.style.backgroundColor='red'; this.style.color='white';" onmouseout="this.style.backgroundColor='#e7e7f0'; this.style.color='rgb(22, 46, 86)';">Delete</button>
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
<div class="footerBottom">
    <p>© LinguMates, 2024; </p>
</div>
</div>
</body>
</html>

