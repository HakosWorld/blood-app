<?php
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

// Retrieve the admin credentials from the form
$email = $_POST['email'];
$password = $_POST['password'];

// Prepare the SQL statement to select the admin from the table
$sql = "SELECT * FROM `admin` WHERE `email` = '$email' AND `password` = '$password'";
$result = $conn->query($sql);

// Check if the query returned a matching admin
if ($result->num_rows > 0) {
    // Admin credentials are correct
    $admin = $result->fetch_assoc(); // Fetch the admin data
    session_start(); // Start the session
    $_SESSION['admin_id'] = $admin['id']; // Store the admin's ID in the session
    header("Location: admin_panel.php"); // Redirect to the admin panel
    exit();
} else {
    // Admin credentials are incorrect
    echo "<h1 style=\"color:red;\">Invalid email or password</h1>";
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <h1>Admin Login</h1>
</head>
<body>
<div class="container">

<form action="admin_login.php" method="post">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br><br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br><br>
    <input type="submit" value="Login">
</form>

</div>
</body>
</html>
