<?php
require_once 'classes/database.php';
session_start();

$db = Database::getInstance();

// Use centralized authentication check
if (!$db->isCustomerLoggedIn()) {
    // Redirect to login and then back to this page after login
    header("Location: customer_login.php?redirect_to=customer_dashboard.php");
    exit();
}

$customer_id = $db->getCurrentCustomerId();

// Fetch customer orders
$result = $db->getCustomerOrders($customer_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Dashboard</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #fff; color: #000; }
    .top-nav { position: fixed; top: 0; left: 0; width: 100%; background: #fff; border-bottom: 1px solid #eee; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; font-size: 22px; font-weight: 700; z-index: 1000; }
    .top-nav .logo { flex: 1; text-align: center; }
    .nav-actions { display: flex; gap: 15px; }
    .nav-actions a { text-decoration: none; color: #000; font-size: 20px; }
    .nav-actions a:hover { color: #555; }
    .logout-btn { display: inline-block; margin: 20px 0; padding: 10px 20px; background: #ff4d4d; color: #fff; text-decoration: none; border-radius: 6px; font-weight: bold; }
    .logout-btn:hover { background: #cc0000; }
    table { width: 90%; margin: 40px auto; border-collapse: collapse; }
    th, td { padding: 12px; border: 1px solid #000; text-align: center; }
    th { background: #f2f2f2; }
  </style>
</head>
<body>
<!-- Top Navbar -->
<div class="top-nav">
    <div class="logo">Happy Sprays</div>
    <div class="nav-actions">
        <a href="customer_dashboard.php" title="My Account">👤</a>
        <a href="cart.php" title="Cart">🛒</a>
        <a href="logout.php" title="Logout">⎋</a>
    </div>
</div>

<div style="margin-top: 80px;">
  <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
  <h2>Your Orders</h2>
  
  <table>
    <thead>
      <tr>
        <th>Order ID</th>
        <th>Total Amount</th>
        <th>Status</th>
        <th>Order Date</th>
        <th>GCash Proof</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result && count($result) > 0): ?>
        <?php foreach ($result as $row): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['id']); ?></td>
            <td><?php echo number_format($row['total_amount'], 2); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            <td>
              <?php if (!empty($row['gcash_proof'])): ?>
                <a href="uploads/proofs/<?php echo htmlspecialchars($row['gcash_proof']); ?>" target="_blank">View Proof</a>
              <?php else: ?>
                N/A
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="5">No orders found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
</body>
</html>
