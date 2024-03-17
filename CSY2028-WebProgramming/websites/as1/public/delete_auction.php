<?php
session_start();
require 'dbconnection.php';

// This checks the user is logged in or not 
if (!isset($_SESSION['user_id'])) {
    
    header("Location: login.php");
    exit;
}


if (isset($_POST['id'])) {
    $auction_id = $_POST['id'];
    $user_id = $_SESSION['user_id'];

    // delete the auction this is the SQL query 
    $deleteStatement = $Connection->prepare("DELETE FROM auctions WHERE id = :auction_id AND uploaded_by = :user_id");
    $deleteStatement->bindParam(':auction_id', $auction_id);
    $deleteStatement->bindParam(':user_id', $user_id);
    $deleteStatement->execute();

  
    header("Location: user_dashboard.php");
    exit;
} else {
  
    header("Location: user_dashboard.php");
    exit;
}
?>