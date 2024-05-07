<?php
session_start();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <script src="https://kit.fontawesome.com/9eeac525af.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="footer.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-GLhlTQ8i1IIVoaFaLcl5wjKOBn0Kk/RUdFEae83a6ho1NFwnfA5qBLF85fwApQ" crossorigin="anonymous">      
    <title>Requests</title>
    <link rel="stylesheet" href="NS_request.css">
    <script src="/Users/shooqalsu/Desktop/web/Homework2 Material/js/jquery-1.9.1.min.js"></script>
    <?php
      define("DBHOST", "localhost");
      define("DBUSER", "root");
      define("DBPWD", "");
      define("DBNAME", "lingumatesdb");

      $con = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);
      if (mysqli_connect_error($con)) {
          die("Failed to connect to the database: " . mysqli_connect_error());
      }

      if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["request_id"]) && isset($_GET["status"])) {
          $requestID = $_GET["request_id"];
          $changedStatus = $_GET["status"];

          $query = "UPDATE requests SET status = ? WHERE requestID = ?";
          $stmt = mysqli_prepare($con, $query);
          mysqli_stmt_bind_param($stmt, "si", $changedStatus, $requestID);
          mysqli_stmt_execute($stmt);

          echo "<script>
                  var confirmation = window.confirm('Do you want to save changes?');
                  if (confirmation) {
                      // Update the status in the table row
                      var row = document.getElementById('$requestID');
                      row.querySelector('.sta').textContent = '$changedStatus';
                      // Remove the form elements
                      row.querySelector('.savestatus').remove();
                  }
                </script>";
      }

      mysqli_stmt_close($stmt);
      mysqli_close($con);
    ?>

    <script>
        function changeStatus(event) {
            if (event.target.classList.contains("sta")) {
                var row = event.target.closest("tr");
                var currentStatus = event.target;
                currentStatus.innerHTML = '<form method="get" action="NS_request_accepted.php">' +
                    '<input type="hidden" name="request_id" value="' + row.id + '">' +
                    '<select id="change" name="status">' +
                    '<option value="accepted">accepted</option>' +
                    '<option value="cancelled">cancelled</option>' +
                    '<option value="waiting">waiting</option>' +
                    '</select>' +
                    '<td><input type="submit" class="save-btn" value="Save"></td>' +
                    '</form>';
            }
        }
        function submitForm() {
            document.getElementById("filterForm").submit();
        }

        document.addEventListener("click", changeStatus);
    </script>

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
                  <a href="NS_homepage.html">
                    <img src="logo.png" alt="Logo">
                  </a>
                </div>
                

                <div class="links">
                  <ul>
                    <li>
                      <a href="NativeProfilePage.html">
                        <img src="user.png" alt="User" class="round-image">
                      </a>
                    </li>
                    <li><a href="Homepage.html">Sign out</a></li>
                  </ul>
                </div>
                

            <div class="overlay"></div>

            <div class="hamburger-menu">
              <div class="bar"></div>
            </div>
          </div>
        </header>

        <!-- requests UI -->
        <div class="container">
            <h1>Requests</h1>
            <form id="filterForm" method="GET" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
              <p id="All">
                  <select name="filter" id="list" onchange="submitForm()">
                      <option value="All" <?php echo isset($_GET["filter"]) && $_GET["filter"] == "All" ? "selected" : ""; ?>>All</option>
                      <option value="accepted" <?php echo isset($_GET["filter"]) && $_GET["filter"] == "accepted" ? "selected" : ""; ?>>accepted</option>
                      <option value="declined" <?php echo isset($_GET["filter"]) && $_GET["filter"] == "declined" ? "selected" : ""; ?>>declined</option>
                      <option value="waiting" <?php echo isset($_GET["filter"]) && $_GET["filter"] == "waiting" ? "selected" : ""; ?>>waiting</option>
                  </select>
              </p>
            </form>
            <div class="requestsboard">
            <?php
              // Create connection
              $conn = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);
              // Check connection
              if (!$conn) {
                  die("Connection failed: " . mysqli_connect_error());
              }
              
              // Get the selected filter from the form
              $filter = isset($_GET["filter"]) ? $_GET["filter"] : "All";
              
              // Get the partnerID from the session global array
              $partnerID = $_SESSION['partnerID'];
              
              // SQL query to retrieve request information based on the selected filter and partnerID
              $sql = "SELECT language, proficiencyLevel, preferredSchedule, sessionDuration, status
                      FROM requests
                      WHERE partnerID = ?";
              
              if ($filter != "All") {
                  if ($filter == "waiting") {
                      $sql .= " AND (status = 'waiting' AND DATEDIFF(NOW(), created_at) <= 30)";
                  } else {
                      $sql .= " AND status = ?";
                  }
              } else {
                  $sql .= " AND ((status = 'waiting' AND DATEDIFF(NOW(), created_at) <= 30) OR status != 'waiting')";
              }
              
              $stmt = mysqli_prepare($conn, $sql);
              if ($filter != "All" && $filter != "waiting") {
                  mysqli_stmt_bind_param($stmt, "ss", $partnerID, $filter);
              } elseif ($filter != "All" && $filter == "waiting") {
                  mysqli_stmt_bind_param($stmt, "s", $partnerID);
              } else {
                  mysqli_stmt_bind_param($stmt, "s", $partnerID);
              }
              
              mysqli_stmt_execute($stmt);
              $result = mysqli_stmt_get_result($stmt);
              
              // Display request information in HTML table format
              echo "<table>";
              echo "<tr id='0'>";
              echo "    <th></th>";
              echo "    <th>Language</th>";
              echo "    <th>Proficiency</th>";
              echo "    <th>Schedule</th>";
              echo "    <th>Duration</th>";
              echo "    <th>Status</th>";
              echo "</tr>";
              
              $rowIndex = 1;
              while ($row = mysqli_fetch_assoc($result)) {
                  echo "<tr id='" . $rowIndex . "'>";
                  echo "    <td><img src='requestIcon.png' alt='request Icon' class='requestIcon'></td>";
                  echo "    <td>" . $row['language'] . "</td>";
                  echo "    <td>" . $row['proficiencyLevel'] . "</td>";
                  echo "    <td>" . $row['preferredSchedule'] . "</td>";
                  echo "    <td>" . $row['sessionDuration'] . " min</td>";
                  echo "    <td><label class='sta'>" . $row['status'] . "</label></td>";
                  echo "</tr>";
                  $rowIndex++;
              }
              
              echo "</table>";
              
              // Close the connection
              mysqli_stmt_close($stmt);
              mysqli_close($conn);
              
              
            ?>





               
          </div>
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
