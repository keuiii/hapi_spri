<?php
session_start();
$host="localhost"; $user="root"; $pass=""; $dbname="happy_sprays";
$conn = new mysqli($host, $user, $pass, $dbname);

if($conn->connect_error){ die("DB connection failed: ".$conn->connect_error); }

// Kunin lahat ng orders
$orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Orders</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #fff;
            color: #000;
            padding: 30px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 16px;
            border: 1px solid #000;
            text-decoration: none;
            color: #000;
            border-radius: 4px;
        }
        .back-btn:hover {
            background: #000;
            color: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
        }
        th {
            background: #f2f2f2;
        }
        a.view-btn {
            padding: 6px 12px;
            border: 1px solid #000;
            text-decoration: none;
            border-radius: 4px;
            color: #000;
            font-size: 13px;
        }
        a.view-btn:hover {
            background: #000;
            color: #fff;
        }
        .no-orders {
            text-align: center;
            margin-top: 40px;
            font-size: 18px;
            color: #666;
        }
    </style>
</head>
<body>
    <a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>
    <h1>All Orders</h1>

    <?php if ($orders->num_rows > 0): ?>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Email</th>
            <th>Total</th>
            <th>Payment</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        <?php while($order = $orders->fetch_assoc()): ?>
        <tr>
            <td>#<?= $order['id'] ?></td>
            <td><?= htmlspecialchars($order['customer_name']) ?></td>
            <td><?= htmlspecialchars($order['email']) ?></td>
            <td>₱<?= number_format($order['total_amount'], 2) ?></td>
            <td><?= ucfirst($order['payment_method']) ?></td>
            <td><?= $order['created_at'] ?></td>
            <td><a class="view-btn" href="receipt.php?order_id=<?= $order['id'] ?>">View</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
        <p class="no-orders">No orders found.</p>
    <?php endif; ?>
</body>
</html>
