<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$bill_id = intval($_GET['id']);

// Only allow user to update their own bill
mysqli_query($conn, "UPDATE bills SET status='paid' WHERE id=$bill_id AND user_id=$user_id");

header("Location: view_bill.php");
exit();
?>
