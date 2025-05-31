<?php
$servername = "localhost";
$username = "root";
$password = "";  // ✅ was incorrectly written as $hashed_password before
$dbname = "electricity_bill_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>


