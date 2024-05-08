<!--Request.php-->
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Post request</title>
<link rel="stylesheet" href="search.css?v=<?php echo time(); ?>" />
<link rel="stylesheet" href="footer.css?v=<?php echo time(); ?>" />
<script>
function validateForm() {
    var language = document.getElementById("language-select").value;
    var proficiency = document.getElementById("proficiency-select").value;
    var schedule = document.getElementById("schedule-select").value;
    var time = document.getElementById("time-select").value;
    var duration = document.getElementById("duration-select").value;

    if (language == "" || proficiency == "" || schedule == ""|| time == ""|| duration == "") {
        alert("Please fill out all fields");
        return false;
    }
    return true;
}
</script>
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

<a href="learnerProfile.php"> Profile  </a>
              </li>
              <li><a href="Homepage.php">Sign out</a></li>
          </ul>
      </div>
  </div>
</header>
<form action="submit_request.php" method="post" onsubmit="return validateForm()">
<div class="form-box">
  <div class="div">
    <div class="div-2">
      <label for="language-select" class="label">
        Specify the language you want to learn:
      </label>
      <select class="select" id="language-select" name="language">
        <option value="">Select Language</option>
        <option value="english">English</option>
        <option value="spanish">Spanish</option>
        <option value="french">French</option>
        <option value="german">German</option>
        <option value="italian">Italian</option>
      </select>
    </div>
    <div class="div-3">
      <label for="proficiency-select" class="label">
        Current proficiency level:
      </label>
      <select class="select" id="proficiency-select" name="proficiency">
        <option value="">Select Proficiency</option>
        <option value="beginner">Beginner</option>
        <option value="intermediate">Intermediate</option>
        <option value="advanced">Advanced</option>
      </select>
    </div>
    <div class="div-4">
      <label for="schedule-select" class="label">Preferred schedule:</label>
      <input type="date" id="schedule-select" name="schedule" class="select">
    </div>
    <div class="div-7">
      <label for="time-select" class="label">time schedule:</label>
      <input type="time" id="time-select" name="time" class="select">
    </div>
    <div class="div-5">
      <label for="duration-select" class="label">Session duration:</label>
      <select class="select" id="duration-select" name="duration">
        <option value="">Select Duration</option>
        <option value="30">30 minutes</option>
        <option value="45">45 minutes</option>
        <option value="60">60 minutes</option>
        <option value="90">90 minutes</option>
      </select>
    </div>
    <button type="submit" class="button">Send Request</button>
  </div>
</div>
</form>


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
