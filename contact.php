<?php

$servername = "localhost";  
$username = "root";
$password = "";
$dbname = "ai_technologies"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $message = trim($_POST['message']);


    if (empty($name) || empty($email) || empty($mobile) || empty($message)) {
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

    $stmt = $conn->prepare("INSERT INTO contact_us (name, email, mobile, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $mobile, $message);

    if ($stmt->execute()) {
        echo "<script>alert('Message Sent Successfully!'); window.location.href='contact.html';</script>";
    } else {
        echo "<script>alert('Error submitting your message. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
