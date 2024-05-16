<?php 
$db = new PDO('mysql:host=localhost;dbname=studentsdb;charset=utf8', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Grades</title>
</head>
<body>
    <form method="get">
        Enter Student ID:
        <input type="text" name="sid"><br>
        <button type="submit">Go</button>
    </form>
    <hr>
</body>
</html>

<?php 
if (!isset($_GET['sid'])) {
    die("ID does not exist");
}

$sid = $_GET['sid'];
$sql = "SELECT * FROM students WHERE sid = $sid";
$csql = "SELECT * FROM grades WHERE sid = $sid";
$studentResult = $db->query($sql);
$gradesResult = $db->query($csql);

if ($studentRow = $studentResult->fetch()) {
    $name = $studentRow['name'];
    $major = $studentRow['major'];
    echo "<h1>Name: $name</h1>\n
          <h1>Major: $major</h1>";
}

echo "<table border='1px' style='border-collapse: collapse;'>
      <tr>
          <th>Course Code</th>
          <th>Grade</th>
          <th>Credits</th>
      </tr>";

$passedCredits = 0;
while ($gradesRow = $gradesResult->fetch()) {
    $courseCode = $gradesRow['coursecode'];
    $grade = $gradesRow['coursegrade'];
    $credits = $gradesRow['credits'];
    $passedCredits += $credits;
    $color = ($grade == 'F') ? 'red' : 'black';

    echo "<tr>
              <td>$courseCode</td>
              <td style='color:$color'>$grade</td>
              <td>$credits</td>
          </tr>";
}

echo "<tr>
          <td colspan='2' style='text-align: right;'>Credits passed:</td>
          <td>$passedCredits</td>
      </tr>
    </table>";
?>
