<?php
session_start();
require 'dbconnection.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit']) && isset($_SESSION['user_id'])) {
   

    $auctionId = $_POST['auction_id'];
    $reviewText = $_POST['reviewtext'];
    $userId = $_SESSION['user_id']; 

    // Thi will be inserting the review into the database
    $insertReviewStatement = $Connection->prepare('INSERT INTO reviews (user_id, reviewer_id, auction_id, review_text) VALUES (:user_id, :reviewer_id, :auction_id, :review_text)');
    $insertReviewStatement->bindParam(':user_id', $userId); 
    $insertReviewStatement->bindParam(':reviewer_id', $userId); 
    $insertReviewStatement->bindParam(':auction_id', $auctionId);
    $insertReviewStatement->bindParam(':review_text', $reviewText);

    if ($insertReviewStatement->execute()) {
       
        
        header("Location: auction.php?id=$auctionId");
        exit;
    } else {
        // It indicates an error occurred while inserting data
        echo '<p>Error: Failed to add the review. Please try again.</p>';
    }
} else {
    
    header("Location: error.php");
    exit;
}
?>