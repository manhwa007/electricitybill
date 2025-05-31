<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM accounts WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Generate new temporary password
        $temp_password = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);
        $hashed = password_hash($temp_password, PASSWORD_DEFAULT);

        // Update password
        $update = $conn->prepare("UPDATE accounts SET password = ? WHERE email = ?");
        $update->bind_param("ss", $hashed, $email);
        $update->execute();

        echo "<script>alert('Temporary password: $temp_password\\nLogin and change your password ASAP.'); window.location.href='login.html';</script>";
    } else {
        echo "<script>alert('Email not found!'); window.location.href='forgot_password.html';</script>";
    }
}
?>
