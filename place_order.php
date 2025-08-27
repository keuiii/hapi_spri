<?php
session_start();
$host="localhost"; $user="root"; $pass=""; $dbname="happy_sprays";
$conn = new mysqli($host, $user, $pass, $dbname);

if($conn->connect_error){ die("DB connection failed: ".$conn->connect_error); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $conn->real_escape_string($_POST['name']);
    $email    = $conn->real_escape_string($_POST['email']);
    $address  = $conn->real_escape_string($_POST['address']);
    $payment  = $conn->real_escape_string($_POST['payment']);

    $gcash_proof = NULL;
    if ($payment === "gcash" && isset($_FILES['gcash_ref']) && $_FILES['gcash_ref']['error'] == 0) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) { mkdir($targetDir, 0777, true); }

        $filename = time() . "_" . basename($_FILES['gcash_ref']['name']);
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($_FILES['gcash_ref']['tmp_name'], $targetFile)) {
            $gcash_proof = $filename;
        }
    }

    $grand_total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $grand_total += $item['price'] * $item['quantity'];
    }

    // Insert into orders
    $sql = "INSERT INTO orders (customer_name, email, address, payment_method, total_amount, gcash_proof, created_at)
            VALUES ('$name', '$email', '$address', '$payment', '$grand_total', " . 
            ($gcash_proof ? "'$gcash_proof'" : "NULL") . ", NOW())";
    $conn->query($sql);
    $order_id = $conn->insert_id;

    // Insert order items
    foreach ($_SESSION['cart'] as $item) {
        $pname = $conn->real_escape_string($item['name']);
        $qty   = intval($item['quantity']);
        $price = floatval($item['price']);
        $image = $conn->real_escape_string($item['image']);

        $conn->query("INSERT INTO order_items (order_id, product_name, quantity, price, image)
                      VALUES ('$order_id', '$pname', '$qty', '$price', '$image')");
    }

    unset($_SESSION['cart']);
    header("Location: receipt.php?order_id=$order_id");
    exit;
}
?>
