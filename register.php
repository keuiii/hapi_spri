<?php
session_start();
$host="localhost"; $user="root"; $pass=""; $dbname="happy_sprays";
$conn=new mysqli($host,$user,$pass,$dbname);
if($conn->connect_error){ die("DB connection failed: ".$conn->connect_error); }

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];
    $secret = trim($_POST['secret_code']);

    // check secret code
    if ($secret !== "happy2025") {
        $msg = "Invalid registration code.";
    } elseif ($username == "" || $password == "") {
        $msg = "All fields are required.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $msg = "Username already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?,?,?)");
            $stmt->bind_param("sss", $username, $hashed, $role);
            if ($stmt->execute()) {
                $msg = "Account created successfully. <a href='login.php'>Login here</a>";
            } else {
                $msg = "Error: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - Happy Sprays</title>
<style>
    body {font-family:'Segoe UI',sans-serif; background:#f4f4f4; margin:0; display:flex; height:100vh; align-items:center; justify-content:center;}
    .register-box {background:#fff; padding:30px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1); width:340px;}
    h2 {margin:0 0 20px; text-align:center; font-weight:600;}
    input, select {width:100%; padding:10px; margin:8px 0; border:1px solid #ccc; border-radius:6px;}
    button {width:100%; padding:10px; border:none; background:#333; color:#fff; font-weight:600; border-radius:6px; cursor:pointer;}
    button:hover {background:#000;}
    .msg {font-size:14px; text-align:center; margin-bottom:10px; color:red;}
</style>
</head>
<body>
    <form class="register-box" method="post">
        <h2>Create Account</h2>
        <?php if($msg): ?><p class="msg"><?= $msg ?></p><?php endif; ?>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
        <input type="text" name="secret_code" placeholder="Registration Code" required>
        <button type="submit">Register</button>
    </form>
</body>
</html>
