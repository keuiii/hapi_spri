<?php
session_start();

// Kung walang laman ang cart, redirect balik sa cart
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Calculate grand total
$grand_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $grand_total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #fff;
            margin: 20px;
            color: #000;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .back-btn {
            display: inline-block;
            margin: 10px 0 20px 20px;
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
        .checkout-container {
            width: 80%;
            margin: auto;
            display: flex;
            gap: 30px;
        }
        .form-section, .summary-section {
            flex: 1;
            border: 1px solid #000;
            padding: 20px;
            border-radius: 4px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #000;
            border-radius: 4px;
            background: none;
        }
        .place-order-btn {
            display: block;
            width: 100%;
            padding: 12px;
            border: 1px solid #000;
            background: none;
            color: #000;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        .place-order-btn:hover {
            background: #000;
            color: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }
        th, td {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
        }
        th {
            background: #f2f2f2;
        }
        img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border: 1px solid #000;
            border-radius: 4px;
        }
        .gcash-info {
            display: none;
            padding: 15px;
            border: 1px solid #000;
            border-radius: 6px;
            margin-top: 10px;
            font-size: 14px;
            background: #fafafa;
        }
        .gcash-info img {
            width: 150px;
            margin: 10px 0;
            border: 1px solid #000;
            border-radius: 6px;
        }
    </style>
    <script>
        function togglePaymentDetails() {
            let payment = document.getElementById("payment").value;
            let gcashInfo = document.getElementById("gcash-info");

            if (payment === "gcash") {
                gcashInfo.style.display = "block";
            } else {
                gcashInfo.style.display = "none";
            }
        }
    </script>
</head>
<body>
    <a href="cart.php" class="back-btn">← Back to Cart</a>
    <h1>Checkout</h1>

    <div class="checkout-container">
        <!-- Checkout Form -->
        <div class="form-section">
            <form action="place_order.php" method="POST" enctype="multipart/form-data">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>

                <label for="street">Street / Barangay</label>
                <input type="text" id="street" name="street" required>

                <label for="city">City / Municipality</label>
                <input type="text" id="city" name="city" required>

                <label for="province">Province</label>
                <input type="text" id="province" name="province" required>

                <label for="postal">Postal Code</label>
                <input type="text" id="postal" name="postal" required>

                <label for="payment">Payment Method</label>
                <select id="payment" name="payment" required onchange="togglePaymentDetails()">
                    <option value="cod">Cash on Delivery</option>
                    <option value="gcash">GCash</option>
                </select>

                <!-- GCash details -->
                <div id="gcash-info" class="gcash-info">
                    <h3>Pay with GCash</h3>
                    <p>📱 Scan this QR code to pay:</p>
                    <img src="images/gcash-qr.png" alt="GCash QR">
                    <p><strong>Number:</strong> 09123456789</p>
                    <p><strong>Account Name:</strong> Happy Sprays</p>
                    <br>
                    <label for="gcash_ref">Upload Proof of Payment (Screenshot)</label>
                    <input type="file" id="gcash_ref" name="gcash_ref" accept="image/*">
                </div>

                <button type="submit" class="place-order-btn">Place Order</button>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="summary-section">
            <h2>Order Summary</h2>
            <table>
                <tr>
                    <th>Image</th>
                    <th>Perfume</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
                <?php foreach ($_SESSION['cart'] as $item): ?>
                <tr>
                    <td><img src="images/<?= htmlspecialchars($item['image']) ?>" alt=""></td>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <th colspan="3">Grand Total</th>
                    <th>₱<?= number_format($grand_total, 2) ?></th>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
