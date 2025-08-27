<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "happy_sprays";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

// Kunin lahat ng users
$result = $conn->query("SELECT id, username, role, created_at FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Users List</title>
<style>
    body { font-family: 'Segoe UI', sans-serif; background:#fff; color:#000; margin:30px; }
    h1 { text-align:center; }
    table { width:80%; margin:auto; border-collapse:collapse; }
    th, td { border:1px solid #000; padding:10px; text-align:center; }
    th { background:#f2f2f2; }
    .back-btn {
        display:inline-block; margin:20px auto; padding:10px 20px;
        border:1px solid #000; text-decoration:none; color:#000; border-radius:4px;
    }
    .back-btn:hover { background:#000; color:#fff; }
</style>
</head>
<body>
    <h1>Registered Users</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Date Created</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['role']) ?></td>
            <td><?= $row['created_at'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div style="text-align:center;">
        <a href="admin_dashboard.php" class="back-btn">← Back to Admin Dashboard</a>
    </div>
</body>
</html>
