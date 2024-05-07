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
$age = '';
$gender = '';
$bio = '';
$phone = $price = $languages='';


$emailExist = false;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $firstName = $mysqli->real_escape_string($_POST["firstName"]);
  $lastName = $mysqli->real_escape_string($_POST["lastName"]);
  $email = $mysqli->real_escape_string($_POST["email"]);
  $password = ($_POST["password"]);
  $city = $mysqli->real_escape_string($_POST["city"]);
  $age = $mysqli->real_escape_string($_POST["age"]);
  $gender = isset($_POST['gender']) ? $mysqli->real_escape_string($_POST['gender']) : '';
  $phone =  $mysqli->real_escape_string($_POST["phone"]);
  $bio = isset($_POST['bio']) ? $_POST['bio'] : '';
  $price = isset($_POST["price"]) ? $_POST["price"] : '';
  $languages = isset($_POST['languages']) ? $_POST['languages'] : [];
  $proficiencies = [];

  $file_name = isset($_FILES['profile-photo']['name']) ? $_FILES['profile-photo']['name'] : 'user.png';
  $tempname = isset($_FILES['profile-photo']['tmp_name']) ? $_FILES['profile-photo']['tmp_name'] : '';
  $folder= 'images/'. $file_name;
  if (!empty($tempname) && move_uploaded_file($tempname, $folder)){
  } else {
      $file_name = 'user.png';
  }
  

  $errors = [];

foreach ($languages as $language) {
    $proficiency = isset($_POST[$language . 'Proficiency']) ? $_POST[$language . 'Proficiency'] : '';
    if (empty($proficiency)) {
        $proficiency = 'advanced';
    }
    $proficiencies[$language] = $proficiency;
}
    

  if (empty($price)) {
    $errors['price'] = "Price is required";
} elseif (!is_numeric($price) || $price < 0 || $price > 30) {
    $errors['price'] = "Price should be a positive number not more than 30";
}

  if (empty($_POST['languages'])) {
      $errors['languages'] = "At least one language must be selected";
  }

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

  $phone = trim($_POST["phone"]);
  if (empty($phone)) {
      $errors['phone'] = "Phone number is required";
  } elseif (!preg_match("/^\d{3}-\d{8}$/", $phone)) {
      $errors['phone'] = "Enter phone as XXX-XXXXXXXX.";
  }
  



  $city = trim($_POST["city"]);
  if (empty($city)) {
      $errors['city'] = "City is required";
  }

  $bio = trim($_POST["bio"]);
  if (empty($bio)) {
      $errors['bio'] = "Bio is required";
  }

  $age = trim($_POST["age"]);
  if (empty($age)) {
    $errors['age'] = "Age is required";}
  elseif (!is_numeric($age) || $age <= 0 || $age >100) {
      $errors['age'] = "Age should be less than or equal to 100";
  }

  if (isset($_POST["gender"])) {
    $gender = trim($_POST["gender"]);
} else {
    $errors['gender'] = "Gender is required";
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

  if(!$emailExist && empty($errors)) { 
    $stmt = $mysqli->prepare("INSERT INTO languagepartners (firstName, lastName, age, gender, email, password, phone, photo, city, bio, sessionPricePerHour) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssisssssssi", $firstName, $lastName, $age, $gender, $email, $password, $phone, $file_name, $city, $bio, $price);
        if ($stmt->execute()) {
            $partnerID = $mysqli->insert_id;

            $stmt = $mysqli->prepare("INSERT INTO partner_languages (partnerID, partner_email, language, proficiency) VALUES (?, ?, ?, ?)");
            if ($stmt) {
                foreach ($proficiencies as $language => $proficiency) {
                    $stmt->bind_param("isss", $partnerID, $email, $language, $proficiency);
                    $stmt->execute();
                }
            }

            $_SESSION["email"] = $_POST["email"];
            header("Location: NS_homepage.php");
            exit();
        } else {
            header("Location: SignUpNat.php");
            exit(); 
        }
    }
}



}
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
    <link rel="stylesheet" href="SignUpNat.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="footer.css?v=<?php echo time(); ?>">
    <title>Native speaker sign up</title>

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
              <i class="fas fa-user"></i>
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
              <i class="fa-solid fa-phone"></i>              
              <input type="text" placeholder="Phone(XXX-XXXXXXXX)" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
              <span class="input-suffix">*</span>
              <span class="error-message" ><?php echo isset($errors['phone']) ? $errors['phone'] : ''; ?></span>
            </div>
            <div class="bio-input">
    <textarea placeholder="Bio: Languages Spoken and Cultural Knowledge" name="bio"><?php echo htmlspecialchars($bio); ?></textarea>
   <span class="error-message-bio" ><?php echo isset($errors['bio']) ? $errors['bio'] : ''; ?></span>
   <span class="input-suffix-bio">*</span>

</div>
<div class="ckeck-box">
    <i class="fa-solid fa-language"></i>
    <label for="language">Select languages to teach.</label>

<div class="language-row">
    <input type="checkbox" id="English" name="languages[]" value="English" <?php echo isset($_POST['languages']) && in_array('English', $_POST['languages']) ? 'checked' : ''; ?>>
    <label for="English">English</label>
    <select id="EnglishProficiency" name="EnglishProficiency">
        <option value="" disabled <?php echo empty($_POST['languages']) || !in_array('English', $_POST['languages']) ? 'selected' : ''; ?>>Select Proficiency</option>
        <option value="advanced" <?php echo isset($_POST['englishProficiency']) && $_POST['englishProficiency'] === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
        <option value="Expert" <?php echo isset($_POST['englishProficiency']) && $_POST['englishProficiency'] === 'Expert' ? 'selected' : ''; ?>>Expert</option>
    </select>
</div>

<div class="language-row">
    <input type="checkbox" id="Spanish" name="languages[]" value="Spanish" <?php echo isset($_POST['languages']) && in_array('Spanish', $_POST['languages']) ? 'checked' : ''; ?>>
    <label for="Spanish">Spanish</label>
    <select id="SpanishProficiency" name="SpanishProficiency">
        <option value="" disabled <?php echo empty($_POST['languages']) || !in_array('Spanish', $_POST['languages']) ? 'selected' : ''; ?>>Select Proficiency</option>
        <option value="advanced" <?php echo isset($_POST['spanishProficiency']) && $_POST['spanishProficiency'] === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
        <option value="Expert" <?php echo isset($_POST['spanishProficiency']) && $_POST['spanishProficiency'] === 'Expert' ? 'selected' : ''; ?>>Expert</option>
    </select>
</div>

<div class="language-row">
    <input type="checkbox" id="French" name="languages[]" value="French" <?php echo isset($_POST['languages']) && in_array('French', $_POST['languages']) ? 'checked' : ''; ?>>
    <label for="French">French</label>
    <select id="FrenchProficiency" name="FrenchProficiency">
        <option value="" disabled <?php echo empty($_POST['languages']) || !in_array('French', $_POST['languages']) ? 'selected' : ''; ?>>Select Proficiency</option>
        <option value="advanced" <?php echo isset($_POST['frenchProficiency']) && $_POST['frenchProficiency'] === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
        <option value="Expert" <?php echo isset($_POST['frenchProficiency']) && $_POST['frenchProficiency'] === 'Expert' ? 'selected' : ''; ?>>Expert</option>
    </select>
</div>

<div class="language-row">
    <input type="checkbox" id="German" name="languages[]" value="German" <?php echo isset($_POST['languages']) && in_array('German', $_POST['languages']) ? 'checked' : ''; ?>>
    <label for="German">German</label>
    <select id="GermanProficiency" name="GermanProficiency">
        <option value="" disabled <?php echo empty($_POST['languages']) || !in_array('German', $_POST['languages']) ? 'selected' : ''; ?>>Select Proficiency</option>
        <option value="advanced" <?php echo isset($_POST['germanProficiency']) && $_POST['germanProficiency'] === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
        <option value="Expert" <?php echo isset($_POST['germanProficiency']) && $_POST['germanProficiency'] === 'Expert' ? 'selected' : ''; ?>>Expert</option>
    </select>
</div>

<div class="language-row">
    <input type="checkbox" id="Italian" name="languages[]" value="Italian" <?php echo isset($_POST['languages']) && in_array('Italian', $_POST['languages']) ? 'checked' : ''; ?>>
    <label for="Italian">Italian</label>
    <select id="ItalianProficiency" name="ItalianProficiency">
        <option value="" disabled <?php echo empty($_POST['languages']) || !in_array('Italian', $_POST['languages']) ? 'selected' : ''; ?>>Select Proficiency</option>
        <option value="advanced" <?php echo isset($_POST['italianProficiency']) && $_POST['italianProficiency'] === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
        <option value="Expert" <?php echo isset($_POST['italianProficiency']) && $_POST['italianProficiency'] === 'Expert' ? 'selected' : ''; ?>>Expert</option>
    </select>
</div>

<div class="language-row">
    <input type="checkbox" id="Arabic" name="languages[]" value="Arabic" <?php echo isset($_POST['languages']) && in_array('Arabic', $_POST['languages']) ? 'checked' : ''; ?>>
    <label for="Arabic">Arabic</label>
    <select id="ArabicProficiency" name="ArabicProficiency">
        <option value="" disabled <?php echo empty($_POST['languages']) || !in_array('Arabic', $_POST['languages']) ? 'selected' : ''; ?>>Select Proficiency</option>
        <option value="advanced" <?php echo isset($_POST['arabicProficiency']) && $_POST['arabicProficiency'] === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
        <option value="Expert" <?php echo isset($_POST['arabicProficiency']) && $_POST['arabicProficiency'] === 'Expert' ? 'selected' : ''; ?>>Expert</option>
    </select>
</div>

    <span class="input-suffix-check">*</span>
    <span class="error-message-check"><?php echo isset($errors['languages']) ? $errors['languages'] : ''; ?></span>
    <span class="message-check"> Note: If no proficiency level is selected, 'Advanced' will be chosen automatically.</span>
</div>



<div class="input-field">
    <i class="fa-solid fa-venus-mars"></i>
    <select id="Gender" name="gender" class="<?php echo isset($gender) && !empty($gender) ? 'selected' : ''; ?>">
        <option value="" disabled <?php echo empty($gender) ? 'selected' : ''; ?>>Select Gender</option>
        <option id="male" value="Male" <?php echo $gender === 'Male' ? 'selected' : ''; ?>>Male</option>
        <option value="Female" <?php echo $gender === 'Female' ? 'selected' : ''; ?>>Female</option>
    </select>
    <span class="input-suffix">*</span>
    <span class="error-message"><?php echo isset($errors['gender']) ? $errors['gender'] : ''; ?></span>
</div>

            <div class="input-field">
              <i class="fa-solid fa-city"></i>  
              <input id="city" type="text" placeholder="City" name="city" value="<?php echo htmlspecialchars($city); ?>">
              <span class="input-suffix">*</span>
              <span class="error-message"   ><?php echo isset($errors['city']) ? $errors['city'] : ''; ?></span>
            </div>

            <div class="input-field">
              <i class="fas fa-user"></i>
              <input type="text" placeholder="Age" name="age" value="<?php echo htmlspecialchars($age); ?>"/>
              <span class="input-suffix">*</span>
              <span class="error-message" ><?php echo isset($errors['age']) ? $errors['age'] : ''; ?></span>
            </div>

            <div class="input-field">
            <i class="fa-solid fa-money-bill"></i>
              <input id="price" type="text" placeholder="Price per hour" name="price" value="<?php echo htmlspecialchars($price); ?>">
              <span class="input-suffix">*</span>
              <span class="error-message" ><?php echo isset($errors['price']) ? $errors['price'] : ''; ?></span>
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
              Welcome back tutors! Unleash the language magic. Sign in now for shared brilliance
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function() {
    $('#Gender').css('color', '#aaa');
    $('#Gender').change(function() {
        var current = $('#Gender').val();
        if (current != '') {
            $('#Gender').css('color', '#333');
        } else {
            $('#Gender').css('color', '#aaa');
        }
    });
});
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('#Gender').change(function() {
            $(this).toggleClass('selected', $(this).val() !== '');
        });
    });

    $(document).ready(function() {
    $('input[name="languages[]"]').each(function() {
        var language = $(this).val();
        $('#' + language + 'Proficiency').hide();
    });

    $('input[name="languages[]"]').change(function() {
        var language = $(this).val();
        var proficiencyInput = $('#' + language + 'Proficiency');
        if ($(this).is(':checked')) {
            proficiencyInput.show();
        } else {
            proficiencyInput.hide();
        }
    });
});

$(document).ready(function() {
    $('select[name$="Proficiency"]').each(function() {
        var current = $(this).val();
        if (current !== '') {
            $(this).css('color', '#333');
        } else {
            $(this).css('color', '#aaa');
        }
        $(this).change(function() {
            var current = $(this).val();
            if (current !== '') {
                $(this).css('color', '#333');
            } else {
                $(this).css('color', '#aaa');
            }
        });
    });
});


</script>




  </body>
</html>
