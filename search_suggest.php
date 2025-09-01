<?php
header('Content-Type: application/json');

// DB connect
$conn = new mysqli("localhost", "root", "", "happy_sprays");
if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

// Get query
$q = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

$results = [];
if ($q !== '') {
    // table name = perfumes (ayon sa code mo)
    $sql = "SELECT id, name, image, price FROM perfumes 
            WHERE name LIKE '%$q%' 
            OR description LIKE '%$q%' 
            LIMIT 10";
    $res = $conn->query($sql);

    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $results[] = $row;
        }
    }
}

echo json_encode($results);
