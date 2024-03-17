<?php

session_start();

// Checking if user is logged in and has admin role or not
if (!isset($_SESSION['user_id']) || $_SESSION['Role'] !== 'admin') {
    // Redirect user to login page or a restricted access page
    header("Location: login.php");
    exit();
}

require 'dbconnection.php';
?>
<?php
// Fetching the categories from the database
$categories = $Connection->query('SELECT * FROM Category_only')->fetchAll(PDO::FETCH_ASSOC);

$categoriesToShow = 7;
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Carbuy Auctions</title>
    <link rel="stylesheet" href="admin_dashboard.css" />
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






<!-- Category List Started -->
<div class = "Category_text_title">
<h1>Category <a class="add-category-btn" href="add_category.php">Add Category</a></h1>
</div>
<div class="category-container">
    <?php
    
    $categories = $Connection->query('SELECT * FROM Category_only');

  
    foreach ($categories as $category) {
        echo '<div class="category-item">';
        echo '<a href="category.php?category=' . $category['category_name'] . '" class="category-link">';
        echo '<button class="btn"><i class="fa fa-folder"></i>' . $category['category_name'] . '</button>';
        echo '</a>';
        echo '<div class="category-options">';
        echo '<a href="edit_category.php?id=' . $category['id'] . '" class="edit">Edit</a>'; // Updated edit link
        echo '<a href="delete_category.php?id=' . $category['id'] . '" class="delete">Delete</a>'; // Updated delete link
        echo '</div>';
        echo '</div>';
    }
    ?>
</div>






















<!-- User List Starts -->

<h1>Users</h1>
<?php

$userStatement = $Connection->prepare('SELECT * FROM register WHERE Role = :role');
$userStatement->bindValue(':role', 'user');
$userStatement->execute();
?>

<?php foreach ($userStatement as $user): ?>
    <div class="user_profile">
        <img src="banners/userprofile.jpg" alt="user image" style="width:100%">
        <div class="user_info">
            <h3><b><?php echo $user['Username']; ?></b></h3> 
            <p><?php echo $user['Email']; ?></p>
            <br><br>
            <form action="delete_user.php" method="post">
    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
    <button type="submit" name="delete">Delete User</button>
</form>
            <br><br>
        </div>
    </div>
<?php endforeach; ?>









<div class="car listing title">
        <h1>Latest Car Listings</h1>
    </div>
    <ul class="carList">
    <?php
    $display = $Connection->prepare('SELECT * FROM auctions');
    $display->execute();

    foreach ($display as $key) {
       
        $bidStatement = $Connection->prepare('SELECT MAX(amount) AS current_bid FROM auction_bids WHERE auction_id = :auction_id');
        $bidStatement->bindParam(':auction_id', $key['id']);
        $bidStatement->execute();
        $currentBid = $bidStatement->fetch(PDO::FETCH_ASSOC);
        $currentBidAmount = $currentBid['current_bid'];

        // Fetching category name based on category_id
        $categoryStatement = $Connection->prepare('SELECT category_name FROM Category_only WHERE id = :category_id');
        $categoryStatement->bindParam(':category_id', $key['category_id']);
        $categoryStatement->execute();
        $category = $categoryStatement->fetch(PDO::FETCH_ASSOC);

        echo '<li>
            <div class="card">
                <img src="' . $key['image_path'] . '" alt="Car Image">';
        if ($currentBidAmount !== null) {
            echo '<p class="price">Current bid: £' . number_format($currentBidAmount, 2) . '</p>';
        } else {
            echo '<p class="price">No bids yet</p>';
        }
        echo '<br>
                <h2>' . $key['car_name'] . '</h2>
                <h3 class="category">' . $category['category_name'] . '</h3> <!-- Display category name -->
                <p>' . $key['description'] . '</p>
                <form action="auction.php" method="get">
                    <input type="hidden" name="id" value="' . $key['id'] . '">
                    <input type="hidden" name="category" value="' . $category['category_name'] . '">
                    <button type="submit" name="more">More</button>
                </form>
            </div>
        </li>';
    }
    ?>
</ul>
<footer>
    &copy; Carbuy 2024
</footer>
</body>
</html>