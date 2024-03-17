<?php

require 'dbconnection.php';
session_start();


if (!isset($_GET['id'])) {
    
    header("Location: admin_dashboard.php");
    exit;
}


$category_id = $_GET['id'];
$statement = $Connection->prepare("SELECT * FROM Category_only WHERE id = :id");
$statement->bindParam(':id', $category_id);
$statement->execute();
$category = $statement->fetch(PDO::FETCH_ASSOC);

// Checking if form is submitted or not
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Getting category name from the form
    $category_name = $_POST['category_name'];

    // This is the Update the category in the database
    $updateStatement = $Connection->prepare("UPDATE Category_only SET category_name = :category_name WHERE id = :id");
    $updateStatement->bindParam(':category_name', $category_name);
    $updateStatement->bindParam(':id', $category_id);
    $updateStatement->execute();

    header("Location: admin_dashboard.php");
    exit;
}


$categoriesToShow = 7;


$categories = $Connection->query('SELECT * FROM Category_only')->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Edit Category</title>
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

<form action="edit_category.php?id=<?php echo $category_id; ?>" method="post" class="add-category-form">
    <h1>Edit Category</h1>
    <div class="form-group">
        <label for="category_name">Category Name:</label>
        <input type="text" name="category_name" id="category_name" value="<?php echo $category['category_name']; ?>" required>
    </div>
    <div class="form-group">
        <input type="submit" value="Edit Category" class="btn">
    </div>
</form>

</body>
</html>
