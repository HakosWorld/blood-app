<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "database";

$connection = new mysqli($servername, $username, $password, $dbname);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
} 

session_start(); // Start the session
if (!isset($_SESSION['admin_id'])) {
  // Redirect to the login page if the admin is not logged in
  header("Location: admin_login.php");
  exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($_POST as $key => $value) {
    if (strpos($key, 'approval_status_') !== false) {
      $userId = str_replace('approval_status_', '', $key);
      $newStatus = $value;

      // Update the approval status in the database
      $updateQuery = "UPDATE user SET approval_status = '$newStatus' WHERE ID = '$userId'";
      mysqli_query($connection, $updateQuery);
    }
  }
}

// Retrieve the updated data from the database
$query = "SELECT * FROM user";
$result = mysqli_query($connection, $query);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Approve users</title>
  
  <link rel="stylesheet" type="text/css" href="approve.css">
</head>
<body>
  <h1>Admin Panel</h1>
  <div class="container">
    <h2>User Data</h2>
    <form method="POST" action="">
      <table>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>CPR</th>
          <th>CPR Attachment</th>
          <th>Email</th>
          <th>Blood Type</th>
          <th>Gender</th>
          <th>Approval Status</th>
          <th>Status</th>
        </tr>
        <?php foreach ($users as $user): ?>
          <tr>
            <td><?php echo $user['ID']; ?></td>
            <td><?php echo $user['name']; ?></td>
            <td><?php echo $user['cpr']; ?></td>
            <td><?php echo $user['cpr_attachment']; ?></td>
            <td><?php echo $user['email']; ?></td>
            <td><?php echo $user['blood_type']; ?></td>
            <td><?php echo $user['gender']; ?></td>
            <td><?php echo $user['approval_status']; ?></td>
            <td>
              <select name="approval_status_<?php echo $user['ID']; ?>">
                <option value="pending" <?php if ($user['approval_status'] === 'pending') echo 'selected'; ?>>Pending</option>
                <option value="rejected" <?php if ($user['approval_status'] === 'rejected') echo 'selected'; ?>>Rejected</option>
                <option value="approved" <?php if ($user['approval_status'] === 'approved') echo 'selected'; ?>>Approved</option>
              </select>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
      <button type="submit">Update Approval Status</button>
    </form>
  </div>
</body>
</html>