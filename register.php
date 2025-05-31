<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Password hash karna
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check agar email pehle se exist karta hai
    $check = $conn->prepare("SELECT id FROM accounts WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo "Email already registered!";
        exit;
    }

    // Insert into table
    $stmt = $conn->prepare("INSERT INTO accounts (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed_password);
    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='login.html';</script>";
exit();

        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
