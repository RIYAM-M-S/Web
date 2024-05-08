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

$query = "SELECT firstName, lastName, photo, email, password, city, location FROM learners WHERE email = '$email'";
$result = $mysqli->query($query);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $firstName = $row['firstName'];
    $lastName = $row['lastName'];
    $photo = $row['photo'];
    $email = $row['email'];
    $password = $row['password'];
    $location = $row['location'];
    $city = $row['city'];
} else {
    echo "Error: No results found for the given email.";
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete"])) {
 
    $delete_query = "DELETE FROM learners WHERE email = '$email'";
    $result = $mysqli->query($delete_query);

    if ($result) {
        $_SESSION = array();

        session_destroy();

        header("Location: Homepage.php");
        exit;
    } else {
        echo "Error deleting account: " . $mysqli->error;
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<script src="https://kit.fontawesome.com/9eeac525af.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="footer.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-GLhlTQ8i1IIVoaFaLcl5wjKOBn0Kk/RUdFEae83a6ho1NFwnfA5qBLF85fwApQ" crossorigin="anonymous">
<title>Personal Profile</title>
<style>
 body {
          margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f1f8fc;
            padding-bottom: 50px; 
        } 
    
        #profile-container {
            display: flex;
            justify-content: space-around;
            padding: 20px;
        }

        #user-image img{
            border-radius: 50% !important;;
            width: 200px;
            height: 200px;
            background-color: #ddd; 
        }

        #info-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }
        .info-box {
   background-color: #f9f9f9;
   padding: 10px;
   margin-bottom: 10px;
   border-radius: 10px;
   width: 500px;
   font-size: 20px; 
}

        #edit-btn {
            padding: 15px;
            background-color: #aaa;
            color: #f1f8fc;
            border: none;
            width: 100px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 20px; 
        }
        #Delete-btn {
            padding: 15px;
            background-color: #aaa;
            color: #ca1b1b;
            border: none;
            width: 100px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 20px; 
        }
        .button-container {
    display: flex;
    justify-content:20px; 
}

.edit-btn,
.delete-btn {
    padding: 10px 20px;
    background-color: #e7e7f0;
    color: rgb(22, 46, 86);
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 15px;
    margin: 0 5px;
    display: inline-block; 
    vertical-align: middle; 
}
                 
.round-image {
 width: 60px;
 height: 60px;
  border-radius: 50%; 
 border: 2px solid #333; 
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
                    <li><a href="Homepage.php">Sign out</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div id="profile-container">
        <div id="user-image">
        <img src="images/<?php echo $photo; ?>" alt="User" class="round-image">
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
        <strong>Email:</strong> <?php echo strlen($email) > 40 ? substr($email, 0, 40) . '...' : $email; ?>
    </div>
                                           
        <div class="info-box">
    <strong>Password:</strong> <?php echo strlen($password) > 40 ? substr($password, 0, 40) . '...' : $password; ?>
</div>

        <div class="info-box">
            <strong>Location:</strong> <?php echo strlen( $location) > 40 ? substr( $location, 0, 40) . '...' :  $location; ?>
        </div>   

        <div class="info-box">
        <strong>City:</strong> <?php echo strlen($city) > 40 ? substr($city, 0, 40) . '...' : $city; ?>
    </div>

    <div class="button-container" style="display: flex; justify-content: center; align-items: center; margin-top: 10px;">
    <a href="editProfile.php" class="edit-btn" style="padding: 10px 20px; background-color: #e7e7f0; color: rgb(22, 46, 86); border: none; border-radius: 5px; cursor: pointer; font-size: 15px; margin: 0 5px;" onmouseover="this.style.backgroundColor='#5bacdf'; this.style.color='white';" onmouseout="this.style.backgroundColor='#e7e7f0'; this.style.color='rgb(22, 46, 86)';">Edit</a>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" onsubmit="return confirm('Are you sure you want to delete your account?');">
        <input type="hidden" name="delete" value="1">
        <button type="button" class="delete-btn" onclick="this.closest('form').submit();" style="padding: 10px 20px; background-color: #e7e7f0; color: rgb(22, 46, 86); border: none; border-radius: 5px; cursor: pointer; font-size: 15px; margin: 0 5px;" onmouseover="this.style.backgroundColor='red'; this.style.color='white';" onmouseout="this.style.backgroundColor='#e7e7f0'; this.style.color='rgb(22, 46, 86)';">Delete</button>
    </form>
    
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
    <p>&copy; LinguMates, 2024;  </p>
</div>
</body>
</html>
