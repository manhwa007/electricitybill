<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        echo "<script>alert('New passwords do not match.'); window.location.href='change_password.html';</script>";
        exit();
    }

    // Fetch current password from DB
    $stmt = $conn->prepare("SELECT password FROM accounts WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (password_verify($current_password, $user['password'])) {
        // Update new hashed password
        $hashed_new = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE accounts SET password = ? WHERE id = ?");
        $update->bind_param("si", $hashed_new, $user_id);
        $update->execute();

        echo "<script>alert('Password successfully updated!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Incorrect current password!'); window.location.href='change_password.html';</script>";
    }
}
?>
