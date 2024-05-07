<!--submit_request.php-->
<?php 
// Database connection
$connection = mysqli_connect('localhost', 'root', '', 'lingumatesdb');

// Check if the connection is successful
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Escape user inputs to prevent SQL injection
$language = mysqli_real_escape_string($connection, $_POST['language']);
$proficiency = mysqli_real_escape_string($connection, $_POST['proficiency']);
$schedule = mysqli_real_escape_string($connection, $_POST['schedule']);
$time = mysqli_real_escape_string($connection, $_POST['time']);
$duration = mysqli_real_escape_string($connection, $_POST['duration']);

// Insert user input into database
$query = "INSERT INTO requests (language, proficiencyLevel, preferredSchedule,time, sessionDuration) VALUES ('$language', '$proficiency', '$schedule','$time', '$duration')";

// Execute the query and handle success/error messages
if (mysqli_query($connection, $query)) {
    echo "<script>alert('Your request has been successfully sent.'); window.location.href = 'viewPartner.php';</script>";
} else {
    echo "Error: " . $query . "<br>" . mysqli_error($connection);
}

// Close the database connection
mysqli_close($connection);
?>
