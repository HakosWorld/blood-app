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
// Check if the admin is logged in
session_start(); // Start the session
if (!isset($_SESSION['admin_id'])) {
  // Redirect to the login page if the admin is not logged in
  header("Location: admin_login.php");
  exit;
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Create campaign</title>
  <link rel="stylesheet" type="text/css" href="adminstyle.css">
</head>
<body>
  <h1>Admin Panel</h1>
  <div class="container">
  <h2>Create Campaign</h2>
  <form action="create_campaign.php" method="post">
    <label for="campaign_name">Campaign Name:</label>
    <input type="text" id="campaign_name" name="campaign_name" required><br><br>

    <label for="start_date">Start Date:</label>
    <input type="date" id="start_date" name="start_date" required><br><br>

    <label for="end_date">End Date:</label>
    <input type="date" id="end_date" name="end_date" required><br><br>

    <label for="max_men">Maximum Men:</label>
    <input type="number" id="max_men" name="max_men" required><br><br>

    <label for="max_women">Maximum Women:</label>
    <input type="number" id="max_women" name="max_women" required><br><br>

    <label for="event_start_time">Event Start Time:</label>
    <input type="time" id="event_start_time" name="event_start_time" required><br><br>

    <label for="event_end_time">Event End Time:</label>
    <input type="time" id="event_end_time" name="event_end_time" required><br><br>

    <label for="interval_limit">Registration limit count per half hour:</label>
    <input type="number" id="interval_limit" name="interval_limit" required><br><br>

  

    <label for="Status">Status:</label>
    <div class="Status-container">

        
      <input type="radio" id="active" name="Status" value="active" required>
      <label for="active">active</label>
      
      <input type="radio" id="inactive" name="Status" value="inactive" required>
      <label for="inactive ">inactive </label>
      
    </div>
    <br><br>

    <input type="submit" value="Create Campaign">
  </form>
</div>
</body>
</html>

<?php 
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["campaign_name"])) {
  // Process the campaign creation form

  // Get the campaign details from the form
  $campaignName = $_POST["campaign_name"];
  $startDate = $_POST["start_date"];
  $endDate = $_POST["end_date"];
  $maxMen = $_POST["max_men"];
  $maxWomen = $_POST["max_women"];
  $eventStartTime = $_POST["event_start_time"];
  $eventEndTime = $_POST["event_end_time"];
  $intervalLimit = $_POST["interval_limit"];
  $status = $_POST["Status"];
  $adminId = $_SESSION['admin_id']; // Get the admin's ID from the session


  // Prepare and bind the statement
$query = "INSERT INTO campaign (name, start_date, end_date, max_men, max_women, event_start_time, event_end_time, interval_limit, created_by, updated_by, created_at, updated_at, status)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?)";
$statement = $connection->prepare($query);
$statement->bind_param("sssiissiiss", $campaignName, $startDate, $endDate, $maxMen, $maxWomen, $eventStartTime, $eventEndTime, $intervalLimit, $adminId, $adminId, $status);

  if ($statement->execute()) {
    echo "Campaign created successfully.";
    exit;
  } else {
    echo "Failed to create the campaign. Please try again.";
  }

  $statement->close(); // Close the statement
}
?>
