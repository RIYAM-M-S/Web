<!--edit.php-->
<?php
// Database connection
$connection = mysqli_connect('localhost', 'root', '', 'lingumatesdb');

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $id = $_POST['requestID']; // Assuming 'requestID' is the primary key column name

    // Check if request status is 'waiting'
    $status_query = "SELECT status FROM requests WHERE requestID = ?";
    $stmt = mysqli_prepare($connection, $status_query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $status);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($status == 'waiting') {
        $delete_query = "DELETE FROM requests WHERE requestID = ?";
        $stmt = mysqli_prepare($connection, $delete_query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Your request has been successfully deleted.'); window.location.href = 'viewPartner.php';</script>";
            exit();
        } else {
            echo "Error deleting request: " . mysqli_error($connection);
        }
    } else {
        // Display alert box
        echo "<script>alert('You cannot delete your request because it is accepted.');</script>";
        // Redirect back to viewPartner.php
        echo "<script>window.location.href = 'viewPartner.php';</script>";
        exit();
    }
}

// Retrieve request ID from URL parameter
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Retrieve request details from the database
    $query = "SELECT * FROM requests WHERE requestID = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $language = $row['language'];
        $proficiency = $row['proficiencyLevel'];
        $schedule = $row['preferredSchedule'];
        $time=$row['time'];
        $duration = $row['sessionDuration'];

        // Check if status is not 'accepted', prevent editing
        if ($row['status'] != 'accepted') {
            echo "<script>alert('You cannot edit a request until it is accepted.'); window.location.href = 'viewPartner.php';</script>";
            exit();
        }
    } else {
        echo "Request not found.";
        exit();
    }
} else {
    echo "Request ID not provided.";
    exit();
}

// Update request if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update'])) {
        $language = $_POST['language'];
        $proficiency = $_POST['proficiency'];
        $schedule = $_POST['schedule'];
        $time=$_POST['time'];
        $duration = $_POST['duration'];

        // Update request in the database
        $query = "UPDATE requests SET language=?, proficiencyLevel=?, preferredSchedule=?, time=?,sessionDuration=? WHERE requestID = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "sssii", $language, $proficiency,$time, $schedule, $duration, $id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Your request has been successfully edited.'); window.location.href = 'viewPartner.php';</script>";
        } else {
            echo "Error updating request: " . mysqli_error($connection);
        }
    }
}

mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="search.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="footer.css" />
    <title>Edit Request</title>
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
                <style>
                  .round-image {
                      width: 60px;
                      height: 60px;
                      border-radius: 50%;
                      border: 2px solid #333;
                  }
              </style>

                  <a href="learnerProfile.html">
                    <img src="user.png" alt="User" class="round-image">
                  </a>
              </li>
              <li><a href="Homepage.php">Sign out</a></li>
          </ul>
      </div>
  </div>
</header>

<h2 class="label">Edit Request</h2>

<form method="post">
<div class="form-box">
  <div class="div">
    <div class="div-2">
    <label class="label" for="language-select">Language:</label>
    <select class="select" id="language-select" name="language">
        <option value="english" <?php if ($language == "english") echo "selected"; ?>>English</option>
        <option value="spanish" <?php if ($language == "spanish") echo "selected"; ?>>Spanish</option>
        <option value="french" <?php if ($language == "french") echo "selected"; ?>>French</option>
        <option value="german" <?php if ($language == "german") echo "selected"; ?>>German</option>
        <option value="italian" <?php if ($language == "italian") echo "selected"; ?>>Italian</option>
    </select>
    </div>
    <div class="div-3">
    <label class="label" for="proficiency-select">Proficiency:</label>
    <select class="select" id="proficiency-select" name="proficiency">
        <option value="beginner" <?php if ($proficiency == "beginner") echo "selected"; ?>>Beginner</option>
        <option value="intermediate" <?php if ($proficiency == "intermediate") echo "selected"; ?>>Intermediate</option>
        <option value="advanced" <?php if ($proficiency == "advanced") echo "selected"; ?>>Advanced</option>
    </select>
    </div>
    <div class="div-4">
    <label for="schedule-select" class="label">Preferred schedule:</label>
    <input type="date" id="schedule-select" name="schedule" class="select" value="<?php echo date('Y-m-d', strtotime($schedule)); ?>">
</div>

<div class="div-7">
    <label for="time-select" class="label">Time schedule:</label>
    <input type="time" id="time-select" name="time" class="select" value="<?php echo date('H:i', strtotime($time)); ?>">
</div>

    <div class="div-5">
    <label class="label" for="duration-select">Duration:</label>
    <select class="select" id="duration-select" name="duration">
        <option value="30" <?php if ($duration == "30") echo "selected"; ?>>30 minutes</option>
        <option value="45" <?php if ($duration == "45") echo "selected"; ?>>45 minutes</option>
        <option value="60" <?php if ($duration == "60") echo "selected"; ?>>60 minutes</option>
        <option value="90" <?php if ($duration == "90") echo "selected"; ?>>90 minutes</option>
    </select>
    </div>
    <button  class="button" type="submit" name="update">Update Request</button>
    
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
