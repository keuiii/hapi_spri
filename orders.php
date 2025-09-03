<?php
session_start();
require_once 'classes/database.php';
$db = Database::getInstance();

// Update order status
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'] ?? 'processing';

    $db->update("UPDATE orders SET status = ? WHERE id = ?", [$new_status, $order_id]);
}

// Fetch all orders
$orders = $db->select("SELECT * FROM orders ORDER BY created_at DESC");
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
    select, button {
        padding: 6px 8px;
        border-radius: 4px;
        border: 1px solid #000;
        background: #fff;
        cursor: pointer;
    }
    button[type="submit"] {
        margin-left: 6px;
    }
    form.status-form {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }
</style>
</head>
<body>

<a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>
<h1>All Orders</h1>

<?php if (!empty($orders)): ?>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Email</th>
            <th>Total</th>
            <th>Payment</th>
            <th>Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php foreach ($orders as $order): ?>
        <tr>
            <td>#<?= htmlspecialchars($order['id']) ?></td>
            <td><?= htmlspecialchars($order['customer_name']) ?></td>
            <td><?= htmlspecialchars($order['email']) ?></td>
            <td>₱<?= number_format($order['total_amount'], 2) ?></td>
            <td><?= htmlspecialchars(ucfirst($order['payment_method'])) ?></td>
            <td><?= htmlspecialchars($order['created_at']) ?></td>
            <td>
                <form method="post" class="status-form">
                    <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                    <select name="status">
                        <?php
                        $statuses = [
                            'processing' => 'Processing',
                            'preparing' => 'Preparing',
                            'out for delivery' => 'Out for Delivery',
                            'received' => 'Received'
                        ];
                        foreach ($statuses as $val => $label) {
                            $sel = ($order['status'] === $val) ? ' selected' : '';
                            echo '<option value="'.htmlspecialchars($val).'"'.$sel.'>'.htmlspecialchars($label).'</option>';
                        }
                        ?>
                    </select>
                    <button type="submit" name="update_status">Update</button>
                </form>
            </td>
            <td><a class="view-btn" href="receipt.php?order_id=<?= (int)$order['id'] ?>">View</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p class="no-orders">No orders found.</p>
<?php endif; ?>

</body>
</html>