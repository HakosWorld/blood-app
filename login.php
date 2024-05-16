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

// Retrieve the user credentials from the form
$email = $_POST['email'];
$password = $_POST['password'];

// Prepare the SQL statement to select the user from the table
$sql = "SELECT * FROM `user` WHERE `email` = '$email' AND `password` = '$password'";
$result = $conn->query($sql);

// Check if the query returned a matching user
if ($result->num_rows > 0) {
    // User credentials are correct
    $user = $result->fetch_assoc(); // Fetch the user data
    
    session_start(); // Start the session
    
    $_SESSION['user_id'] = $user['ID']; // Store the user's ID in the session
    $_SESSION['email'] = $user['email']; // Store the user's email in the session
    
    header("Location: user_panel.php"); // Redirect to the user panel
    exit();
} else {
    // User credentials are incorrect
    echo "Invalid email or password";
}

// Close the database connection
$conn->close();
?>
