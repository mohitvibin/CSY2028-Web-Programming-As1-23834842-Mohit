<?php
session_start();
require 'dbconnection.php';


$auctionId = isset($_GET['id']) ? $_GET['id'] : 0;

$statement = $Connection->prepare('SELECT * FROM auctions WHERE id = :id');
$statement->bindParam(':id', $auctionId);
$statement->execute();
$selectedAuction = $statement->fetch(PDO::FETCH_ASSOC);


$bidStatement = $Connection->prepare('SELECT MAX(amount) AS current_bid FROM auction_bids WHERE auction_id = :auction_id');
$bidStatement->bindParam(':auction_id', $auctionId);
$bidStatement->execute();
$currentBid = $bidStatement->fetch(PDO::FETCH_ASSOC);
$currentBidAmount = $currentBid['current_bid'];


$publisherUsername = '';
$auctionUserId = 0;
if ($selectedAuction) {
    $userStatement = $Connection->prepare('SELECT Username, id FROM register WHERE id = :user_id');
    $userStatement->bindParam(':user_id', $selectedAuction['uploaded_by']);
    $userStatement->execute();
    $userResult = $userStatement->fetch(PDO::FETCH_ASSOC);
    $publisherUsername = $userResult ? $userResult['Username'] : '';
    $auctionUserId = $userResult ? $userResult['id'] : 0;
}


$categories = $Connection->query('SELECT * FROM Category_only')->fetchAll(PDO::FETCH_ASSOC);

//  Defining the number of categories to be display in the main navbar
$categoriesToShow = 7;

// I am Handling bid placement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['placeBid'])) {
    $bidAmount = isset($_POST['bidAmount']) ? floatval($_POST['bidAmount']) : 0;

    if ($bidAmount > $currentBidAmount) {
        
        $existingBidStatement = $Connection->prepare('SELECT auction_id FROM auction_bids WHERE auction_id = :auction_id');
        $existingBidStatement->bindParam(':auction_id', $auctionId);
        $existingBidStatement->execute();
        $existingBid = $existingBidStatement->fetch(PDO::FETCH_ASSOC);

        if ($existingBid) {
            //  existing bid amount Updating 
            $updateBidStatement = $Connection->prepare('UPDATE auction_bids SET amount = :bid_amount WHERE auction_id = :auction_id');
            $updateBidStatement->bindParam(':bid_amount', $bidAmount);
            $updateBidStatement->bindParam(':auction_id', $auctionId);
            if ($updateBidStatement->execute()) {
                $currentBidAmount = $bidAmount;
            }
        } else {
            // I have Inserted a new bid 
            $insertBidStatement = $Connection->prepare('INSERT INTO auction_bids (user_id, amount, auction_id) VALUES (:user_id, :amount, :auction_id)');
            $insertBidStatement->bindParam(':user_id', $_SESSION['user_id']);
            $insertBidStatement->bindParam(':amount', $bidAmount);
            $insertBidStatement->bindParam(':auction_id', $auctionId);
            if ($insertBidStatement->execute()) {
                $currentBidAmount = $bidAmount;
            }
        }
    } else {
// I have Showed the bid amount is not higher than the current bid
        echo '<p>Bid amount must be higher than the current bid.</p>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auction</title>
    <link rel="stylesheet" href="auction.css" />
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
<!-- This will be the auction page left section with title, category,image, discription and auction published by  -->
<div class="container">
    <div class="left-section">
        <?php
        if ($selectedAuction) {
            echo '<h1>' . $selectedAuction['car_name'] . '</h1>';
            
            
            $categoryStatement = $Connection->prepare('SELECT category_name FROM Category_only WHERE id = :category_id');
            $categoryStatement->bindParam(':category_id', $selectedAuction['category_id']);
            $categoryStatement->execute();
            $categoryResult = $categoryStatement->fetch(PDO::FETCH_ASSOC);

            if ($categoryResult) {
                echo '<h3 class="category">' . $categoryResult['category_name'] . '</h3>';
            } else {
                echo '<p>Category not available</p>';
            }

            echo '<img src="' . $selectedAuction['image_path'] . '" alt="' .'">';
            echo '<p>' . $selectedAuction['description'] . '</p>';

            
            echo '<p>Auction Published by: ' . $publisherUsername . '</p>';
        ?>
    </div>
    <div class="right-section">
        <!-- This is the right section content (Current bid, Bidding Date End, Place Bid Amount, and Reivew -->
        <?php
        if ($currentBidAmount !== null) {
            echo '<p class="price">Current bid: £' . number_format($currentBidAmount, 2) . '</p>';
        } else {
            echo '<p class="price">No bids yet</p>';
        }
            echo '<p>Bidding End Date: ' . $selectedAuction['End_date'] . '</p>';
        } else {
            echo '<p>Auction not found</p>';
        }
        ?>



        <form action="" method="post">
            <label for="bidAmount">Bid Amount:</label>
            <input type="text" id="bidAmount" name="bidAmount" required>
            <button type="submit" name="placeBid">Place Bid</button>
        </form>


        

        <section class="reviews">
            <h2>Reviews of <?php echo $publisherUsername; ?></h2>
            <ul>
                <?php
                $reviewStatement = $Connection->prepare('SELECT r.review_text, r.review_date, u.Username FROM reviews r INNER JOIN register u ON r.reviewer_id = u.id WHERE r.auction_id = :auction_id');
                $reviewStatement->bindParam(':auction_id', $auctionId);
                $reviewStatement->execute();
                $reviews = $reviewStatement->fetchAll(PDO::FETCH_ASSOC);

                if ($reviews) {
                    foreach ($reviews as $review) {
                        echo '<li><strong>' . $review['Username'] . ' said </strong>' . $review['review_text'] . ' <em>' . $review['review_date'] . '</em></li>';
                    }
                } else {
                    echo '<li>No reviews yet</li>';
                }
                ?>
            </ul>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $auctionUserId && !hasReviewedAuction($_SESSION['user_id'], $auctionId, $Connection)): ?>
                <form action="" method="post">
                    <input type="hidden" name="auction_id" value="<?php echo $auctionId; ?>">
                    <textarea name="reviewtext" required></textarea>
                    <input type="submit" name="submit" value="Add Review" />
                </form>
            <?php elseif (!isset($_SESSION['user_id'])): ?>
                <p>You are not logged in. <a href="login.php">Log in</a> to write a review.</p>
            <?php endif; ?>

            <?php
            // I've added a Function to check if the user has already reviewed the auction or not
            function hasReviewedAuction($userId, $auctionId, $connection) {
                $reviewCheckStatement = $connection->prepare('SELECT COUNT(*) AS review_count FROM reviews WHERE reviewer_id = :user_id AND auction_id = :auction_id');
                $reviewCheckStatement->bindParam(':user_id', $userId);
                $reviewCheckStatement->bindParam(':auction_id', $auctionId);
                $reviewCheckStatement->execute();
                $reviewCount = $reviewCheckStatement->fetchColumn();
                return ($reviewCount > 0);
            }
            ?>
        </section>
    </div>
</div>
<footer>
    &copy; Carbuy 2024
</footer>
</body>
</html>