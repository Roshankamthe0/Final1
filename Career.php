<?php
// Include navbar safely
$navbarPath = __DIR__ . '/navbar.html';
if (file_exists($navbarPath)) {
    include $navbarPath;
} else {
    echo "<!-- navbar.html not found -->";
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ai_technologies";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jobTitle = trim($_POST['jobTitle']);
    $name = trim($_POST['name']);
    $mobile = trim($_POST['mobile']);
    $email = trim($_POST['email']);
    $coverLetter = trim($_POST['coverLetter']);

    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $resume = $_FILES["resume"];
    $file_name = uniqid() . "_" . basename($resume["name"]);
    $target_file = $target_dir . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $file_size = $resume["size"];
    $allowed_types = ["pdf", "doc", "docx"];
    $max_size = 1 * 1024 * 1024;

    // Validate file extension
    if (!in_array($file_type, $allowed_types)) {
        echo "<script>alert('Only PDF, DOC, and DOCX files are allowed!'); window.history.back();</script>";
        exit;
    }

    // Validate file MIME type for added security
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $resume["tmp_name"]);
    finfo_close($finfo);

    $allowed_mimes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    if (!in_array($mime, $allowed_mimes)) {
        echo "<script>alert('Invalid file type uploaded.'); window.history.back();</script>";
        exit;
    }

    // Validate file size
    if ($file_size > $max_size) {
        echo "<script>alert('File size exceeds 1MB limit!'); window.history.back();</script>";
        exit;
    }

    // Move uploaded file
    if (move_uploaded_file($resume["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO job_applications (jobTitle, name, mobile, email, resume, coverLetter) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $jobTitle, $name, $mobile, $email, $target_file, $coverLetter);

        if ($stmt->execute()) {
            echo "<script>alert('Application Submitted Successfully!'); window.location.href='Career.html';</script>";
        } else {
            echo "<script>alert('Error submitting your application. Please try again.'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('File upload failed.'); window.history.back();</script>";
    }
}

// Fetch job applications for display
$sql = "SELECT id, jobTitle, name, mobile, email, coverLetter, resume FROM job_applications";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Job Applications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            margin-top: 70px;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #004d7f;
            color: white;
        }
        a {
            text-decoration: none;
            color: blue;
        }
        a:hover {
            text-decoration: underline;
        }
        table, th, td {
            font-size: 14px !important;
        }
        h1 {
            font-size: 24px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Job Applications</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Job Title</th>
            <th>Name</th>
            <th>Mobile</th>
            <th>Email</th>
            <th>Cover Letter</th>
            <th>Resume</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['jobTitle']); ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['mobile']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($row['coverLetter'])); ?></td>
                <td>
                    <a href="<?php echo htmlspecialchars($row['resume']); ?>" target="_blank">View Resume</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>
