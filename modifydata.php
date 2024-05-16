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

$query = "SELECT * FROM user";
$result = mysqli_query($connection, $query);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  foreach ($users as $user) {
    $userId = $user['ID'];

    if (isset($_POST["delete_" . $userId])) {
      // Delete the user if the corresponding delete button is clicked
      $deleteQuery = "DELETE FROM user WHERE ID = $userId";
      mysqli_query($connection, $deleteQuery);
    } else {
      // Update the user's attributes if any changes are made
      $name = $_POST["name_" . $userId];
      $cpr = $_POST["cpr_" . $userId];
      $cprAttachment = $_POST["cpr_attachment_" . $userId];
      $email = $_POST["email_" . $userId];
      $bloodType = $_POST["blood_type_" . $userId];
      $gender = $_POST["gender_" . $userId];
      $approvalStatus = $_POST["approval_status_" . $userId];

      $updateQuery = "UPDATE user SET name = '$name', cpr = '$cpr', cpr_attachment = '$cprAttachment', email = '$email', blood_type = '$bloodType', gender = '$gender', approval_status = '$approvalStatus' WHERE ID = $userId";
      mysqli_query($connection, $updateQuery);
    }
  }

  // Refresh the page to reflect the updated values
  header("Location: ".$_SERVER['PHP_SELF']);
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Modify users</title>
  
  <link rel="stylesheet" type="text/css" href="approve.css">
</head>
<body>
  <h1>Super Admin Panel</h1>
  <div class="container">
    <h2>User Data</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
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
          <th>Action</th>
        </tr>
        <?php foreach ($users as $user): ?>
          <tr>
            <td><?php echo $user['ID']; ?></td>
            <td><input type="text" name="name_<?php echo $user['ID']; ?>" value="<?php echo $user['name']; ?>"></td>
            <td><input type="text" name="cpr_<?php echo $user['ID']; ?>" value="<?php echo $user['cpr']; ?>"></td>
            <td><input type="text" name="cpr_attachment_<?php echo $user['ID']; ?>" value="<?php echo $user['cpr_attachment']; ?>"></td>
            <td><input type="text" name="email_<?php echo $user['ID']; ?>" value="<?php echo $user['email']; ?>"></td>
            <td><input type="text" name="blood_type_<?php echo $user['ID']; ?>" value="<?php echo $user['blood_type']; ?>"></td>
            <td><input type="text" name="gender_<?php echo $user['ID']; ?>" value="<?php echo $user['gender']; ?>"></td>
            <td>
              <select name="approval_status_<?php echo $user['ID']; ?>">
                <option value="pending" <?php if ($user['approval_status'] === 'pending') echo 'selected'; ?>>Pending</option>
                <option value="approved" <?php if ($user['approval_status'] === 'approved') echo 'selected'; ?>>Approved</option>
                <option value="rejected" <?php if ($user['approval_status'] === 'rejected') echo 'selected'; ?>>Rejected</option>
              </select>
            </td>
            <td><?php echo $user['approval_status']; ?></td>
            <td>
              <button type="submit" name="delete_<?php echo $user['ID']; ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
      <input type="submit" value="Submit changes">
    </form>
  </div>
</body>
</html>
