<?php
session_start();
require 'dbconnection.php';

// Checking if a category is selected
if (isset($_GET['category'])) {
    $selected_category = $_GET['category'];
    $display = $Connection->prepare('SELECT auctions.*, Category_only.category_name 
                                     FROM auctions 
                                     INNER JOIN Category_only 
                                     ON auctions.category_id = Category_only.id 
                                     WHERE Category_only.category_name = :category_name');
    $display->bindParam(':category_name', $selected_category);    
} else {
    
    $display = $Connection->prepare('SELECT auctions.*, Category_only.category_name 
                                     FROM auctions 
                                     INNER JOIN Category_only 
                                     ON auctions.category_id = Category_only.id');
}

$display->execute();
?>
<?php

$categories = $Connection->query('SELECT * FROM Category_only')->fetchAll(PDO::FETCH_ASSOC);


$categoriesToShow = 7;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions</title>
    <link rel="stylesheet" href="category.css" />
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


<!-- Slider Start -->
<div class="slider">
    <div class="slideshow">
        <div id="change-1" class="change">
            <a href="#change-3"></a>
            <a href="#change-2"></a>
            <img src="banners/1.jpg" alt="Banner" />
        </div>
        <div id="change-2" class="change">
            <a href="#change-1"></a>
            <a href="#change-3"></a>
            <img src="banners/2.jpg" alt="Banner" />
        </div>
        <div id="change-3" class="change">
            <a href="#change-2"></a>
            <a href="#change-1"></a>
            <img src="banners/3.jpg" alt="Banner" />
        </div>
        <div class="dot">
            <span class="mark"> </span> 
            <span class="mark"> </span> 
            <span class="mark"> </span> 
        </div>
    </div>
</div>
<!-- Slider Stop -->

<div class="car listing title">
    <h1>Particular Category Car Listing</h1>
</div>

<ul class="carList">
    <?php
    foreach ($display as $key) {
      
        $bidStatement = $Connection->prepare('SELECT MAX(amount) AS current_bid FROM auction_bids WHERE auction_id = :auction_id');
        $bidStatement->bindParam(':auction_id', $key['id']);
        $bidStatement->execute();
        $currentBid = $bidStatement->fetch(PDO::FETCH_ASSOC);
        $currentBidAmount = $currentBid['current_bid'];

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
                <h3 class="category">' . $key['category_name'] . '</h3> <!-- Display the category name -->
                <p>' . $key['description'] . '</p>
                <div style="margin: 24px 0;">
                    <a href="#"><i class="fa fa-star"></i></a>
                    <a href="#"><i class="fa fa-star"></i></a>
                    <a href="#"><i class="fa fa-star"></i></a>
                    <a href="#"><i class="fa fa-star"></i></a>
                </div>
                <form action="auction.php" method="get">
                    <input type="hidden" name="id" value="' . $key['id'] . '">
                    <input type="hidden" name="category" value="' . $selected_category . '">
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