<?php
// my_orders.php - Refactored using centralized Database methods
require_once 'classes/database.php';
$db = Database::getInstance();

// Check if customer is logged in and redirect if not
$db->requireCustomerLogin();

// Get customer orders using the centralized method
$orders = $db->getCustomerOrders();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Orders - HAPPY SPRAYS</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            background-color: #f9f9f9;
            margin: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        h1 {
            color: #333;
            margin: 0;
        }
        
        .back-btn {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        
        .back-btn:hover {
            background: #0056b3;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        
        th, td { 
            border: 1px solid #ddd; 
            padding: 12px; 
            text-align: center; 
        }
        
        th { 
            background: #f8f9fa; 
            font-weight: bold;
            color: #555;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #f0f8ff;
        }
        
        .status {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 15px;
            color: white;
            font-size: 12px;
            display: inline-block;
        }
        
        .status-pending { background-color: #ffc107; color: #000; }
        .status-processing { background-color: #17a2b8; }
        .status-shipped { background-color: #fd7e14; }
        .status-delivered { background-color: #28a745; }
        .status-completed { background-color: #28a745; }
        .status-cancelled { background-color: #dc3545; }
        
        .no-orders {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .no-orders h3 {
            color: #999;
            margin-bottom: 15px;
        }
        
        .shop-btn {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
        
        .shop-btn:hover {
            background: #218838;
        }
        
        .view-details {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }
        
        .view-details:hover {
            text-decoration: underline;
        }
        
        .order-summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        
        .summary-label {
            font-size: 14px;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
            }
            
            table {
                font-size: 14px;
            }
            
            th, td {
                padding: 8px;
            }
            
            .order-summary {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>My Orders</h1>
        <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>

    <?php 
    // Get order summary data using centralized methods
    $totalOrders = $db->getCustomerOrdersCount();
    $pendingOrders = count($db->getCustomerOrdersByStatus('pending'));
    $completedOrders = count($db->getCustomerOrdersByStatus('completed'));
    ?>
    
    <div class="order-summary">
        <div class="summary-item">
            <div class="summary-number"><?= $totalOrders ?></div>
            <div class="summary-label">Total Orders</div>
        </div>
        <div class="summary-item">
            <div class="summary-number"><?= $pendingOrders ?></div>
            <div class="summary-label">Pending</div>
        </div>
        <div class="summary-item">
            <div class="summary-number"><?= $completedOrders ?></div>
            <div class="summary-label">Completed</div>
        </div>
    </div>

    <?php if (!empty($orders)): ?>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Order Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $order): ?>
                <tr>
                    <td><strong>#<?= htmlspecialchars($order['id']) ?></strong></td>
                    <td><?= $db->formatPrice($order['total_amount']) ?></td>
                    <td><?= ucfirst(htmlspecialchars($order['payment_method'] ?? 'N/A')) ?></td>
                    <td>
                        <span class="status <?= $db->getOrderStatusClass($order['status']) ?>">
                            <?= $db->formatOrderStatus($order['status']) ?>
                        </span>
                    </td>
                    <td><?= $db->formatOrderDate($order['created_at']) ?></td>
                    <td>
                        <a href="order_details.php?id=<?= $order['id'] ?>" class="view-details">
                            View Details
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-orders">
            <h3>No Orders Yet</h3>
            <p>You haven't placed any orders yet. Start shopping to see your orders here!</p>
            <a href="shop.php" class="shop-btn">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>