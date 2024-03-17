<?php

session_start();

// Checks this if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, this redirects to the login page
    header("Location: login.php");
    exit;
}

require 'dbconnection.php';
?>
<?php

$categories = $Connection->query('SELECT * FROM Category_only')->fetchAll(PDO::FETCH_ASSOC);


$categoriesToShow = 7;
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="user_dashboard.css" />
    <style>
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }
    </style>
</head>
<body>
<header>
    <h1>
        <a href="https://as1.v.je/">
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
            <div class="dropdown">
                <span>User</span>
                <div class="dropdown-content">
                    <a href="user_dashboard.php">Dashboard</a>
                    <a href="addAuction.php">Add Auction</a>
                    <a href="logout.php">Logout</a>
                </div>
                <span><?php echo $_SESSION['user_id']; ?></span> <!--This  Displays user's email -->
            </div>
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
		
		 <!-- Silderrr Start -->

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

 <h1>Latest Car Listings</h1>
</div>

<ul class="carList">
    <?php
   
    $user_id = $_SESSION['user_id'];
    
    // Preparing and executing the SQL query to fetch the user's auctions
    $display = $Connection->prepare('SELECT * FROM auctions WHERE uploaded_by = :user_id');
    $display->bindParam(':user_id', $user_id);
    $display->execute();

    foreach ($display as $key) {
        
        $bidStatement = $Connection->prepare('SELECT MAX(amount) AS current_bid FROM auction_bids WHERE auction_id = :auction_id');
        $bidStatement->bindParam(':auction_id', $key['id']);
        $bidStatement->execute();
        $currentBid = $bidStatement->fetch(PDO::FETCH_ASSOC);
        $currentBidAmount = $currentBid['current_bid'];

       
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
                <div style="margin: 24px 0;">
                    <a href="#"><i class="fa fa-star"></i></a>
                    <a href="#"><i class="fa fa-star"></i></a>
                    <a href="#"><i class="fa fa-star"></i></a>
                    <a href="#"><i class="fa fa-star"></i></a>
                </div>

                <form action="auction.php" method="get">
                <input type="hidden" name="id" value="' . $key['id'] . '">
                <input type="hidden" name="category" value="' . $category['category_name'] . '">
                <button type="submit" name="more">More</button>
            </form>        
    <br>
                <form action="editAuction.php" method="get">
                    <input type="hidden" name="id" value="' . $key['id'] . '">
                    <button type="submit" name="edit">Edit Auction</button>
                </form>
                <br>
                <form action="delete_auction.php" method="post">
                    <input type="hidden" name="id" value="' . $key['id'] . '">
                    <button type="submit" name="delete">Delete Auction</button>
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