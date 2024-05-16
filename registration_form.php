<!-- registration_form.php -->
<!DOCTYPE html>
<html>
<head>
  <title>Society Registration Form</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <?php
  // Establish a connection to MySQL database
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "database";

  $conn = new mysqli($servername, $username, $password, $dbname);

  // Check the connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // Function to sanitize and validate input
  function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  // Process form submission
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitizeInput($_POST["name"]);
    $email = sanitizeInput($_POST["email"]);
    $code = sanitizeInput($_POST["code"]);
    $cpr = sanitizeInput($_POST["cpr"]);
    $bloodType = sanitizeInput($_POST["blood_type"]);
    $gender = sanitizeInput($_POST["gender"]);
    $cprAttachment = $_FILES["cpr_attachment"]["name"];
    $cprAttachmentTmpName = $_FILES["cpr_attachment"]["tmp_name"];
    $cprAttachmentError = $_FILES["cpr_attachment"]["error"];
    $cprAttachmentSize = $_FILES["cpr_attachment"]["size"];

    $societySql = "SELECT name, code FROM society WHERE code = ?";
    $societyStmt = $conn->prepare($societySql);
    $societyStmt->bind_param("s", $code);
    $societyStmt->execute();
    $societyResult = $societyStmt->get_result();
    $societyRow = $societyResult->fetch_assoc();
    $sname= $societyRow["name"];
    
    // Check if societyname already exists
    
    $societyNameExistsSql = "SELECT id FROM vipmember WHERE societyname = ?";
    $societyNameExistsStmt = $conn->prepare($societyNameExistsSql);
    $societyNameExistsStmt->bind_param("s", $sname);
    $societyNameExistsStmt->execute();
    $societyNameExistsResult = $societyNameExistsStmt->get_result();



    // Check if cpr already exists
    $cprExistsSql = "SELECT id FROM vipmember WHERE cpr = ?";
    $cprExistsStmt = $conn->prepare($cprExistsSql);
    $cprExistsStmt->bind_param("s", $cpr);
    $cprExistsStmt->execute();
    $cprExistsResult = $cprExistsStmt->get_result();

    if ($societyNameExistsResult->num_rows > 0 || $cprExistsResult->num_rows > 0) {
      echo "<p id='message'>Someone has already registered with the provided society name or CPR.</p>";
    } else {
      // Retrieve society ID based on the code
      $societyIdSql = "SELECT id, name FROM society WHERE code = ?";
      $societyIdStmt = $conn->prepare($societyIdSql);
      $societyIdStmt->bind_param("s", $code);
      $societyIdStmt->execute();
      $societyIdResult = $societyIdStmt->get_result();

      if ($societyIdResult->num_rows > 0) {
        $societyIdRow = $societyIdResult->fetch_assoc();
        $societyId = $societyIdRow["id"];
        $societyName = $societyIdRow["name"];

        // Insert data into the vipmember table
        $insertSql = "INSERT INTO vipmember (name, cpr, cpr_attachment, email, blood_type, gender, societyname)
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("sssssss", $name, $cpr, $cprAttachment, $email, $bloodType, $gender, $societyName);

        if ($insertStmt->execute()) {
          // Upload CPR attachment if provided
          if ($cprAttachmentSize > 0) {
            $targetDir = "attachments/";
            $targetFile = $targetDir . basename($cprAttachment);

            if ($cprAttachmentError === 0) {
              if (move_uploaded_file($cprAttachmentTmpName, $targetFile)) {
                echo "<p id='message'>Registration submitted successfully.</p>";
              } else {
                echo "<p id='message'>Error uploading CPR attachment.</p>";
              }
            } else {
              echo "<p id='message'>Error: " . $cprAttachmentError . "</p>";
            }
          } else {
            echo "<p id='message'>Registration submitted successfully.</p>";
          }
        } else {
          echo "<p id='message'>Error: " . $insertStmt->error . "</p>";
        }
      } else {
        echo "<p id='message'>Invalid society code.</p>";
      }

      $societyIdStmt->close();
      $insertStmt->close();
    }

    $societyNameExistsStmt->close();
    $cprExistsStmt->close();
  } else {
    $code = sanitizeInput($_GET["code"]);

    // Retrieve society details based on the code
    $societySql = "SELECT name, code FROM society WHERE code = ?";
    $societyStmt = $conn->prepare($societySql);
    $societyStmt->bind_param("s", $code);
    $societyStmt->execute();
    $societyResult = $societyStmt->get_result();

    /*$name = sanitizeInput($_GET["code"]);
    $societyNameExistsSql = "SELECT id FROM vipmember WHERE societyname = ?";
    $societyNameExistsStmt = $conn->prepare($societyNameExistsSql);
    $societyNameExistsStmt->bind_param("s", $name);
    $societyNameExistsStmt->execute();
    $societyNameExistsResult = $societyNameExistsStmt->get_result();

    echo"$societyNameExistsResult->num_rows";

    if($societyNameExistsResult->num_rows < 0)
    */

    $societySql = "SELECT name, code FROM society WHERE code = ?";
    $societyStmt = $conn->prepare($societySql);
    $societyStmt->bind_param("s", $code);
    $societyStmt->execute();
    $societyResult = $societyStmt->get_result();
    $societyRow = $societyResult->fetch_assoc();
    $sname= $societyRow["name"];
    
    // Check if societyname already exists
    
    $societyNameExistsSql = "SELECT id FROM vipmember WHERE societyname = ?";
    $societyNameExistsStmt = $conn->prepare($societyNameExistsSql);
    $societyNameExistsStmt->bind_param("s", $sname);
    $societyNameExistsStmt->execute();
    $societyNameExistsResult = $societyNameExistsStmt->get_result();


    if($societyNameExistsResult->num_rows > 0 )
    echo"someone already registered";
    else{
      $societySql = "SELECT name, code FROM society WHERE code = ?";
      $societyStmt = $conn->prepare($societySql);
      $societyStmt->bind_param("s", $code);
      $societyStmt->execute();
      $societyResult = $societyStmt->get_result();
      
    if ($societyResult->num_rows > 0 ) {
      $societyRow = $societyResult->fetch_assoc();
      echo "<h2>Welcome " . $societyRow["name"] . "</h2>";
      echo "<form action='registration_form.php' method='post' enctype='multipart/form-data'>";
      echo "<label for='name'>Full Name:</label>";
      echo "<input type='text' id='name' name='name' required><br><br>";
      echo "<label for='email'>Email:</label>";
      echo "<input type='email' id='email' name='email' required><br><br>";
      echo "<label for='cpr'>CPR (9 digits):</label>";
      echo "<input type='text' id='cpr' name='cpr' pattern='\\d{9}' required><br><br>";
      echo "<label for='blood_type'>Blood Type:</label>";
      echo "<select id='blood_type' name='blood_type' required>";
      echo "<option value='A+'>A+</option>";
      echo "<option value='A-'>A-</option>";
      echo "<option value='B+'>B+</option>";
      echo "<option value='B-'>B-</option>";
      echo "<option value='AB+'>AB+</option>";
      echo "<option value='AB-'>AB-</option>";
      echo "<option value='O+'>O+</option>";
      echo "<option value='O-'>O-</option>";
      echo "</select><br><br>";
      echo "<label for='gender'>Gender:</label>";
      echo "<select id='gender' name='gender' required>";
      echo "<option value='male'>Male</option>";
      echo "<option value='female'>Female</option>";
      echo "</select><br><br>";
      echo "<label for='cpr_attachment'>CPR Attachment:</label>";
      echo "<input type='file' id='cpr_attachment' name='cpr_attachment'><br><br>";
      echo "<input type='hidden' name='code' value='" . $code . "'>";
      echo "<input type='submit' value='Register'>";
      echo "</form>";
    } else {
      echo "Society not found.";
    }
  }
    $societyStmt->close();
  }

  $conn->close();
  ?>
</body>
</html>
