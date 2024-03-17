<?php
session_start();

// in this code i've Redirected to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'dbconnection.php';

if(isset($_POST['add'])){
    
    $target_directory = "uploads/";

    
    if (!file_exists($target_directory)) {
        mkdir($target_directory, 0777, true); 
    }

    $target_file = $target_directory . basename($_FILES["file"]["name"]);
    move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);

    
    $endDate = date('Y-m-d', strtotime('+7 days'));

    // I am Inserting into database
    $auctionStatement = $Connection->prepare('INSERT INTO auctions (car_name, category_id, description, image_path, uploaded_by, End_date)
    VALUES(:name, :category_id, :description, :image_path, :uploaded_by, :end_date)');

    $auctionParameters = [
        ':name' => $_POST['title'],
        ':category_id' => $_POST['category'],
        ':description' => $_POST['description'],
        ':image_path' => $target_file,
        ':uploaded_by' => $_SESSION['user_id'], 
        ':end_date' => $endDate 
    ];

    $auctionStatement->execute($auctionParameters);

    
    $auctionId = $Connection->lastInsertId();

    $bidAmount = $_POST['bidAmount'];
    $bidStatement = $Connection->prepare('INSERT INTO auction_bids (user_id, amount, auction_id) VALUES (:user_id, :amount, :auction_id)');
    $bidStatement->bindParam(':user_id', $_SESSION['user_id']);
    $bidStatement->bindParam(':amount', $bidAmount);
    $bidStatement->bindParam(':auction_id', $auctionId);
    $bidStatement->execute();

    // Redirecting to the  user dashboard
    header("Location: user_dashboard.php");
    exit();
}
?>
<?php
// I am Fetching categories from the database
$categories = $Connection->query('SELECT * FROM Category_only')->fetchAll(PDO::FETCH_ASSOC);


$categoriesToShow = 7;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions</title>
    <link rel="stylesheet" href="carbuy.css" />

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
    <h1> <a href = "https://as1.v.je/">
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
                    <a href="admin_dashboard.php">Add Category</a>
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
        // Loop through the categories and I generate HTML for each category link
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

<main>
    <form action="#" method="post" enctype="multipart/form-data">
        <label for="titleid">Title</label>
        <input type="text" name="title" id="titleid"><br>
        <label for="description">Description</label>
        <input type="text" name="description" id="description"><br>
        <select name="category" id="category">
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>"><?php echo $category['category_name']; ?></option>
            <?php endforeach; ?>
        </select><br>
        <label for="image">Choose image:</label>
        <input type="file" name="file" id="image"><br>
        <!-- End Date -->
        <label for="endDate">Auction End Date</label>
        <input type="date" name="endDate" id="endDate" value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>"><br>
        <!-- Place Bid section -->
        <label for="bidAmount">Bid Amount</label>
        <input type="text" name="bidAmount" id="bidAmount" value="0"><br>
        <input type="submit" name="add" value="Add">
    </form>
</main>
</body>
</html>