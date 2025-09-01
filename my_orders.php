<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost","root","","happy_sprays");
if ($conn->connect_error) die("DB failed: ".$conn->connect_error);

$customer_id = $_SESSION['customer_id'];
$orders = $conn->query("SELECT * FROM orders WHERE customer_id = $customer_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 10px; text-align: center; }
        th { background: #f2f2f2; }
        .status { font-weight: bold; }
    </style>
</head>
<body>
<h1>My Orders</h1>

<?php if ($orders->num_rows > 0): ?>
<table>
    <tr>
        <th>Order ID</th>
        <th>Total</th>
        <th>Payment</th>
        <th>Status</th>
        <th>Date</th>
    </tr>
    <?php while($o = $orders->fetch_assoc()): ?>
    <tr>
        <td>#<?= $o['id'] ?></td>
        <td>₱<?= number_format($o['total_amount'],2) ?></td>
        <td><?= ucfirst($o['payment_method']) ?></td>
        <td class="status"><?= ucfirst($o['status']) ?></td>
        <td><?= $o['created_at'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
<p>You have no orders yet.</p>
<?php endif; ?>

</body>
</html>
