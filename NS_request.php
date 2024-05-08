<?php
session_start();

define("DBHOST", "localhost");
define("DBUSER", "root");
define("DBPWD", "");
define("DBNAME", "lingumatesdb");

$conn = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];


$partner_photo = '';
$partner_name = '';
$partner_description = '';
$partner_proficiency = '';
if (!empty($email)) {
    $query = "SELECT photo, firstName, lastName, bio, partnerID FROM languagepartners WHERE email = ?";
    $stmt_partner = mysqli_prepare($conn, $query);
    if ($stmt_partner) {
        mysqli_stmt_bind_param($stmt_partner, "s", $email);
        mysqli_stmt_execute($stmt_partner);
        $result_partner = mysqli_stmt_get_result($stmt_partner);
        if ($result_partner && mysqli_num_rows($result_partner) > 0) {
            $row_partner = mysqli_fetch_assoc($result_partner);
            $partner_photo = $row_partner['photo'];
            $partner_name = $row_partner['firstName'] . ' ' . $row_partner['lastName'];
            $partner_description = $row_partner['bio'];
            $partnerID = $row_partner['partnerID'];
            $bio = $partner_description; // Assigning the bio to $bio variable
        }
        
        mysqli_stmt_close($stmt_partner);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width,initial-scale=1.0">
      <script src="https://kit.fontawesome.com/9eeac525af.js" crossorigin="anonymous"></script>
      <link rel="stylesheet" href="footer.css" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-GLhlTQ8i1IIVoaFaLcl5wjKOBn0Kk/RUdFEae83a6ho1NFwnfA5qBLF85fwApQ" crossorigin="anonymous">      
      <title>Requests</title>
      <link rel="stylesheet" href="NS_request.css">
      <?php
        $conect = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);

        if (!$conect) {
            die("Connection failed: " . mysqli_connect_error());
        }
        if (isset($_GET["new_status"])) {
          $requestID = $_GET["request_id"];
          $changedStatus = $_GET["new_status"];
      
          $query = "UPDATE requests SET status = '$changedStatus' WHERE requestID = $requestID;";
          mysqli_query($conect, $query);
        }
      
      ?>
      <script>
      function changeStatus(event) {
        if (event.target.classList.contains("change")) {
          var row = event.target.closest("tr");
          var currentStatus = row.querySelector(".sta");
          var editcell = currentStatus.closest("td");

        
          var form = document.createElement("form");
          form.method = "get";
          form.action = "NS_request.php";
          form.id = "statusform";

        
          var requestIdInput = document.createElement("input");
          requestIdInput.type = "hidden";
          requestIdInput.name = "request_id";
          requestIdInput.value = row.id;
          form.appendChild(requestIdInput);

        
          var statusSelect = document.createElement("select");
          statusSelect.id = "new";
          statusSelect.name = "new_status";

          var options = ["accepted", "cancelled", "waiting"];
          options.forEach(function(option) {
            var optionElement = document.createElement("option");
            optionElement.value = option;
            optionElement.textContent = option;
            optionElement.setAttribute("onselect", "submitForm2()");
            statusSelect.appendChild(optionElement);
          });

          form.appendChild(statusSelect);
        
          currentStatus.innerHTML = "";
          currentStatus.appendChild(form);
          var existingSaveButton = editcell.querySelector(".save");

          if (existingSaveButton) {
            return; 
          }
          
          var saveButton = document.createElement("button");
          saveButton.innerHTML = "save";
          saveButton.className = "save-btn save";
          saveButton.setAttribute("onclick", "submitForm2()");
          editcell.appendChild(saveButton);
        }
      }

      function submitForm2() {
        var ans = window.prompt("Do you want to save changes? y/n");
        if (ans && ans.toLowerCase() === "y") {
          document.getElementById("statusform").submit();
        }
      }
    </script>


  <script>
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
            <a href="NS_homepage.php">
              <img src="logo.png" alt="Logo">
            </a>
          </div>
          
            <div class="links">
              <ul>
                <li>
                <a href="NativeProfilePage.php"> Profile</a>
                  </a>
                </li>
                <li><a href="LinguMates/LinguMates/signOut.php">Sign out</a></li>
              </ul>
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
          <option value="cancelled" <?php echo isset($_GET["filter"]) && $_GET["filter"] == "cancelled" ? "selected" : ""; ?>>cancelled</option>
          <option value="waiting" <?php echo isset($_GET["filter"]) && $_GET["filter"] == "waiting" ? "selected" : ""; ?>>waiting</option>
        </select>
      </p>
    </form>
    <div class="requestsboard">
    <?php

$conn = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$filter = isset($_GET["filter"]) ? $_GET["filter"] : "All";





echo "<table>";
echo "<tr id='0'>";
echo "    <th></th>";
echo "    <th>Language</th>";
echo "    <th>Proficiency</th>";
echo "    <th>Schedule</th>";
echo "    <th>Duration</th>";
echo "    <th>Status</th>";
echo "    <th></th>";
echo "</tr>";


$sql = "SELECT language, proficiencyLevel, preferredSchedule, sessionDuration,requestID, status
        FROM requests
        WHERE partnerID = $partnerID ";

if ($filter != "All") {
    if ($filter == "waiting") {
      $sql .= " AND (status = 'waiting' AND DATEDIFF(NOW(), created_at) <= 30)";
    } else {
        $sql .= " AND status = '$filter'";
    }
} else {
  $sql .= " AND ((status = 'waiting' AND DATEDIFF(NOW(), created_at) <= 30) OR status != 'waiting')";
}

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error executing the query: " . mysqli_error($conn));
}


$rowIndex = 1;
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr id='" . $row['requestID'] . "'>";
    echo "    <td><img src='requestIcon.png' alt='request Icon' class='requestIcon'></td>";
    echo "    <td>" . $row['language'] . "</td>";
    echo "    <td>" . $row['proficiencyLevel'] . "</td>";
    echo "    <td>" . $row['preferredSchedule'] . "</td>";
    echo "    <td>" . $row['sessionDuration'] . " </td>";
    echo "    <td><label class='sta'>" . $row['status'] . "</label></td>";
    echo "    <td ><div onclick='changeStatus()' class='change save-btn'>edit</div></td>";

    echo "</tr>";
    $rowIndex++;
}

echo "</table>";
$sendsql = "SELECT * FROM `requests` WHERE STATUS = 'accepted'";
$allaccepted = mysqli_query($conn, $sendsql);

if (!$allaccepted) {
    die("Error executing the query: " . mysqli_error($conn));
}

while ($arr = mysqli_fetch_assoc($allaccepted)) {
    $insertsql = "INSERT INTO `sessions` (`learnerID`, `partnerID`,`scheduledTime`, `duration`, `status`)
                  VALUES (".$arr['learnerID'].", ".$arr['partnerID'].", '".$arr['preferredSchedule']."', ".$arr['sessionDuration'].", '".$arr['status']."')";
    
    if (!mysqli_query($conn, $insertsql)) {
        die("Error inserting into sessions table: " . mysqli_error($conn));
    }
}


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
  <p>&copy; LinguMates, 2024;</p>
</div>

</body>
</html>
