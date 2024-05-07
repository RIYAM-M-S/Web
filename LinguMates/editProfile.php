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

$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

if (!empty($email)) {
    $query = "SELECT learnerID, firstName, lastName, email, location, city, photo, password FROM learners WHERE email = '$email'";
    $result = $mysqli->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $learnerID = $row['learnerID']; 
        $firstName = $row['firstName'];
        $lastName = $row['lastName'];
        $email = $row['email'];
        $location = $row['location'];
        $city = $row['city'];
        $photo = $row['photo'];
        $password = $row['password']; 
    } else {
        echo "Error: No results found for the given email.";
    }
} else {
    echo "Error: Session email is not set.";
}


function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}


function containsNumber($str) {
    return preg_match('/\d/', $str);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newFirstName = $_POST['firstName'];
    $newLastName = $_POST['lastName'];
    $newEmail = $_POST['email'];
    $newLocation = $_POST['location'];
    $newCity = $_POST['city'];
    $newPassword = $_POST['password'];
   
    $newPhoto = '';
    if ($_FILES['newPhoto']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['newPhoto']['tmp_name'];
        $fileName = $_FILES['newPhoto']['name'];
        $fileType = $_FILES['newPhoto']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newPhoto = uniqid() . '.' . $fileExtension;
        $uploadPath = "images/" . $newPhoto;
        move_uploaded_file($fileTmpPath, $uploadPath);
    }

    
    $errors = [];

  
    if (empty($newFirstName)) {
        $errors['firstName'] = "First name is required";
    } elseif (!preg_match("/^[a-zA-Z'-]+$/", $newFirstName)) {
        $errors['firstName'] = "Invalid first name format";
    }

   
    if (empty($newLastName)) {
        $errors['lastName'] = "Last name is required";
    } elseif (!preg_match("/^[a-zA-Z'-]+$/", $newLastName)) {
        $errors['lastName'] = "Invalid last name format";
    }


    if ($newEmail != $email) {
        if (empty($newEmail)) {
            $errors['email'] = "Email is required";
        } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email format";
        } else {
            $checkQuery = "SELECT email FROM learners WHERE email = '$newEmail' UNION SELECT email FROM languagepartners WHERE email = '$newEmail'";
            $checkResult = $mysqli->query($checkQuery);
            if ($checkResult && $checkResult->num_rows > 0) {
                $errors['email'] = "Email already exists";
            }
        }
    }


    if (empty($newPassword)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($newPassword) < 8) {
        $errors['password'] = "Password must be at least 8 characters long";
    }

 
    if (empty($newCity)) {
        $errors['city'] = "City is required";
    }


    if (empty($newLocation)) {
        $errors['location'] = "Location is required";
    }

 
    if (empty($errors)) {
        $updateQuery = "UPDATE learners SET firstName='$newFirstName', lastName='$newLastName', email='$newEmail', location='$newLocation', city='$newCity'";
        

        if (!empty($newPassword)) {
            $updateQuery .= ", password='$newPassword'";
        }
        
   
        if (!empty($newPhoto)) {
            $updateQuery .= ", photo='$newPhoto'";
        }

        $updateQuery .= " WHERE learnerID=$learnerID";

        if ($mysqli->query($updateQuery) === TRUE) {
            $_SESSION['email'] = $newEmail; 
            echo "<script>alert('Profile updated successfully!'); window.location.href = 'learnerProfile.php';</script>";
        } else {
            echo "Error updating profile: " . $mysqli->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Personal Profile</title>
    <link rel="stylesheet" href="footer.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-GLhlTQ8i1IIVoaFaLcl5wjKOBn0Kk/RUdFEae83a6ho1NFwnfA5qBLF85fwApQ" crossorigin="anonymous">
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

        #user-image img {
            border-radius: 50%;
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

        .input {
            max-width: 500px;
            width: 100%;
            background-color: #f9f9f9;
            margin: 5px 0; 
            height: 65px;
            border-radius: 10px;
            display: flex; 
            align-items: center; 
            padding: 0 20px; 
            position: relative;
        }

        .input strong {
            margin-right: 10px; 
            color: black; 
            font-size: 1.1rem; 
            width: 150px; 
        }

        .input input {
            flex: 1; 
            background: none;
            outline: none;
            border: none;
            font-weight: 200;
            font-size: 1.1rem; 
            color: #333;
        }

        .input input::placeholder {
            color: #aaa;
            font-weight: 400;
        }

        #save-btn {
            padding: 15px;
            background-color: #e7e7f0;
            color: rgb(22, 46, 86);
            border: none;
            width: 70px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 15px; 
            margin: 0 200px;                   
        }

        #save-btn:hover {
            background-color: #5bacdf;
            color: white;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-left: 10px;
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
                        <li><a href="signOut.php">Sign out</a></li>
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
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                <div class="input">
                    <strong>Upload New Photo:</strong>
                    <input type="file" name="newPhoto" accept="image/*">
                </div>
                <div class="input">
                    <strong>First Name:</strong>
                    <input type="text" name="firstName" value="<?php echo $firstName; ?>" />
                    <span class="error"><?php echo isset($errors['firstName']) ? $errors['firstName'] : ''; ?></span>
                </div>
                <div class="input">
                    <strong>Last Name:</strong>
                    <input type="text" name="lastName" value="<?php echo $lastName; ?>" />
                    <span class="error"><?php echo isset($errors['lastName']) ? $errors['lastName'] : ''; ?></span>
                </div>
                <div class="input">
                    <strong>Email:</strong>
                    <input type="text" name="email" value="<?php echo $email; ?>" />
                    <span class="error"><?php echo isset($errors['email']) ? $errors['email'] : ''; ?></span>
                </div>
                <div class="input">
                    <strong>Password:</strong>
                    <input type="password" name="password" value="<?php echo $password; ?>" />
                    <span class="error"><?php echo isset($errors['password']) ? $errors['password'] : ''; ?></span>
                </div>
                <div class="input">
                    <strong>Location:</strong>
                    <input type="text" name="location" value="<?php echo $location; ?>" />
                    <span class="error"><?php echo isset($errors['location']) ? $errors['location'] : ''; ?></span>
                </div>
                <div class="input">
                    <strong>City:</strong>
                    <input type="text" name="city" value="<?php echo $city; ?>" />
                    <span class="error"><?php echo isset($errors['city']) ? $errors['city'] : ''; ?></span>
                </div>
                <button type="submit" id="save-btn">Save</button>
            </form>
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
</body>
</html>