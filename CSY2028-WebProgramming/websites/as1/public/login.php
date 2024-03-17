<?php

require 'dbconnection.php';


session_start();

// Checking if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Checking if the email and password are provided
    if(isset($_POST['email']) && isset($_POST['password'])) {
        
        $statement = $Connection->prepare("SELECT * FROM register WHERE Email = :email");
        $statement->bindParam(':email', $_POST['email']);
        $statement->execute();
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        // Verifying if the user exists and password is correct
        if($user && password_verify($_POST['password'], $user['Password'])) {
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['Role'] = $user['Role']; 
            
            if($user['Role'] == 'admin') {
                
                header("Location: admin_dashboard.php");
                exit;
            } elseif($user['Role'] == 'user') {
                
                header("Location: user_dashboard.php");
                exit;
            }
        } else {
            //  This is Invalid credentials
            $login_error = "Invalid email or password!";
        }
    } else {
        // Email or password are not provided
        $login_error = "Please provide both email and password!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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
<div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="post">
            <label for="login_email">Email:</label>
            <input type="text" name="email" id="login_email" required>
            
            <label for="login_password">Password:</label>
            <input type="password" name="password" id="login_password" required>

            <input type="submit" name="login_as_admin" value="Login as Admin">
            <br> <br>
            <input type="submit" name="login_as_user" value="Login as User">

            <?php if(isset($login_error)) { ?>
                <p style="color: red;"><?php echo $login_error; ?></p>
            <?php } ?>
            </form>
    </div>
    <div class = "backtohome">
<a href = "index.php"> Back to Home </a>
    </div>
</body>
</html>
