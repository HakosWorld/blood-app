<?php
// Assuming you have a database connection established
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "database";

$connection = new mysqli($servername, $username, $password, $dbname);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
} 
// Check if the user is logged in
session_start(); // Start the session
if (!isset($_SESSION['email'])) {
  // Redirect to the login page if the user is not logged in
  header("Location: login.php");
  exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Check if a campaign ID is selected
  if (isset($_POST["campaign_id"])) {
    // Get the selected campaign ID
    $campaignId = $_POST["campaign_id"];

    // Retrieve the start_date, end_date, event_start_time, and event_end_time from the campaign table
    $query = "SELECT start_date, end_date, event_start_time, event_end_time, interval_limit FROM campaign WHERE id = '$campaignId'";
    $result = mysqli_query($connection, $query);
    $campaign = mysqli_fetch_assoc($result);
    $startDate = $campaign['start_date'];
    $endDate = $campaign['end_date'];
    $eventStartTime = $campaign['event_start_time'];
    $eventEndTime = $campaign['event_end_time'];
    $intervalLimit = $campaign['interval_limit'];

    // Calculate the available dates within the campaign's start_date and end_date
    $startDateObj = new DateTime($startDate);
    $endDateObj = new DateTime($endDate);
    $interval = new DateInterval('P1D');
    $dateRange = new DatePeriod($startDateObj, $interval, $endDateObj->modify('+1 day'));

    $availableDates = [];
    foreach ($dateRange as $date) {
      $availableDates[] = $date->format('Y-m-d');
    }

    // Display the selected campaign button with a border
    echo "<style>.selected-campaign-button[value='$campaignId'] { border: 4px solid green; }</style>";
  } else {
    echo "No campaign selected.";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>User Panel</title>
  <link rel="stylesheet" type="text/css" href="panelstyle.css">
 
  <h1>User Panel</h1>
</head>
<body>
  <div class="container">
    <h2>Campaign Registration</h2>
    <?php 
      echo "User is: ".$_SESSION["email"];  
    ?>
    <h2>Active Campaigns</h2>
    <h2>________________________________________</h2>
    <br>
    <form id="campaign_form" action="user_panel.php" method="post">
      <?php
        // Fetch campaigns from the database and create a button for each campaign
        // Assuming you have a database connection object named $connection
        $query = "SELECT id, name FROM campaign";
        $result = mysqli_query($connection, $query);

        while ($row = mysqli_fetch_assoc($result)) {
          $buttonCampaignId = $row['id']; // Unique variable name for the campaign ID
          $campaignName = $row['name'];
          echo "<button type='submit' class='campaign-button selected-campaign-button' name='campaign_id' value='$buttonCampaignId' onclick='updateCampaignId($buttonCampaignId)'>$campaignName</button>";
        }
      ?>
    </form>
    <h2>________________________________________</h2>
    <?php if (isset($campaignId)) { ?>
      <br>
      <form id="registration_form" action="camp_register.php" method="post" onsubmit="return validateForm()">
        <input type="hidden" id="selected_campaign_id" name="campaign_id" value="<?php echo $campaignId; ?>">
        <div class="date-time-box">
          <div>
            <label>Select a date:</label>
            <select name="selected_date" id="selected_date">
              <option value="">Select a date</option>
              <?php foreach ($availableDates as $date) { ?>
                <option value="<?php echo $date; ?>"><?php echo $date; ?></option>
              <?php } ?>
            </select>
          </div>
          <div>
            <label>Select a time:</label>
            <select name="selected_time" id="selected_time">
              <option value="">Select a time</option>
              <?php
                $startTimeObj = new DateTime($eventStartTime);
                $endTimeObj = new DateTime($eventEndTime);
                $interval = new DateInterval('PT30M');
                $timeRange = new DatePeriod($startTimeObj, $interval, $endTimeObj);

                foreach ($timeRange as $time) {
                  $timeStr = $time->format('H:i');
                  $timeCountQuery = "SELECT COUNT(*) AS registrations FROM campaign_registrations WHERE campaign_id = '$campaignId' AND time = '$timeStr'";
                  $timeCountResult = mysqli_query($connection, $timeCountQuery);
                  $timeCountRow = mysqli_fetch_assoc($timeCountResult);
                  $timeCount = $timeCountRow['registrations'];

                  if ($timeCount < $intervalLimit) {
                    echo "<option value='$timeStr'>$timeStr</option>";
                  }
                }
              ?>
            </select>
           
          </div>
        
        </div>
        <h7>⚠️if no time = full<h7>
        <br>
        <input type="submit" value="Register">
      </form>
    <?php } ?>
  </div>

  <script>
    function updateCampaignId(campaignId) {
      document.getElementById("selected_campaign_id").value = campaignId;
    }

    function validateForm() {
      var selectedDate = document.getElementById("selected_date").value;
      var selectedTime = document.getElementById("selected_time").value;

      if (selectedDate === "") {
        alert("Please select a date.");
        return false;
      }

      if (selectedTime === "") {
        alert("Please select a time.");
        return false;
      }

      return true;
    }
  </script>
</body>
</html>
