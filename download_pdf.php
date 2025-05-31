<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

session_start();
include 'db.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("User not logged in.");
}

// Get bill_id from URL if available
$bill_id = $_GET['id'] ?? null;

if ($bill_id) {
    // Fetch specific bill
    $res = mysqli_query($conn, "SELECT * FROM bills WHERE id = $bill_id AND user_id = $user_id");
} else {
    // Fetch latest bill if no ID given
    $res = mysqli_query($conn, "SELECT * FROM bills WHERE user_id = $user_id ORDER BY date DESC LIMIT 1");
}

$row = mysqli_fetch_assoc($res);
if (!$row) {
    die("No bill found.");
}

// Prepare HTML for PDF
$html = "
    <h2 style='text-align: center;'>Electricity Bill</h2>
    <hr>
    <p><strong>Bill ID:</strong> {$row['id']}</p>
    <p><strong>Date:</strong> " . date('F Y', strtotime($row['date'])) . "</p>
    <p><strong>Units Consumed:</strong> {$row['units']} kWh</p>
    <p><strong>Amount:</strong> ₹{$row['amount']}</p>
    <p><strong>Status:</strong> {$row['status']}</p>
    <hr>
    <p style='text-align: center;'>Thank you for using Electricity Bill Portal</p>
";

// Generate PDF
$dompdf = new Dompdf(['enable_remote' => true]);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("electricity_bill_{$row['id']}.pdf", ["Attachment" => 0]);
?>
