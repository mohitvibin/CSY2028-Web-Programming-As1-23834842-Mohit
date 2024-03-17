<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'dbconnection.php';

// Checking if auction ID is provided
if(isset($_GET['id'])) {
    $auction_id = $_GET['id'];
    
    
    $statement = $Connection->prepare('SELECT a.*, c.category_name FROM auctions a JOIN Category_only c ON a.category_id = c.id WHERE a.id = :id AND a.uploaded_by = :uploaded_by');
    $statement->bindParam(':id', $auction_id);
    $statement->bindParam(':uploaded_by', $_SESSION['user_id']);
    $statement->execute();
    $auction = $statement->fetch(PDO::FETCH_ASSOC);
} else {
    // If auction ID is not provided, this redirect to user dashboard to user
    header("Location: user_dashboard.php");
    exit();
}

// This is for Checking if form is submitted for editing
if(isset($_POST['edit'])) {
    
    $target_directory = "uploads/";

    
    if (!file_exists($target_directory)) {
        mkdir($target_directory, 0777, true); 
    }

    // This Checks if a new image has been uploaded or not
    if ($_FILES["file"]["size"] > 0) {
        $target_file = $target_directory . basename($_FILES["file"]["name"]);
        move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
    } else {
        
        $target_file = $auction['image_path'];
    }

    // Updating auction in the database
    $updateStatement = $Connection->prepare('UPDATE auctions SET car_name = :name, category_id = :category_id, description = :description, image_path = :image_path, End_date = :end_date WHERE id = :auction_id');
    $updateStatement->bindParam(':name', $_POST['title']);
    $updateStatement->bindParam(':category_id', $_POST['category']);
    $updateStatement->bindParam(':description', $_POST['description']);
    $updateStatement->bindParam(':image_path', $target_file);
    $updateStatement->bindParam(':end_date', $_POST['endDate']);
    $updateStatement->bindParam(':auction_id', $auction_id);
    $updateStatement->execute();

    
    header("Location: user_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions</title>
    <link rel="stylesheet" href="carbuy.css" />
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
            <div class="dropdown">
                <span>User</span>
                <div class="dropdown-content">
                    <a href="user_dashboard.php">Dashboard</a>
                    <a href="addAuction.php">Add Auction</a>
                    <a href="logout.php">Logout</a>
                </div>
                <span><?php echo $_SESSION['user_id']; ?></span> <!-- Display user's email -->
            </div>
        </div>
    </form>
</header>

<nav>
    <ul>
        <?php
        
        $categories = $Connection->query('SELECT * FROM Category_only')->fetchAll(PDO::FETCH_ASSOC);

       
        $categoriesToShow = 7;

        
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
    <h2>Edit Auction</h2>
    <form action="#" method="post" enctype="multipart/form-data">
        <label for="titleid">Title</label>
        <input type="text" name="title" id="titleid" value="<?php echo $auction['car_name']; ?>"><br>
        <label for="description">Description</label>
        <input type="text" name="description" id="description" value="<?php echo $auction['description']; ?>"><br>
        <select name="category" id="categoryd">
            <?php
            
            foreach ($categories as $category) {
                $selected = ($auction['category_id'] == $category['id']) ? 'selected' : '';
                echo '<option value="' . $category['id'] . '" ' . $selected . '>' . $category['category_name'] . '</option>';
            }
            ?>
        </select><br>
        <!-- This is the End Date -->
        <label for="endDate">Auction End Date</label>
        <input type="date" name="endDate" id="endDate" value="<?php echo date('Y-m-d', strtotime($auction['End_date'])); ?>"><br>
        <label for="image">Choose image:</label>
        <input type="file" name="file" id="image">
        <label for="bidAmount">Bid Amount</label>
        <input type="text" name="bidAmount" id="bidAmount" value="0"><br>
        <input type="submit" name="edit" value="Edit">
    </form>
</main>
</body>
</html>
