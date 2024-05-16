<!-- index.php -->
<!DOCTYPE html>
<html>
<head>
  <title>VIP User Registration</title>
  <link rel="stylesheet" type="text/css" href="style.css">
  <script>
    setTimeout(function(){
      var message = document.getElementById("message");
      if (message) {
        message.style.display = "none";
      }
    }, 3000);
  </script>
</head>
<body>
  <h1>Add VIP Member</h1>
  <form action="vip_register.php" method="post" enctype="multipart/form-data">
    <!-- Form fields -->
    <label for="name">Society Name:</label>
    <input type="text" id="name" name="name" required><br><br>

    <label for="code">Code:</label>
    <input type="text" id="code" name="code" required><br><br> 

    <input type="submit" value="Add Member">
    <br><br> <br>

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

  // Include the QR code library
  include('phpqrcode/qrlib.php');

  // Function to generate and save the QR code
  function generateQRCode($code, $name)
  {
      $qrCodeData = "http://localhost/Blood%20App/registration_form.php?code=" . $code; // Replace with the actual URL for registration form

      // Generate the QR code image
      $qrCodePath = "qrcodes/" . $code . ".png"; // Path to save the QR code image
      QRcode::png($qrCodeData, $qrCodePath, QR_ECLEVEL_L, 3); // Adjust the error correction level (QR_ECLEVEL_L) and size (10) as per your requirements

      return $qrCodePath;
  }

  // Process form submission
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $code = $_POST["code"];

    // Check if the name or code already exists in the society table
    $checkSql = "SELECT * FROM society WHERE name = '$name' OR code = '$code'";
    $checkResult = $conn->query($checkSql);

    if ($checkResult->num_rows > 0) {
      echo "<p id='message' style='color: red; font-size: 1.2em;'>Name or code already exists. Please enter a unique name and code.</p>
      </p>";
    } else {
      // Insert data into the society table
      $sql = "INSERT INTO society (name, code) VALUES ('$name', '$code')";

      if ($conn->query($sql) === TRUE) {
        echo "<p id='message'>Member added successfully.</p>";

        // Generate and save the QR code
        $qrCodePath = generateQRCode($code, $name);

        // Display the QR code image and copy button
        echo "<h2>QR Code:</h2>";
        echo "<img id='qrCodeImg' src='" . $qrCodePath . "' alt='QR Code'>";
        echo "<br><br>";
        echo "<button onclick='copyQRCodeImage()'>Copy QR Code Image</button>";
      } else {
        echo "<p id='message'>Error: " . $sql . "<br>" . $conn->error . "</p>";
      }
    }
  }
  echo "</form>";
  // Drop table row action
  if (isset($_GET['action']) && $_GET['action'] == 'drop' && isset($_GET['code'])) {
    $code = $_GET['code'];
    $dropSql = "DELETE FROM society WHERE code = '$code'";
    if ($conn->query($dropSql) === TRUE) {
      echo "<p id='message'>Table row dropped successfully.</p>";
    } else {
      echo "<p id='message'>Error dropping table row: " . $conn->error . "</p>";
    }
  }

  // Display registered societies and their codes
  $sql = "SELECT id, name, code FROM society";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    echo "<h2>Registered Societies:</h2>";
    echo "<p>Number of registered societies: " . $result->num_rows . "</p>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Code</th><th>QR Code</th><th>Actions</th></tr>";

    while ($row = $result->fetch_assoc()) {
      echo "<tr>";
      echo "<td>" . $row["id"] . "</td>";
      echo "<td>" . $row["name"] . "</td>";
      echo "<td>" . $row["code"] . "</td>";
      
      // Generate and save the QR code
      $qrCodePath = generateQRCode($row["code"], $row["name"]);

      // Display the QR code image in the table
      echo "<td><img src='" . $qrCodePath . "' alt='QR Code'></td>";

      echo "<td><a href='registration_form.php?code=" . $row["code"] . "'>Register</a> | <a href='vip_register.php?action=drop&code=" . $row["code"] . "'>Drop</a></td>";
      echo "</tr>";
    }

    echo "</table>";
  } else {
    echo "No societies registered yet.";
  }

  $conn->close();
  ?>

  <script>
    function copyQRCodeImage() {
      var qrCodeImg = document.getElementById("qrCodeImg");
      if (qrCodeImg) {
        var canvas = document.createElement("canvas");
        var context = canvas.getContext("2d");
        canvas.width = qrCodeImg.width;
        canvas.height = qrCodeImg.height;
        context.drawImage(qrCodeImg, 0, 0);
        canvas.toBlob(function(blob) {
          navigator.clipboard.write([
            new ClipboardItem({
              [blob.type]: blob
            })
          ]).then(function() {
            alert("QR Code image copied to the clipboard!");
          }).catch(function(error) {
            console.error("Error copying QR Code image:", error);
          });
        });
      }
    }
  </script>
</body>
</html>
