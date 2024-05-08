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

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}


function containsNumber($str) {
    return preg_match('/\d/', $str);
}

$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {


   
     $newbio = trim($_POST["bio"]);
     if (empty($newbio)) {
         $errors['bio'] = "Bio is required";
     }

    $newFirstName = trim($_POST["first_name"]);
    if (empty($newFirstName)) {
        $errors['first_name'] = "First name is required";
    } elseif (!preg_match("/^[a-zA-Z'-]+$/", $newFirstName)) {
        $errors['first_name'] = "Invalid first name format";
    }


    $newLastName = trim($_POST["last_name"]);
    if (empty($newLastName)) {
        $errors['last_name'] = "Last name is required";
    } elseif (!preg_match("/^[a-zA-Z'-]+$/", $newLastName)) {
        $errors['last_name'] = "Invalid last name format";
    }

    $newBio = $_POST['bio'];
     

    $newPassword = $_POST["password"];
    if (empty($newPassword)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($newPassword) < 8) {
        $errors['password'] = "Password must be at least 8 characters long";
    }

    $newPhone = trim($_POST["phone"]);
    if (!empty($newPhone) && !preg_match("/^\d{3}-\d{8}$/", $newPhone)) {
        $errors['phone'] = "Enter phone as XXX-XXXXXXXX.";
    }


    $newCity = trim($_POST["city"]);
    if (empty($newCity)) {
        $errors['city'] = "City is required";
    }

    $newGender = $gender; 



    $newEmail = trim($_POST["new_email"]);
    if (empty($newEmail)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }


    $newAge = $_POST["age"];
    if (empty($newAge)) {
        $errors['age'] = "Age is required";
    } elseif (!is_numeric($newAge) || $newAge<= 0|| $newAge > 100) {
        $errors['age'] = "Age must be a number and at least 18 years old";
    }

    $newSessionPricePerHour = $_POST["session_price"];
    if (empty($newSessionPricePerHour)) {
        $errors['session_price'] = "Session price is required";
    } elseif (!is_numeric($newSessionPricePerHour) || $newSessionPricePerHour  < 0 || $newSessionPricePerHour>30) {
        $errors['session_price'] = "Session price should be a positive number not more than 30";
    }


    if (!isset($_POST['proficiency'])) {
        $errors['proficiency'] = "Please select proficiency levels for all languages";
    }


    if (empty($errors)) {
        if ($newEmail != $email) {
            $updatePartnerLanguagesEmailQuery = "UPDATE partner_languages SET partner_email='$newEmail' WHERE partner_email='$email'";
            if ($mysqli->query($updatePartnerLanguagesEmailQuery) === FALSE) {
                echo "Error updating email in partner_languages table: " . $mysqli->error;
            }
            

            $updateEmailQuery = "UPDATE languagepartners SET email='$newEmail' WHERE email='$email'";
            if ($mysqli->query($updateEmailQuery) === TRUE) {

                $_SESSION['email'] = $newEmail;
                $email = $newEmail; 
                echo "Email updated successfully.";
            } else {
                echo "Error updating email: " . $mysqli->error;
            }
        }
        

        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "images/";
            $target_file = $target_dir . basename($_FILES["profile_photo"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));


            $check = getimagesize($_FILES["profile_photo"]["tmp_name"]);
            if ($check !== false) {

                if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
                    $photo = basename($_FILES["profile_photo"]["name"]);
                    $updatePhotoQuery = "UPDATE languagepartners SET photo='$photo' WHERE email='$email'";
                    if ($mysqli->query($updatePhotoQuery) === TRUE) {
                        echo "Photo updated successfully.";
                    } else {
                        echo "Error updating photo: " . $mysqli->error;
                    }
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            } else {
                echo "File is not an image.";
            }
        }  


        foreach ($_POST['proficiency'] as $index => $proficiency_level) {
            $language = $languages_data[$index]['language'];


            $updateProficiencyQuery = "UPDATE partner_languages SET proficiency='$proficiency_level' WHERE partner_email='$email' AND language='$language'";
            if ($mysqli->query($updateProficiencyQuery) !== TRUE) {
                echo "Error updating proficiency level for $language: " . $mysqli->error;
            }
        }
        
        $updateQuery = "UPDATE languagepartners SET firstName='$newFirstName', lastName='$newLastName', bio='$newBio', password='$newPassword', phone='$newPhone', city='$newCity', gender='$newGender', age='$newAge', sessionPricePerHour='$newSessionPricePerHour' WHERE email='$email'";
        if ($mysqli->query($updateQuery) === TRUE) {
            header("Location: NativeProfilePage.php");
            exit;
        } else {
            echo "Error updating profile: " . $mysqli->error;
        }

        $mysqli->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Personal Profile Editing</title>
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="NativeProfileEditPage.css">
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
                        <a href="NativeProfilePage.php"> Profile</a>
                        </li>
                        <li>
                                <a href="Homepage.php"> Sign out</a>
                            
                        </li>
                    </ul>
                </div>
            </div>
        </header>
  
    <div id="profile-container">
           <div id="user-image">
            <img src="images/<?php echo $photo; ?>" alt="User Profile Image">
        </div>
      </div>
   
 <div id="info-container">
 <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
        <div class="info-input">
            <label>Upload New Photo:</label>
            <input type="file" name="profile_photo" accept="image/*">
        </div>
          
<div class="info-input">
    <label for="bio">Bio:</label><br>
    <textarea id="bio" name="bio"><?php echo $bio; ?></textarea>
    <?php if (!empty($errors['bio'])): ?>
        <p style="color: red;"><?php echo $errors['bio']; ?></p>
    <?php endif; ?>
</div>

<div class="info-input">
    <label for="first-name">First Name:</label>
    <input type="text" id="first-name" name="first_name" value="<?php echo $firstName; ?>">
    <?php if (!empty($errors['first_name'])): ?>
        <p style="color: red;"><?php echo $errors['first_name']; ?></p>
    <?php endif; ?>
</div>

<div class="info-input">
    <label for="last-name">Last Name:</label>
    <input type="text" id="last-name" name="last_name" value="<?php echo $lastName; ?>">
    <?php if (!empty($errors['last_name'])): ?>
        <p style="color: red;"><?php echo $errors['last_name']; ?></p>
    <?php endif; ?>
</div>


<div class="info-input">
    <label for="age">Age:</label>
    <input type="number" id="age" name="age" value="<?php echo $age; ?>">
    <?php if (!empty($errors['age'])): ?>
        <p style="color: red;"><?php echo $errors['age']; ?></p>
    <?php endif; ?>
</div>


<div class="info-input">
    <label><b>Gender:</b></label><br>
    <span><?php echo $gender; ?></span>
</div>


<div class="info-input">
    <label for="new_email">Email:</label>
    <input type="email" id="new_email" name="new_email" value="<?php echo $email; ?>">
    <?php if (!empty($errors['email'])): ?>
        <p style="color: red;"><?php echo $errors['email']; ?></p>
    <?php endif; ?>
</div>


<div class="info-input">
    <label for="password">Password:</label>
    <input type="text" id="password" name="password" value="<?php echo $password; ?>">
    <?php if (!empty($errors['password'])): ?>
        <p style="color: red;"><?php echo $errors['password']; ?></p>
    <?php endif; ?>
</div>


<div class="info-input">
    <label for="phone">Phone:</label>
    <input type="tel" id="phone" name="phone" value="<?php echo $phone; ?>">
    <?php if (!empty($errors['phone'])): ?>
        <p style="color: red;"><?php echo $errors['phone']; ?></p>
    <?php endif; ?>
</div>


<div class="info-input">
    <label for="city">City:</label>
    <input type="text" id="city" name="city" value="<?php echo $city; ?>">
    <?php if (!empty($errors['city'])): ?>
        <p style="color: red;"><?php echo $errors['city']; ?></p>
    <?php endif; ?>
</div>


<div class="info-input">
    <label for="session_price">Session Price Per Hour ($):</label>
    <input type="number" id="session_price" name="session_price" value="<?php echo $sessionPricePerHour; ?>">
    <?php if (!empty($errors['session_price'])): ?>
        <p style="color: red;"><?php echo $errors['session_price']; ?></p>
    <?php endif; ?>
</div>


<div class="info-input">
    <label><b>Language Proficiency Levels:</b></label><br>
    <?php foreach ($languages_data as $index => $language_data): ?>
        <div class="proficiency-level">
            <label for="proficiency_<?php echo $index; ?>"><?php echo $language_data['language']; ?>:</label>
            <select id="proficiency_<?php echo $index; ?>" name="proficiency[]">
                <option value="Advanced" <?php if ($language_data['proficiency'] === 'Advanced') echo 'selected'; ?>>Advanced</option>
                <option value="Expert" <?php if ($language_data['proficiency'] === 'Expert') echo 'selected'; ?>>Expert</option>
            </select>
        </div>
    <?php endforeach; ?>
</div>

<div style="display: inline-block; margin-top: 10px;">
    <input type="submit" value="Save" style="padding: 8px 15px; background-color: #e7e7f0; color: rgb(22, 46, 86); border: none; cursor: pointer; border-radius: 3px; text-align: center; text-decoration: none; display: inline-block; font-size: 14px;  margin-left: 180px;"
    onmouseover="this.style.backgroundColor='#5bacdf'; this.style.color='white';"
    onmouseout="this.style.backgroundColor='#e7e7f0'; this.style.color='rgb(22, 46, 86)';">
</div>

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
