<?php
$navbarPath = __DIR__ . '/navbar.html';
if (file_exists($navbarPath)) {
    include $navbarPath;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ai_technologies";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jobTitle = trim($_POST['jobTitle'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($jobTitle) || empty($name) || empty($mobile) || empty($email)) {
        echo "<script>alert('Please fill all required fields.'); window.history.back();</script>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.'); window.history.back();</script>";
        exit;
    }
    if (!preg_match('/^\d{10,15}$/', $mobile)) {
        echo "<script>alert('Invalid mobile number. Must be 10-15 digits.'); window.history.back();</script>";
        exit;
    }

    if (!isset($_FILES["resume"]) || $_FILES["resume"]["error"] !== UPLOAD_ERR_OK) {
        echo "<script>alert('Please upload your resume.'); window.history.back();</script>";
        exit;
    }

    $resume = $_FILES["resume"];
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $originalName = basename($resume["name"]);
    $cleanName = preg_replace("/[^a-zA-Z0-9\._-]/", "", $originalName);
    $file_name = uniqid() . "_" . $cleanName;
    $target_file = $target_dir . $file_name;

    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $file_size = $resume["size"];
    $allowed_types = ["pdf", "doc", "docx"];
    $max_size = 1 * 1024 * 1024; // 1MB

    if (!in_array($file_type, $allowed_types)) {
        echo "<script>alert('Only PDF, DOC, and DOCX files are allowed!'); window.history.back();</script>";
        exit;
    }

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

    if ($file_size > $max_size) {
        echo "<script>alert('File size exceeds 1MB limit!'); window.history.back();</script>";
        exit;
    }

    if (move_uploaded_file($resume["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO job_applications (jobTitle, name, mobile, email, resume) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $jobTitle, $name, $mobile, $email, $target_file);

        if ($stmt->execute()) {
            echo "<script>alert('Application Submitted Successfully!'); window.location.href='Career.html';</script>";
            exit;
        } else {
            echo "<script>alert('Error submitting your application. Please try again.'); window.history.back();</script>";
            exit;
        }
        $stmt->close();
    } else {
        echo "<script>alert('File upload failed.'); window.history.back();</script>";
        exit;
    }
}

$conn->close();
?>
