<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update profile
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $update_sql = "UPDATE accounts SET name='$name', email='$email' WHERE id = $user_id";
    if (mysqli_query($conn, $update_sql)) {
        $success = "Profile updated successfully!";
    } else {
        $error = "Error updating profile: " . mysqli_error($conn);
    }
}

// Fetch current user data to display in form
$sql = "SELECT name, email FROM accounts WHERE id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
?>

<!-- HTML starts here -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Profile</title>
    <link rel="stylesheet" href="index.css" />
    <style>
        /* Add nice styling for form */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f9f9f9;
            padding: 20px;
        }
        .profile-container {
            max-width: 500px;
            background: white;
            margin: auto;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #003366;
        }
        label {
            display: block;
            margin-top: 1rem;
            font-weight: 600;
            color: #333;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 0.7rem;
            margin-top: 0.3rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
        }
        button {
            margin-top: 2rem;
            width: 100%;
            background: #007BFF;
            color: white;
            border: none;
            padding: 1rem;
            font-size: 1.1rem;
            border-radius: 30px;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(0,123,255,0.6);
            transition: background 0.3s ease;
        }
        button:hover {
            background: #0056b3;
            box-shadow: 0 0 20px rgba(0,123,255,0.9);
        }
        .message {
            margin-top: 1rem;
            text-align: center;
            font-weight: 600;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        a.back-link {
            display: block;
            text-align: center;
            margin-top: 2rem;
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }
        a.back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <h2>Edit Profile</h2>

    <?php if (!empty($success)): ?>
        <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" required value="<?= htmlspecialchars($user['name']) ?>" />

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>" />

        <button type="submit">Save Changes</button>
    </form>

    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>
