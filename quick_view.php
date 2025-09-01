<?php
header('Content-Type: application/json');

// Database connection
$conn = new mysqli("localhost", "root", "", "happy_sprays");

// Check connection
if ($conn->connect_error) {
    echo json_encode([
        "error" => true,
        "message" => "Database connection failed"
    ]);
    exit;
}

// Get product ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query product details
// ⚠️ Kung wala kang column na `description`, palitan mo ng `scent AS description`
$sql = "SELECT id, name, price, description, image 
        FROM perfumes 
        WHERE id = $id 
        LIMIT 1";

$res = $conn->query($sql);

if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    echo json_encode([
        "error" => false,
        "id" => $row['id'],
        "name" => $row['name'],
        "price" => $row['price'],
        "description" => $row['description'],
        "image" => $row['image']
    ]);
} else {
    echo json_encode([
        "error" => true,
        "message" => "Product not found"
    ]);
}

$conn->close();
