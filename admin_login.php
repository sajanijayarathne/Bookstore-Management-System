<?php
session_start();
include 'includes/db_connect.php';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password']; 

    // Find user in database
    $sql = "SELECT id, password_hash FROM admin_users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        // Check password (using MD5 as set in the setup SQL)
        if (MD5($password) === $row['password_hash']) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            header("Location: manage_books.php");
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Specific login box styling */
        .login-box { max-width: 400px; margin: 100px auto; padding: 20px; border: 1px solid #ccc; background: white; border-radius: 5px; }
        .login-box button { width: 100%; margin-top: 20px; }
    </style>
</head>
<body>
    <header>
        <h1>🔒 Admin Login</h1>
        <a href="index.php" class="button">Back to Catalog</a>
    </header>
    <div class="login-box">
        <?php if ($error) { echo "<p style='color: red; text-align: center; font-weight: bold;'>$error</p>"; } ?>
        <form method="post" action="admin_login.php">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" placeholder="admin" required>
            
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" placeholder="password123" required>
            
            <button type="submit" class="button">Log In</button>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>