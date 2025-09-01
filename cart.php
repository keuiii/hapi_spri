<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// --- ADD TO CART ---
if (isset($_POST['add_to_cart'])) {
    $id    = $_POST['id'];
    $name  = $_POST['name'] ?? '';
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $image = $_POST['image'] ?? '';
    $qty   = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1; // ✅ use posted qty

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] += $qty; // ✅ add by qty
    } else {
        $_SESSION['cart'][$id] = [
            'name'     => $name,
            'price'    => $price,
            'image'    => $image,
            'quantity' => $qty, // ✅ set to qty
        ];
    }

    // kapag AJAX call lang → wag mag-render ng buong cart
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        echo "success";
        exit;
    }
}

// --- UPDATE QUANTITY ---
if (isset($_POST['update_qty'])) {
    $id  = $_POST['id'];
    $qty = max(1, (int)$_POST['quantity']); // bawal 0 o negative
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] = $qty;
    }
    header("Location: cart.php");
    exit;
}

// --- REMOVE ITEM ---
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
    header("Location: cart.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Cart</title>
    <style>
        body {font-family:'Segoe UI', sans-serif; background:#fff; margin:20px; color:#000;}
        h1 {text-align:center; margin-bottom:30px;}
        table {width:80%; margin:auto; border-collapse:collapse;}
        th, td {padding:12px; border:1px solid #000; text-align:center;}
        th {background:#f2f2f2;}
        img {width:80px; height:80px; object-fit:cover; border-radius:5px; border:1px solid #000;}
        .remove-btn {padding:6px 12px; background:#ff4d4d; color:#fff; text-decoration:none; border-radius:4px;}
        .remove-btn:hover {background:#cc0000;}
        .checkout-btn {display:block; width:220px; margin:30px auto; padding:12px; border:1px solid #000; text-align:center; border-radius:6px; text-decoration:none; color:#000;}
        .checkout-btn:hover {background:#000; color:#fff;}
        .empty {text-align:center; font-size:18px; margin-top:50px;}
        .qty-input {width:70px; padding:6px; text-align:center; border:1px solid #000; border-radius:6px;}
        .update-btn {padding:6px 10px; border:1px solid #000; background:#fff; cursor:pointer; border-radius:6px;}
        .update-btn:hover {background:#000; color:#fff;}
        .back-btn {display:inline-block; margin:10px 0 20px 20px; padding:8px 16px; border:1px solid #000; text-decoration:none; color:#000; border-radius:6px;}
        .back-btn:hover {background:#000; color:#fff;}
    </style>
</head>
<body>
    <a href="index.php" class="back-btn">← Back to Shop</a>
    <h1>My Cart</h1>

    <?php if (!empty($_SESSION['cart'])): ?>
    <table>
        <tr>
            <th>Image</th>
            <th>Perfume</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
        <?php 
        $grand_total = 0;
        foreach ($_SESSION['cart'] as $id => $item): 
            $total = $item['price'] * $item['quantity'];
            $grand_total += $total;
        ?>
        <tr>
            <td><img src="images/<?= htmlspecialchars($item['image']) ?>" alt=""></td>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td>₱<?= number_format($item['price'], 2) ?></td>
            <td>
                <form method="post" style="display:flex; gap:6px; justify-content:center; align-items:center;">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                    <input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>" min="1" step="1" class="qty-input">
                    <button type="submit" name="update_qty" class="update-btn">Update</button>
                </form>
            </td>
            <td>₱<?= number_format($total, 2) ?></td>
            <td><a href="cart.php?remove=<?= urlencode($id) ?>" class="remove-btn">Remove</a></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <th colspan="4">Grand Total</th>
            <th colspan="2">₱<?= number_format($grand_total, 2) ?></th>
        </tr>
    </table>

    <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
    <?php else: ?>
        <p class="empty">Your cart is empty.</p>
    <?php endif; ?>
</body>
</html>
