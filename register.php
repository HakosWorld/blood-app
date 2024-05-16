<?php

function sanitizeInput($data) {
    // Remove whitespace from beginning and end of the data
    $data = trim($data);
    // Remove backslashes
    $data = stripslashes($data);
    // Convert special characters to HTML entities
    $data = htmlspecialchars($data);
    // Return the sanitized data
    return $data;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "database";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $password = $confirmpassword = $cpr = $email = $bloodType = $gender = "";

$errors = array();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = sanitizeInput($_POST['name']);
    $password = sanitizeInput($_POST['password']);
    $confirmpassword = sanitizeInput($_POST['confirmpassword']);
    $cpr = sanitizeInput($_POST['cpr']);
    $email = sanitizeInput($_POST['email']);
    $bloodType = sanitizeInput($_POST['blood_type']);
    $gender = sanitizeInput($_POST['gender']);

    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif ($password !== $confirmpassword) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($cpr)) {
        $errors[] = "CPR is required.";
    } elseif (strlen($cpr) !== 9 || !ctype_digit($cpr)) {
        $errors[] = "CPR should have exactly 9 digits.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
    } else {
        // Sanitize the uploaded file name
        $cprAttachment = $_FILES['cpr_attachment'];
        $cprAttachmentName = sanitizeInput($cprAttachment['name']);
        $cprAttachmentTmpName = $cprAttachment['tmp_name'];
        $cprAttachmentError = $cprAttachment['error'];

        if ($cprAttachmentError === UPLOAD_ERR_OK) {
            $cprAttachmentDestination = 'cpr_attachments/' . $cprAttachmentName;

            move_uploaded_file($cprAttachmentTmpName, $cprAttachmentDestination);
        } else {
            $errors[] = "Error uploading CPR attachment.";
        }

        $stmt = $conn->prepare("INSERT INTO user (name, password, cpr, cpr_attachment, email, blood_type, gender) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $password, $cpr, $cprAttachmentDestination, $email, $bloodType, $gender);

        if ($stmt->execute()) {
            echo "Registration successful.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();

?>
