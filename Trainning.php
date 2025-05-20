<?php

$servername = "localhost";  
$username = "root";
$password = "";
$dbname = "ai_technologies"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $mobile = trim($_POST['mobile']);
    $email = trim($_POST['email']);
    $applied_at = date("Y-m-d H:i:s");

    if (empty($name) || empty($mobile) || empty($email)) {
        echo "<script>alert('All fields are required!'); window.history.back();</script>";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format!'); window.history.back();</script>";
        exit();
    }

    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        echo "<script>alert('Invalid mobile number!'); window.history.back();</script>";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO trainning (name, mobile, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $mobile, $email);

    if ($stmt->execute()) {
        echo "<script>alert('Message Sent Successfully!'); window.location.href='Trainning.html';</script>";
    } else {
        echo "<script>alert('Error submitting your message. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
