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

session_start(); // Start the session
if (!isset($_SESSION['email'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["campaign_id"]) && isset($_POST["selected_date"]) && isset($_POST["selected_time"])) {
        // Get the campaign ID, user ID, date, and time from the form submission
        $campaignId = $_POST["campaign_id"];
        $userId = $_SESSION["user_id"];
        $selectedDate = $_POST["selected_date"];
        $selectedTime = $_POST["selected_time"];

        // Insert the registration data into the campaign_registrations table
        $query = "INSERT INTO campaign_registrations (campaign_id, user_id, date, time) VALUES ('$campaignId', '$userId', '$selectedDate', '$selectedTime')";
        if ($connection->query($query) === TRUE) {
            echo"<h1>";
            echo "تم التسدجيل بنجاح";
            echo"<br>";
            echo "Email : ".$_SESSION["email"];
            echo"<br> USER ID:".$userId;
            echo"<br>";
            echo "Time : ". $selectedTime;
            echo"<br>";
            echo "Date : ". $selectedDate;
            echo"<br>";
            echo"</h1>";
            
        } else {
            echo "Error: " . $query . "<br>" . $connection->error;
        }
    } else {
        echo "Incomplete form data.";
    }
}

$connection->close();
?>
