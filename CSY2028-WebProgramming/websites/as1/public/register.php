<?php
include 'dbconnection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    // Retrieving form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $role = $_POST['role']; 

    //It hashes the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // It checks for existing admin
    $stmt_check_admin = $Connection->prepare("SELECT COUNT(*) FROM register WHERE Role = 'admin'");
    $stmt_check_admin->execute();
    $admin_count = $stmt_check_admin->fetchColumn();

    if ($role === 'admin' && $admin_count > 0) {
        echo "Only one admin is allowed!";
    } else {
        // It prepares and executes SQL query to insert user data
        $stmt = $Connection->prepare("INSERT INTO register (Username, Password, Email, Role) VALUES (:username, :password, :email, :role)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role); 

        // It executes the query
        $stmt->execute();

        echo "Registration successful!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
    <link rel="stylesheet" href="loginregister.css" />
</head>
<body>

















<header>
    <h1> <a href="https://as1.v.je/">
            <span class="C">C</span>
            <span class="a">a</span>
            <span class="r">r</span>
            <span class="b">b</span>
            <span class="u">u</span>
            <span class="y">y</span>
        </a>
    </h1>

    <form action="search.php" method="GET">
    <input type="text" name="search" placeholder="Search for a car title">
    <input type="submit" value="Search">

        <div class="loginregister">
        <a class="login" href="https://as1.v.je/login.php">Login</a>
        <a> / </a>
        <a class="register" href="https://as1.v.je/register.php">Register</a>
</div>
    </form>
</header>



















    <div class="register-container">
        <h2>Register</h2>
        <form action="register.php" method="post">
            <label for="register_name">Username:</label>
            <input type="text" name="username" id="register_name" required>
            
            <label for="register_password">Password:</label>
            <input type="password" name="password" id="register_password" required>

            <label for="register_email">Email:</label>
            <input type="text" name="email" id="register_email" required>

            <label for="role">Role:</label>
            <select name="role" id="role">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>

            <input type="submit" name="register" value="Register">
        </form>
    </div>

    <div class = "backtologin">
        <p> Already have account? </p>
<a href = "login.php"> Login </a>
    </div>

</body>
</html>
