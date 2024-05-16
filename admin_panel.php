<?php
session_start(); // Start the session

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php"); // Redirect to the login page if not logged in
    exit();
}

// Assuming the database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "database";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the admin ID from the session
$admin_id = $_SESSION['admin_id'];

// Prepare the SQL statement to select the admin from the table
$sql = "SELECT * FROM `admin` WHERE `id` = '$admin_id'";
$result = $conn->query($sql);

// Check if the query returned a matching admin
if ($result->num_rows > 0) {
    // Admin credentials are valid
    $admin = $result->fetch_assoc(); // Fetch the admin data
} else {
    // Admin not found
    header("Location: admin_login.php"); // Redirect to the login page
    exit();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <h1>Welcome, <?php echo $admin['email']; ?>!</h1>
</head>
<body>
   
<div class="container">
    <h1>Admin Actions</h1>
    <form action="approve.php" method="post">
        <input type="submit" value="Approve users">
    </form>
    <form action="create_campaign.php" method="post">
        <input type="submit" value="Create Campaign">
    </form>
    
</div>
</body>
</html>
