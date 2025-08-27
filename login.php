<?php
session_start();
$host="localhost"; $user="root"; $pass=""; $dbname="happy_sprays";
$conn=new mysqli($host,$user,$pass,$dbname);
if($conn->connect_error){ die("DB connection failed: ".$conn->connect_error); }

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username == "" || $password == "") {
        $msg = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username=? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed, $role);
            $stmt->fetch();

            if (password_verify($password, $hashed)) {
                // ✅ Login success
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;

                // kung admin → dashboard, kung user → shop
                if ($role === "admin") {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $msg = "Invalid password.";
            }
        } else {
            $msg = "User not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Happy Sprays</title>
<style>
    body {font-family:'Segoe UI',sans-serif; background:#f4f4f4; margin:0; display:flex; height:100vh; align-items:center; justify-content:center;}
    .login-box {background:#fff; padding:30px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1); width:320px;}
    h2 {margin:0 0 20px; text-align:center; font-weight:600;}
    input {width:100%; padding:10px; margin:8px 0; border:1px solid #ccc; border-radius:6px;}
    button {width:100%; padding:10px; border:none; background:#333; color:#fff; font-weight:600; border-radius:6px; cursor:pointer;}
    button:hover {background:#000;}
    .msg {font-size:14px; text-align:center; margin-bottom:10px; color:red;}
    .register-link {display:block; text-align:center; margin-top:12px; font-size:14px;}
</style>
</head>
<body>
    <form class="login-box" method="post">
        <h2>Login</h2>
        <?php if($msg): ?><p class="msg"><?= $msg ?></p><?php endif; ?>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <a href="register.php" class="register-link">Create Account</a>
    </form>
</body>
</html>
