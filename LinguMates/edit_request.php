<!--edit_request.php-->
<?php
// Database connection
$connection = mysqli_connect('localhost', 'root', '', 'lingumatesdb');

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve all requests from the database
$query = "SELECT * FROM requests";
$result = mysqli_query($connection, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Requests</title>
    <link rel="stylesheet" href="footer.css?v=<?php echo time(); ?>" >
    <link rel="stylesheet" href="Langrequest.css?v=<?php echo time(); ?>">
    
    <style>
        .round-image {
            width: 60px;
            height: 60px;
            border-radius: 50%; /* This will make the image round */
            border: 2px solid #333; /* This will create a dark outline */
            
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
                        <li>
                        
                        <a href="learnerProfile.php"> Profile  </a>
                        </li>
                        <li><a href="Homepage.php">Sign out</a></li>
                    </ul>
                </div>
            </div>
          </header>

<div class="container">
<h2 style="text-align:center;position:relative ;top: -15px;">View Requests</h2>

<div class="requestsboard">
<table>
    <tr id="catigory">
        <th>Language</th>
        <th>Proficiency</th>
        <th>Schedule</th>
        <th>time</th>
        <th>Duration</th>
        <th>Action</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <tr>
    <td><?php echo $row['language']; ?></td>
    <td><?php echo $row['proficiencyLevel']; ?></td>
    <td><?php echo $row['preferredSchedule']; ?></td>
    <td><?php echo $row['time']; ?></td>
    <td><?php echo $row['sessionDuration']; ?></td>
    <td>
        <form method="get" action="edit.php" style="display: inline;">
            <input type="hidden" name="id" value="<?php echo $row['requestID']; ?>">
            <button type="submit" id="save-btn">Edit</button>
        </form>
        <form method="post" action="edit.php">
            <input type="hidden" name="requestID" value="<?php echo $row['requestID']; ?>">
            <button type="submit" name="delete" id="dlt-btn">Delete</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>

   
      
   </table>
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
      <div class="footerBottom">
      <p>&copy; LinguMates, 2024;  </p>
  </div>
      </footer>

</body>
</html>


