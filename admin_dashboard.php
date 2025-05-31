<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == '') {
    header("Location: admin_login.php");
    exit();
}

include 'db.php';

// Optional: double-check role
$email = $_SESSION['email'];
$roleRes = mysqli_query($conn, "SELECT role FROM accounts WHERE email = '$email'");
$roleRow = mysqli_fetch_assoc($roleRes);
if ($roleRow['role'] !== 'admin') {
    echo "<script>alert('Access denied'); window.location.href='login.html';</script>";
    exit();
}

// Get all bills
$query = "
SELECT accounts.name, accounts.email, bills.units, bills.amount, bills.status, bills.date
FROM bills
JOIN accounts ON bills.user_id = accounts.id
ORDER BY bills.date DESC
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="index.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f2f2f2;
        }

        h2 {
            padding: 20px;
            background: #003366;
            color: white;
        }

        table {
            margin: 20px auto;
            width: 90%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 8px rgba(0,0,0,0.2);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background: #007BFF;
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

<h2>🛡️ Admin Panel - All Payments</h2>

<table>
    <tr>
        <th>User Name</th>
        <th>Email</th>
        <th>Units</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Date</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= $row['units'] ?> kWh</td>
        <td>₹<?= $row['amount'] ?></td>
        <td class="<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></td>
        <td><?= date('F Y', strtotime($row['date'])) ?></td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
