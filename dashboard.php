<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$name = $_SESSION['name'] ?? 'User'; // fallback if name not set
;

include 'db.php';

$user_id = $_SESSION['user_id'];

$paid = 0;
$pending = 0;
$total_units = 0;

// Total Paid
$res1 = mysqli_query($conn, "SELECT SUM(amount) AS total FROM bills WHERE user_id = $user_id AND status = 'paid'");
if ($row1 = mysqli_fetch_assoc($res1)) {
    $paid = $row1['total'] ?? 0;
}

// Total Pending
$res2 = mysqli_query($conn, "SELECT SUM(amount) AS total FROM bills WHERE user_id = $user_id AND status = 'unpaid'");
if ($row2 = mysqli_fetch_assoc($res2)) {
    $pending = $row2['total'] ?? 0;
}

// Total Units
$res3 = mysqli_query($conn, "SELECT SUM(units) AS total FROM bills WHERE user_id = $user_id");
if ($row3 = mysqli_fetch_assoc($res3)) {
    $total_units = $row3['total'] ?? 0;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="index.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #f0f8ff, #dfefff);
            color: #333;
        }

        header {
            background-color: #003366;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
        }

        .nav-links a {
            color: white;
            margin-left: 1.5rem;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .nav-links a:hover {
            color: #ffd700;
            text-shadow: 0 0 10px #fff;
        }

        .container {
            padding: 2rem;
        }

        .welcome {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
        }

        .cards {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            flex: 1;
            min-width: 200px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.03);
        }

        .card h3 {
            margin: 0;
            font-size: 1.5rem;
        }

        .actions {
            margin-top: 2rem;
        }

        .actions a {
            display: inline-block;
            margin: 1rem 1.5rem 0 0;
            padding: 0.8rem 2rem;
            background: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 30px;
            box-shadow: 0 0 10px rgba(0,123,255,0.6);
            transition: all 0.3s;
        }

        .actions a:hover {
            background: #0056b3;
            box-shadow: 0 0 20px rgba(0,123,255,0.9);
        }

        footer {
            margin-top: 4rem;
            background-color: #003366;
            color: white;
            text-align: center;
            padding: 1rem;
        }

        /* Chart placeholder */
        .chart-box {
            margin-top: 3rem;
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            text-align: center;
            font-style: italic;
            color: #777;
        }
      body.dark {
    background: #111;
    color: #eee;
}
.dark .card {
    background: #222;
    color: #eee;
}
.dark header, .dark footer {
    background: #000;
}
.dark a {
    color: #ccc;
}
.dark-toggle {
    background: #007BFF;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: bold;
    cursor: pointer;
    margin-left: 1rem;
    box-shadow: 0 0 6px rgba(0, 123, 255, 0.5);
    transition: 0.3s;
}

.dark-toggle:hover {
    background: #0056b3;
}


    </style>
</head>
<body>

    <header>
        <button onclick="toggleDarkMode()" class="dark-toggle">🌙 Toggle Dark Mode</button>
        <h1>Dashboard</h1>
        <div class="nav-links">
            <a href="edit_profile.php">Profile</a>
           <a href="settings.html">Settings</a>


            <a href="logout.php">Logout</a>
        </div>
    </header>

    <div class="container">
       <div class="welcome">👋 Welcome, <strong><?php echo htmlspecialchars($name); ?></strong>!</div>


        <div class="cards">
            <div class="card">
                <h3>Paid Bills</h3>
                <p>₹<?= $paid ?></p>

            </div>
            <div class="card">
                <h3>Pending Bills</h3>
                <p>₹<?= $pending ?></p>

            </div>
            <div class="card">
                <h3>Total Units</h3>
                <p><?= $total_units ?> kWh</p>

            </div>
        </div>

        <div class="actions">
               <a href="view_bill.php">📄 View Bills</a>
               <a href="download_pdf.php" onclick="showToast('⬇️ Downloading your bill...')">⬇️ Download PDF</a>
               <a href="edit_profile.php">📝 Edit Profile</a>
               <a href="submit_bill.php">➕ Submit Bill</a>

        </div>


       <div class="chart-box">
    <canvas id="usageChart" width="400" height="200"></canvas>
</div>
<script>
fetch('get_chart_data.php')
    .then(res => res.json())
    .then(data => {
        const labels = data.map(row => row.month);
        const units = data.map(row => row.total_units);

        new Chart(document.getElementById('usageChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Electricity Usage (kWh)',
                    data: units,
                    backgroundColor: '#007BFF'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
</script>



    </div>

    <footer>
        <p>&copy; 2025 Electricity Bill Management System</p>
    </footer>
    <div id="toast" style="position: fixed; top: 20px; right: 20px; background: #28a745; color: white; padding: 10px 20px; border-radius: 5px; display: none; box-shadow: 0 0 10px #000;">
       ✅ Success! Your action was completed.
    </div>

    <script>
       function showToast(message = "✅ Action completed!", color = "#28a745") {
        const toast = document.getElementById("toast");
        toast.innerText = message;
        toast.style.background = color;
        toast.style.display = "block";
        setTimeout(() => {
            toast.style.display = "none";
        }, 3000);
    }

    // Call like this when needed: showToast("Bill uploaded!", "#007BFF");
    </script>
    <script>
function toggleDarkMode() {
    document.body.classList.toggle('dark');
    localStorage.setItem('darkMode', document.body.classList.contains('dark') ? 'true' : 'false');
}

// Apply on load
window.onload = function() {
    if (localStorage.getItem('darkMode') === 'true') {
        document.body.classList.add('dark');
    }
}
</script>


</body>
</html>