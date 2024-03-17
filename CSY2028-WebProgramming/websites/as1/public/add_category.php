<?php
session_start();

// I've Checked if the user is logged in and is an admin
if (!(isset($_SESSION['user_id']) && $_SESSION['Role'] == 'admin')) {
    header("Location: login.php"); 
    exit;
}

require 'dbconnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = $_POST['category_name'];
    $user_id = $_SESSION['user_id'];
    // I am fetching the username based on the user ID in the Database table named "register"
    $userStatement = $Connection->prepare('SELECT Username FROM register WHERE id = :user_id');
    $userStatement->bindParam(':user_id', $user_id);
    $userStatement->execute();
    $user = $userStatement->fetch(PDO::FETCH_ASSOC);
    $username = $user['Username'];

    // inserting category into Category_only table
    $statement = $Connection->prepare("INSERT INTO Category_only (category_name, category_uploaded_by) VALUES (:category_name, :username)");
    $statement->bindParam(':category_name', $category_name);
    $statement->bindParam(':username', $username);
    $statement->execute();
    header("Location: admin_dashboard.php");
    exit;
}
?>
<?php

$categories = $Connection->query('SELECT * FROM Category_only')->fetchAll(PDO::FETCH_ASSOC);

$categoriesToShow = 7;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Category</title>
    <link rel="stylesheet" href="add_category.css" />
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
    <?php if(isset($_SESSION['user_id'])) { 
        if($_SESSION['Role'] == 'admin') { ?>
            <div class="dropdown">
                <span>Admin</span>
                <div class="dropdown-content">
                    <a href="admin_dashboard.php">Dashboard</a>
                    <a href="addAuction.php">Add Auction</a>
                    <a href="add_category.php">Add Category</a>
                    <a href="logout.php">Logout</a>
                </div>
                <span><?php echo $_SESSION['user_id']; ?></span>
            </div>
        <?php } else { ?>
            <div class="dropdown">
                <span>User</span>
                <div class="dropdown-content">
                    <a href="user_dashboard.php">Dashboard</a>
                    <a href="addAuction.php">Add Auction</a>
                    <a href="logout.php">Logout</a>
                </div>
                <span><?php echo $_SESSION['user_id']; ?></span>
            </div>
        <?php } ?>
    <?php } else { ?>
        <a class="login" href="https://as1.v.je/login.php">Login</a>
        <a> / </a>
        <a class="register" href="https://as1.v.je/register.php">Register</a>
    <?php } ?>
</div>
    </form>
</header>

<nav>
    <ul>
        <?php
        $counter = 0;
        foreach ($categories as $category) {
            if ($counter < $categoriesToShow) {
                echo '<li><a class="categoryLink" href="category.php?category=' . $category['category_name'] . '">' . $category['category_name'] . '</a></li>';
            } else {
                break; 
            }
            $counter++;
        }
        ?>
        <li class="more">
            <button class="morebtn">More</button>
            <div class="more-content">
                <?php
                
                for ($i = $counter; $i < count($categories); $i++) {
                    echo '<a href="category.php?category=' . $categories[$i]['category_name'] . '">' . $categories[$i]['category_name'] . '</a>';
                }
                ?>
            </div>
        </li>
    </ul>
</nav>







<div class="form-container">
    <form action="add_category.php" method="post" class="add-category-form">
        <h1>Add Category</h1>
        <div class="form-group">
            <label for="category_name">Category Name:</label>
            <input type="text" name="category_name" id="category_name" required>
        </div>
        <div class="form-group">
            <input type="submit" value="Add Category" class="btn">
        </div>
    </form>
</div>


</body>
</html>