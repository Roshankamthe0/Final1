<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ai_technologies";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $feedback = trim($_POST['feedback']);
    $rating = isset($_POST['rating']) ? $_POST['rating'] : null;

    if (empty($name) || empty($email) || empty($feedback) || empty($rating)) {
        echo "<script>alert('All fields are required!'); window.history.back();</script>";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format!'); window.history.back();</script>";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO feedback (name, email, feedback, rating) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $email, $feedback, $rating);

    if ($stmt->execute()) {
        echo "<script>alert('Feedback Submitted Successfully!'); window.location.href='feedback.html';</script>";
    } else {
        echo "<script>alert('Error submitting feedback. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
