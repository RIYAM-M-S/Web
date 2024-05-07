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
$firstName = '';
$lastName = '';
$email = '';
$password = '';
$city = '';
$location = '';


$emailExist = false;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $firstName = $mysqli->real_escape_string($_POST["firstName"]);
  $lastName = $mysqli->real_escape_string($_POST["lastName"]);
  $email = $mysqli->real_escape_string($_POST["email"]);
  $password = ($_POST["password"]);
  $city = $mysqli->real_escape_string($_POST["city"]);
  $location = $mysqli->real_escape_string($_POST["location"]);

$file_name = isset($_FILES['profile-photo']['name']) ? $_FILES['profile-photo']['name'] : 'user.png';
$tempname = isset($_FILES['profile-photo']['tmp_name']) ? $_FILES['profile-photo']['tmp_name'] : '';
$folder= 'images/'. $file_name;
if (!empty($tempname) && move_uploaded_file($tempname, $folder)){
    // File uploaded successfully
} else {
    $file_name = 'user.png';
}


  $errors = [];

  $firstName = trim($_POST["firstName"]);
  if (empty($firstName)) {
      $errors['firstName'] = "First name is required";
  } elseif (!preg_match("/^[a-zA-Z'-]+$/", $firstName)) {
      $errors['firstName'] = "Invalid first name format";
  }

  $lastName = trim($_POST["lastName"]);
  if (empty($lastName)) {
      $errors['lastName'] = "Last name is required";
  } elseif (!preg_match("/^[a-zA-Z'-]+$/", $lastName)) {
      $errors['lastName'] = "Invalid last name format";
  }

  $email = trim($_POST["email"]);
  if (empty($email)) {
      $errors['email'] = "Email is required";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors['email'] = "Invalid email format";
  }

  $password = $_POST["password"];
  if (empty($password)) {
      $errors['password'] = "Password is required";
  } elseif (strlen($password) < 8) {
      $errors['password'] = "Password must be at least 8 characters long";
  }



  $city = trim($_POST["city"]);
  if (empty($city)) {
      $errors['city'] = "City is required";
  }

  $location = trim($_POST["location"]);
if (empty($location)) {
    $errors['location'] = "Location is required";
}


  $result = $mysqli->query("SELECT learnerID FROM learners WHERE email = '$email'");
  if ($result->num_rows > 0) {
      $emailExist = true;
      $_SESSION['post-data'] = $_POST;
      $_SESSION['email_exist'] = $emailExist;

  }

  $result = $mysqli->query("SELECT partnerID FROM languagepartners WHERE email = '$email'");
  if ($result->num_rows > 0) {
      $emailExist = true;
      $_SESSION['post-data'] = $_POST;
      $_SESSION['email_exist'] = $emailExist;

  }

if(!$emailExist && empty($errors)){ 
    $stmt = $mysqli->prepare("INSERT INTO learners (firstName, lastName, email, password, photo,city, location) VALUES (?, ?, ?,?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $firstName, $lastName, $email, $password, $file_name,$city, $location);

    if ($stmt->execute()) {
      $_SESSION["email"] = $_POST["email"];
      header("Location: LearnerHP.php");
        exit();
    } else {
        header("Location: SignUpLangLear.php");
        exit(); 
    }   
  }}


?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script
      src="https://kit.fontawesome.com/64d58efce2.js"
      crossorigin="anonymous"
    ></script>
    <script src="https://kit.fontawesome.com/9eeac525af.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="SignUpLangLear.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="footer.css?v=<?php echo time(); ?>">

    <title>Language learner sign up</title>

    <style>
      .error-message{
        color: red; 
        width: 500px;
        font-size: small;
      }

    </style>
  </head>
  <body>
      <div class="big-wrapper light">
   <header>
        <div class="container">
          <div class="logo">
              <a href="Homepage.php">
                  <img src="logo.png" alt="Logo">
              </a>
               </div>
         
            </div>
          </header>

           </div>


      <div class="forms-container">

        <div class="signin-signup">

        <form action="#" class="sign-in-form" method="post" enctype="multipart/form-data">
            <h2 class="title">Sign Up</h2>

            <?php if ($emailExist): ?>
            <div class='error-message' style='text-align:center;'>
                <p>This email is used. Please try another one.</p>
            </div>
        <?php endif; ?>


        <div class="input-field">
              <i class="fas fa-user"*></i>
              <input id="fName" type="text" placeholder="First name" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>"> 
              <span class="input-suffix">*</span>
              <span class="error-message"><?php echo isset($errors['firstName']) ? $errors['firstName'] : ''; ?></span>
            
              
              <div class="file-input">
    <input type="file" id="profile-photo" name="profile-photo" accept="image/*" onchange="previewPhoto(event)" />
    <img id="selected-photo" src="images/user.png" alt="Selected photo">
</div>

            </div>
            
            <div class="input-field">
              <i class="fas fa-user"></i>
              <input id="LName" type="text" placeholder="Last name" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>">
              <span class="input-suffix">*</span>
              <span class="error-message" ><?php echo isset($errors['lastName']) ? $errors['lastName'] : ''; ?></span>
            </div>

            <div class="input-field">
              <i class="fa-solid fa-envelope"></i>
              <input id="email" type="text" placeholder="Email" name="email" value="<?php echo htmlspecialchars($email); ?>">
              <span class="input-suffix">*</span>
              <span class="error-message" ><?php echo isset($errors['email']) ? $errors['email'] : ''; ?></span>
            </div>
            
            <div class="input-field">
              <i class="fas fa-lock"></i>
              <input id="password" type="password" placeholder="Password" name="password" />
              <span class="input-suffix">*</span>
              <span class="error-message" ><?php echo isset($errors['password']) ? $errors['password'] : ''; ?></span>
            </div>

             <div class="input-field">
              <i class="fa-solid fa-city"></i>  
              <input id="city" type="text" placeholder="City" name="city" value="<?php echo htmlspecialchars($city); ?>">
              <span class="input-suffix">*</span>
              <span class="error-message"   ><?php echo isset($errors['city']) ? $errors['city'] : ''; ?></span>
            </div>

            <div class="input-field">
              <i class="fa-solid fa-city"></i>  
              <input id="location" type="text" placeholder="Location" name="location" value="<?php echo htmlspecialchars($location); ?>">
              <span class="input-suffix">*</span>
              <span class="error-message"   ><?php echo isset($errors['location']) ? $errors['location'] : ''; ?></span>
            </div>

            <input type="submit" name="submit" value="Sign up" class="btn" />
        </form>

        </div>
      </div>

      <div class="panels-container">
        <div class="panel left-panel">
          <div class="content">
            <h2>One of us ?</h2>
            <p>
              Welcome back learners! Unleash the language magic. Sign in now for shared brilliance
            </p><br>
            <a href="Homepage.php" class="btn">Sign in</a>
          </div>
        </div>

       
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
  <script>
    function previewPhoto(event) {
        var input = event.target;
        var reader = new FileReader();
        reader.onload = function() {
            var img = document.getElementById('selected-photo');
            img.src = reader.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
</script>

  </body>
</html>
