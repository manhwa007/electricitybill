<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$user_id = $_SESSION['user_id'];
$monthFilter = $_GET['month'] ?? '';

if (!empty($monthFilter)) {
    $year = substr($monthFilter, 0, 4);
    $month = substr($monthFilter, 5, 2);
    $result = mysqli_query($conn, "SELECT * FROM bills WHERE user_id = $user_id AND MONTH(date) = $month AND YEAR(date) = $year ORDER BY date DESC");
} else {
    $result = mysqli_query($conn, "SELECT * FROM bills WHERE user_id = $user_id ORDER BY date DESC");
}

?>

<html>
<head>
    <title>View Bills</title>
    <link rel="stylesheet" href="index.css">
    <style>
        table {
            border-collapse: collapse;
            width: 90%;
            margin: 30px auto;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        .paid {
            color: green;
            font-weight: bold;
        }

        .unpaid {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;">📄 Your Bill History</h2>
    <form method="GET" style="text-align:center; margin: 20px;">
    <label for="month">Filter by:</label>
    <input type="month" name="month" id="month" value="<?= $_GET['month'] ?? '' ?>">
    <button type="submit">Filter</button>
    <a href="view_bill.php" style="margin-left: 10px; padding: 6px 14px; background: #17a2b8; color: white; border: none; border-radius: 6px; font-weight: bold; text-decoration: none; box-shadow: 0 2px 6px rgba(0,0,0,0.2);">
    🔄 Reset
</a>



</form>


    <table>
        <tr>
            <th>Month</th>
            <th>Units</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
    <td><?= date('F Y', strtotime($row['date'])) ?></td>
    <td><?= $row['units'] ?> kWh</td>

    <?php
    $billDate = new DateTime($row['date']);
    $today = new DateTime();
    $interval = $billDate->diff($today);
    $isLate = $row['status'] === 'unpaid' && $interval->days > 30;
    $lateFee = $isLate ? 50 : 0;
    $totalWithLate = $row['amount'] + $lateFee;
    ?>

    <td>₹<?= $totalWithLate ?> <?= $lateFee > 0 ? '(incl. ₹50 late fee)' : '' ?></td>
    <td class="<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></td>
    <td>
        <?php if ($row['status'] === 'unpaid'): ?>
            <a href="mark_paid.php?id=<?= $row['id'] ?>" onclick="return confirm('Mark this bill as paid?')">💳 Mark as Paid</a>
        <?php else: ?>
            ✔ Paid
        <?php endif; ?>
    </td>
    <a href="download_pdf.php?id=<?= $row['id'] ?>" class="btn">⬇️ Download</a>

</tr>

        <?php endwhile; ?>
    </table>
</body>
</html>
