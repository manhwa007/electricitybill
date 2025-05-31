<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $units = intval($_POST['units']);
    $date = $_POST['date']; // YYYY-MM format

    // Slab logic
    if ($units <= 100) {
        $amount = $units * 3;
    } elseif ($units <= 300) {
        $amount = (100 * 3) + (($units - 100) * 5);
    } else {
        $amount = (100 * 3) + (200 * 5) + (($units - 300) * 8);
    }

    // Insert query
    $query = "INSERT INTO bills (user_id, units, amount, status, date) 
              VALUES ('$user_id', '$units', '$amount', 'unpaid', '$date-01')";

    if (mysqli_query($conn, $query)) {
        $message = "✅ Bill submitted successfully!";
    } else {
        $message = "❌ Failed to submit bill.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Bill</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .form-box {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #aaa;
        }

        input, select {
            width: 100%;
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            padding: 10px 20px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .msg {
            margin-top: 15px;
            font-weight: bold;
            color: green;
        }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>➕ Submit New Bill</h2>
        <form method="POST">
            <label>Units Consumed</label>
            <input type="number" name="units" required min="1">

            <label>Bill Month</label>
            <input type="month" name="date" required>

            <button type="submit">Submit Bill</button>
        </form>

        <?php if ($message): ?>
            <div class="msg"><?= $message ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
