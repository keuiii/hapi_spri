<?php
require_once 'classes/database.php';
session_start();

$db = Database::getInstance();
$msg = "";

// Determine where to redirect after login
$redirect_to = $_GET['redirect_to'] ?? 'index.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username_or_email = trim($_POST['username_or_email']);
    $password = trim($_POST['password']);

    // Use the centralized loginCustomerAuth method with flexible username/email
    $result = $db->loginCustomerAuth($username_or_email, $password);

    if ($result['success']) {
        // Redirect logic
        $redirect = $_POST['redirect_to'] ?? 'index.php';
        
        // Ensure valid redirect
        if (empty($redirect) || $redirect === 'customer_login.php') {
            $redirect = 'index.php';
        }
        
        header("Location: " . $redirect);
        exit();
    } else {
        $msg = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Customer Login - Happy Sprays</title>
<style>
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background: #fff; color: #000; }

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
    box-sizing: border-box;
}

.top-nav .logo { 
    flex: 1; 
    text-align: center;
    color: #000;
    text-decoration: none;
}

.nav-actions { 
    display: flex; 
    align-items: center; 
    gap: 20px; 
    position: absolute; 
    right: 20px; 
    top: 50%; 
    transform: translateY(-50%);
}

.profile-link, .cart-link { 
    text-decoration: none; 
    font-size: 20px; 
    color: #000; 
    transition: color 0.3s;
}

.profile-link:hover, .cart-link:hover { 
    color: #555; 
}

/* Sub Navbar */
.sub-nav { 
    position: fixed; 
    top: 60px; 
    left: 0; 
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
    box-sizing: border-box;
}

.sub-nav a { 
    margin: 0 20px; 
    text-decoration: none; 
    color: #000; 
    font-size: 16px;
    transition: color 0.3s;
}

.sub-nav a:hover { 
    color: #555;
}

/* Login Form */
.login-container { 
    background: #f5f5f5; 
    padding: 40px; 
    border-radius: 12px; 
    width: 360px; 
    box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
    text-align: center; 
    margin: 150px auto 0;
    box-sizing: border-box;
}

.login-container h2 { 
    margin-bottom: 20px; 
    font-size: 28px; 
    font-weight: bold;
    color: #333;
}

.login-container input { 
    width: 100%; 
    padding: 12px; 
    margin: 10px 0; 
    border: 1px solid #ddd; 
    border-radius: 8px; 
    font-size: 14px;
    box-sizing: border-box;
}

.login-container input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0,123,255,0.3);
}

.login-container button { 
    width: 100%; 
    padding: 12px; 
    border: 1px solid #000; 
    border-radius: 8px; 
    background: #fff; 
    font-weight: bold; 
    cursor: pointer; 
    transition: all 0.3s;
    font-size: 16px;
}

.login-container button:hover { 
    background: #000; 
    color: #fff;
}

.msg { 
    color: #dc3545; 
    font-size: 14px; 
    margin-bottom: 15px;
    padding: 10px;
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 6px;
}

.extra-links { 
    margin-top: 20px; 
    font-size: 14px;
}

.extra-links a { 
    color: #007bff; 
    text-decoration: none;
    transition: color 0.3s;
}

.extra-links a:hover { 
    text-decoration: underline;
    color: #0056b3;
}

.back-link {
    display: inline-block;
    margin-bottom: 20px;
    color: #007bff;
    text-decoration: none;
    font-size: 14px;
}

.back-link:hover {
    text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 768px) {
    .login-container {
        width: 90%;
        margin: 120px auto 0;
        padding: 30px 20px;
    }
    
    .top-nav {
        padding: 10px 15px;
        font-size: 18px;
    }
    
    .nav-actions {
        right: 15px;
        gap: 15px;
    }
    
    .sub-nav a {
        margin: 0 10px;
        font-size: 14px;
    }
}
</style>
</head>
<body>
<!-- Top Navbar -->
<div class="top-nav">
    <a href="index.php" class="logo">Happy Sprays</a>
    <div class="nav-actions">
        <?php if ($db->isCustomerLoggedIn()): ?>
            <a href="customer_dashboard.php" class="profile-link" title="My Account">👤</a>
            <a href="logout.php" class="profile-link" title="Logout">⎋</a>
        <?php else: ?>
            <a href="customer_login.php?redirect_to=index.php" class="profile-link" title="Login">👤</a>
        <?php endif; ?>
        <a href="cart.php" class="cart-link" title="Shopping Cart">🛒</a>
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
    <a href="index.php" class="back-link">← Back to Home</a>
    
    <h2>Customer Login</h2>
    
    <?php if($msg): ?>
        <div class="msg"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    
    <form method="post">
        <input type="text" name="username_or_email" placeholder="Username or Email Address" required 
               value="<?= htmlspecialchars($_POST['username_or_email'] ?? '') ?>">
        <input type="password" name="password" placeholder="Password" required>
        <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($redirect_to) ?>">
        <button type="submit">Login</button>
    </form>
    
    <div class="extra-links">
        <a href="forgot_password.php">Forgot your password?</a><br><br>
        Don't have an account? <a href="customer_register.php">Sign up here</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const usernameInput = document.querySelector('input[name="username_or_email"]');
    if (usernameInput && !usernameInput.value) {
        usernameInput.focus();
    }
});
</script>

</body>
</html>