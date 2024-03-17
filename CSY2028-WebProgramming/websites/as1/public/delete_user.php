<?php

session_start();


require 'dbconnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['user_id'])) {
        $userId = $_POST['user_id'];
        
        // Deleting user from the database
        $deleteUserStatement = $Connection->prepare("DELETE FROM register WHERE id = :id");
        $deleteUserStatement->bindParam(':id', $userId);
        $deleteUserStatement->execute();
        
        // Deleting user's auctions from the database
        $deleteAuctionsStatement = $Connection->prepare("DELETE FROM auctions WHERE uploaded_by = :user_id");
        $deleteAuctionsStatement->bindParam(':user_id', $userId);
        $deleteAuctionsStatement->execute();


     
        header("Location: admin_dashboard.php");
        exit;
    }
}
?>
