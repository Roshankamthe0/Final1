<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ai_technologies";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, name, resume FROM job_applications";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Resumes</title>
</head>
<body>
    <h2>Uploaded Resumes</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Resume</th>
            <th>View</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['resume']; ?></td>
                <td>
                    <a href="<?php echo $row['resume']; ?>" target="_blank">Open PDF</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
