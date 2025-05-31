<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];

$query = "SELECT DATE_FORMAT(date, '%b') AS month, SUM(units) as total_units
          FROM bills
          WHERE user_id = $user_id
          GROUP BY MONTH(date)
          ORDER BY MONTH(date)";
$result = mysqli_query($conn, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}
echo json_encode($data);
?>
