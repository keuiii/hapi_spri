<?php
session_start();

// Include centralized database connection
require_once 'classes/database.php';

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['username']); // actually an email
    $password = trim($_POST['password']);

    if ($email === "" || $password === "") {
        $msg = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Please enter a valid email address.";
    } else {
        try {
            // Get database instance
            $db = Database::getInstance();

            // Authenticate user (customer role only)
            $user = $db->authenticateUser($email, $password, 'user');

            if ($user) {
                // ✅ Login success
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                header("Location: customer_dashboard.php");
                exit;
            } else {
                $msg = "Invalid email, password, or role.";
            }
        } catch (Exception $e) {
            $msg = "Login error. Please try again.";
            error_log("Login error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Customer Login</title>
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    background: #fff;
    color: #000;
}
/* Top Navbar */
.top-nav {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background: #fff;
    border-bottom: 1px solid #eee;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-family: 'Playfair Display', serif;
    font-size: 22px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
    z-index: 1000;
}
.top-nav .logo { flex: 1; text-align: center; }
.nav-actions {
    display: flex;
    align-items: center;
    gap: 20px;
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
}
.icon-btn, .cart-link, .profile-link {
    background: none; border: none; cursor: pointer; padding: 0;
}
.icon-btn svg, .cart-link svg, .profile-link svg {
    stroke: black; width: 22px; height: 22px;
}
.icon-btn:hover svg, .cart-link:hover svg, .profile-link:hover svg {
    stroke: #555;
}
/* Sub Navbar */
.sub-nav {
    position: fixed;
    top: 60px; left: 0;
    width: 100%;
    background: #fff;
    border-bottom: 1px solid #ccc;
    text-align: center;
    padding: 12px 0;
    z-index: 999;
    font-family: 'Playfair Display', serif;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 1px;
}
.sub-nav a {
    margin: 0 20px;
    text-decoration: none;
    color: #000;
    font-size: 16px;
}
.sub-nav a:hover { color: #555; }
/* Login Form */
.login-container {
    background: #f5f5f5;
    padding: 40px;
    border-radius: 12px;
    width: 360px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    text-align: center;
    margin: 150px auto 0;
}
.login-container h2 {
    margin-bottom: 20px;
    font-size: 28px;
    font-weight: bold;
}
.login-container input {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
}
.login-container button {
    width: 100%;
    padding: 12px;
    border: 1px solid #000;
    border-radius: 8px;
    background: #fff;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}
.login-container button:hover {
    background: #000;
    color: #fff;
}
.msg { color: red; font-size: 14px; margin-bottom: 10px; }
.extra-links { margin-top: 15px; font-size: 14px; }
.extra-links a { color: #000; text-decoration: none; }
.extra-links a:hover { text-decoration: underline; }
</style>
<script>
function validateEmail() {
    const email = document.getElementById('email').value;
    if (!email.includes('@')) {
        alert('Please enter a valid email address containing "@"');
        return false;
    }
    return true;
}
</script>
</head>
<body>
<!-- Top Navbar -->
<div class="top-nav">
    <div class="logo">Happy Sprays</div>
    <div class="nav-actions">
        <button class="icon-btn" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </button>
        <a href="cart.php" class="cart-link">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 7h12l1 12H5L6 7z"/>
                <path d="M9 7V5a3 3 0 0 1 6 0v2"/>
            </svg>
        </a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="customer_dashboard.php" class="profile-link" title="My Account">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </a>
        <?php else: ?>
            <a href="customer_login.php" class="profile-link" title="Login">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Sub Navbar -->
<div class="sub-nav">
    <a href="index.php">Home</a>
    <a href="index.php?gender=Male">For Him</a>
    <a href="index.php?gender=Female">For Her</a>
    <a href="#contact">Contact</a>
    <a href="reviews.php">Reviews</a>
</div>

<!-- Login Form -->
<div class="login-container">
    <h2>Customer Login</h2>
    <?php if($msg): ?><p class="msg"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
    <form method="post" onsubmit="return validateEmail();">
        <input type="text" id="email" name="username" placeholder="E-mail" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <div class="extra-links">
        <a href="#">Forgot your password?</a><br>
        <a href="customer_register.php">Sign up</a>
    </div>
</div>
</body>
</html>
