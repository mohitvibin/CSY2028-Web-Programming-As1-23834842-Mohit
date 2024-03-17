<?php
session_start();
require 'dbconnection.php';

// this will be checking if the user is logged in 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// this will be checking if category ID is provided
if (isset($_GET['id'])) {
    $category_id = $_GET['id'];

    // this will be deleting category from the database
    $delete_category = $Connection->prepare('DELETE FROM Category_only WHERE id = :id');
    $delete_category->bindParam(':id', $category_id);
    $delete_category->execute();

    
    header("Location: admin_dashboard.php");
    exit();
} else {

    header("Location: admin_dashboard.php");
    exit();
}
?>
