<?php
session_start();
include "db.php"; // Your DB connection file

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$username = $_SESSION['username'];
$new_username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

// Sanitize input
$new_username = mysqli_real_escape_string($conn, $new_username);
$email = mysqli_real_escape_string($conn, $email);

if (!empty($password)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $query = "UPDATE users SET username='$new_username', email='$email', password='$hashed_password' WHERE username='$username'";
} else {
    $query = "UPDATE users SET username='$new_username', email='$email' WHERE username='$username'";
}

if (mysqli_query($conn, $query)) {
    $_SESSION['username'] = $new_username;
    echo "<script>alert('Profile updated successfully'); window.location.href='dashboard.php';</script>";
} else {
    echo "<script>alert('Update failed'); window.history.back();</script>";
}
?>
