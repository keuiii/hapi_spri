<?php
// classes/database.php - Centralized Database Connection for HAPPY-SPRAYS

class Database {
    private static $instance = null;
    private $connection;
    
    private $host = 'localhost';
    private $database = 'happy_sprays';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    
    private function __construct() {
        $this->connect();
    }
    private function __clone() {}
    public function __wakeup() { throw new Exception("Cannot unserialize singleton"); }
    public static function getInstance() {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }
    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false,
            ];
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    public function getConnection() { return $this->connection; }

    // --- Generic Query Helpers ---
    public function select($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }
    public function fetch($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) { return false; }
    }
    public function insert($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $this->connection->lastInsertId();
        } catch (PDOException $e) { return false; }
    }
    public function update($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) { return false; }
    }
    public function delete($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) { return false; }
    }

    // --- Dashboard Helpers ---
    public function getCount($table) {
        try {
            $stmt = $this->connection->prepare("SELECT COUNT(*) as total FROM {$table}");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getLowStockProducts($threshold = 10) {
        return $this->select("SELECT * FROM perfumes WHERE stock < ?", [$threshold]);
    }

    // --- Product Helpers ---
    public function addProduct($data, $files) {
        $name = $data['name'];
        $brand = $data['brand'];
        $price = $data['price'];
        $gender = $data['gender'];
        $stock = $data['stock'] ?? 0;
        $description = $data['description'] ?? '';
        $ml_size = $data['ml_size'] ?? '';

        $image = null;
        if (!empty($files['image']['name'])) {
            $image = $files['image']['name'];
            move_uploaded_file($files['image']['tmp_name'], "images/" . basename($image));
        }

        $image2 = null;
        if (!empty($files['image2']['name'])) {
            $image2 = $files['image2']['name'];
            move_uploaded_file($files['image2']['tmp_name'], "images/" . basename($image2));
        }

        return $this->insert(
            "INSERT INTO perfumes (name, brand, price, gender, image, image2, description, stock, ml_size)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$name, $brand, $price, $gender, $image, $image2, $description, $stock, $ml_size]
        );
    }

    public function getProductById($id) {
        return $this->fetch("SELECT * FROM perfumes WHERE id = ?", [$id]);
    }

    public function updateProduct($data, $files) {
        $id = intval($data['id']);
        $name = $data['name'];
        $brand = $data['brand'];
        $price = $data['price'];
        $gender = $data['gender'];
        $stock = $data['stock'];
        $description = $data['description'];
        $ml_size = $data['ml_size'];

        $params = [$name, $brand, $price, $gender, $stock, $description, $ml_size];
        $updateQuery = "UPDATE perfumes SET name = ?, brand = ?, price = ?, gender = ?, stock = ?, description = ?, ml_size = ?";

        if (!empty($files['image']['name'])) {
            $image = $files['image']['name'];
            move_uploaded_file($files['image']['tmp_name'], "images/" . basename($image));
            $updateQuery .= ", image = ?";
            $params[] = $image;
        }
        if (!empty($files['image2']['name'])) {
            $image2 = $files['image2']['name'];
            move_uploaded_file($files['image2']['tmp_name'], "images/" . basename($image2));
            $updateQuery .= ", image2 = ?";
            $params[] = $image2;
        }

        $updateQuery .= " WHERE id = ?";
        $params[] = $id;

        return $this->update($updateQuery, $params);
    }
    public function getOrdersCount() {
    try {
        $tableExists = $this->fetch("SHOW TABLES LIKE 'orders'");
        if ($tableExists) {
            $row = $this->fetch("SELECT COUNT(*) as cnt FROM orders");
            return $row ? (int)$row['cnt'] : 0;
        }
    } catch (Exception $e) {
        return 0;
    }
    return 0;
}

    public function getUsersCount() {
        try {
            $tableExists = $this->fetch("SHOW TABLES LIKE 'users'");
            if ($tableExists) {
                $row = $this->fetch("SELECT COUNT(*) as cnt FROM users");
                return $row ? (int)$row['cnt'] : 0;
            }
        } catch (Exception $e) {
            return 0;
        }
        return 0;
    }

    public function getProductsCount() {
        try {
            $row = $this->fetch("SELECT COUNT(*) as cnt FROM perfumes");
            return $row ? (int)$row['cnt'] : 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    public function getPaginatedResults($table, $page = 1, $limit = 10, $orderBy = "id DESC") {
    $offset = ($page - 1) * $limit;

    // Count total rows
    $countStmt = $this->connection->prepare("SELECT COUNT(*) as total FROM $table");
    $countStmt->execute();
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Fetch paginated data
    $stmt = $this->connection->prepare("SELECT * FROM $table ORDER BY $orderBy LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        "data" => $data,
        "total" => $total,
        "total_pages" => ceil($total / $limit),
        "current_page" => $page
    ];
}
/* -------------------- CART METHODS -------------------- */
    public function getCart() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        return $_SESSION['cart'];
    }

    public function addToCart($id, $name, $price, $image, $qty = 1) {
        $qty = max(1, (int)$qty);

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += $qty;
        } else {
            $_SESSION['cart'][$id] = [
                'name'     => $name,
                'price'    => (float)$price,
                'image'    => $image,
                'quantity' => $qty,
            ];
        }
    }

    public function updateCartQuantity($id, $qty) {
        $qty = max(1, (int)$qty);
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] = $qty;
        }
    }

    public function removeFromCart($id) {
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
    }

    public function clearCart() {
        $_SESSION['cart'] = [];
    }

    public function getCartTotals() {
        $cart = $this->getCart();
        $grandTotal = 0;
        foreach ($cart as $item) {
            $grandTotal += $item['price'] * $item['quantity'];
        }
        return $grandTotal;
    }
    public function isCartEmpty(): bool {
    return empty($_SESSION['cart']);
}  

    public function calculateGrandTotal(): float {
        $grand_total = 0;
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $grand_total += $item['price'] * $item['quantity'];
            }
        }
        return $grand_total;
    }

    public function getCartItems(): array {
        return $_SESSION['cart'] ?? [];
    }
    public function authenticateUser(string $email, string $password, string $expectedRole = 'user') {
    try {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Optional: enforce role check
            if ($expectedRole && $user['role'] !== $expectedRole) {
                return false;
            }
            return $user; // return user row
        }
        return false;
    } catch (PDOException $e) {
        error_log("Auth error: " . $e->getMessage());
        return false;
    }

}


}
?>
