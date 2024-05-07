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

$email = '';
$password = '';
$notCorrect = false;
$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sql_learners = "SELECT * FROM learners WHERE email = ?";
    $stmt_learners = $mysqli->prepare($sql_learners);
    $stmt_learners->bind_param("s", $_POST["email"]);
    $stmt_learners->execute();
    $result_learners = $stmt_learners->get_result();

    $sql_languagepartners = "SELECT * FROM languagepartners WHERE email = ?";
    $stmt_languagepartners = $mysqli->prepare($sql_languagepartners);
    $stmt_languagepartners->bind_param("s", $_POST["email"]);
    $stmt_languagepartners->execute();
    $result_languagepartners = $stmt_languagepartners->get_result();

    $user_learners = $result_learners->fetch_assoc();
    $user_languagepartners = $result_languagepartners->fetch_assoc();
    $errors = [];

    $email = trim($_POST["email"]);
    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    // Check if email exists in either table
    if ($user_learners || $user_languagepartners) {
        // Email exists in either table
        $user = $user_learners ? $user_learners : $user_languagepartners;

        $password = $_POST["password"];
        if (empty($password)) {
            $errors['password'] = "Password is required";
        }
        
        if ($password === $user["password"]) {
            $_SESSION["email"] = $_POST["email"];

            if ($user_learners) {
                header("Location: LearnerHP.php");
                exit;
            } else {
                header("Location: NS_homepage.php");
                exit;
            }
        } elseif(!empty($password)&& !empty($email)) {
            $notCorrect = true;
        }
    } elseif((!empty($email))&& (!$user_learners || !$user_languagepartners) ){
        $is_invalid = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="stylesheet" href="LHP.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="footer.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="Homepage.css?v=<?php echo time(); ?>">
    <style>
      .error-message{
        color: rgb(245, 25, 25);
        font-size: small;
        text-align: center;
        top:1px;
        position: relative;
      }

    </style>

    <script src="https://kit.fontawesome.com/9eeac525af.js" crossorigin="anonymous"></script>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="Homepage.php">
                    <img src="logo.png" alt="Logo">
                </a>
            </div>
        </div>
    </header>

    <header class="hero-section">
      <div class="Sign-in">
        <h1 class="card-timing">Sign in</h1>
        <?php if ($notCorrect): ?>
                <div class='error-message' style='text-align:center;'>
                    <p>Incorrect email or password.</p>
                </div>
            <?php elseif ($is_invalid): ?>
                <div class='error-message' style='text-align:center;'>
                    <p>Email not found. Please sign up.</p>
                </div>
            <?php endif; ?>
        <p class="Sign-in-description">
            <form action="#" class="sign-in-form" method="post">
                <div class="input-field">
                    <i class="fa-solid fa-envelope"></i>
                    <input id="email" type="text" placeholder="Email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <span class="error-message" style='width: 200px;'><?php echo isset($errors['email']) ? $errors['email'] : ''; ?></span>
                </div>

                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input id="password" type="password" placeholder="Password" name="password" />
                    <span class="error-message" style='width: 200px;'><?php echo isset($errors['password']) ? $errors['password'] : ''; ?></span>
                </div>

                <input type="submit" value="Sign in" class="hero-btn" />
                <p><a href="#sign">New here? Sign up now!</a></p>
            </form>


      </div>

      <h1 class="hero-title">Welcome to our language learning platform!</h1>
      <p class="hero-description">Are you ready to embark on an exciting journey to find the perfect tutor and unlock your full potential? Let's begin this amazing experience together and discover the best match that will help you reach your goals!</p>
    </header>

    <div class="time-load-section">
        <div class="container">
        <h1 class="section-title" id="sign">New here?</h1>
<p class="section-description">Sign up now!</p>

            <div class="row">
                <div class="card">
                    <h1 class="card-timing">Native Speaker</h1>
                    <p class="card-description">Passionate about your language? Tutor and guide learners to fluency. Sign up now!</p>
                    <button id="NS" class="hero-btn" onclick="window.location.href='SignUpNat.php'">Sign up</button>
                </div>

                <div class="card">
                    <h1 class="card-timing">Language Learner</h1>
                    <p class="card-description">Turn your language passion into a skill. Sign up for a learning adventure!</p>
                    <button onclick="window.location.href='SignUpLangLear.php'" class="hero-btn">Sign up</button>
                </div>
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
        <p>&copy; LinguMates, 2024;</p>
    </div>
</body>
</html>
