<?php
    header("location: blogMenu.php");
    error_reporting(E_ALL);
    $database = new mysqli("localhost", "root", "", "blogs");

    $id = $_POST['id'];
    
    $query = "DELETE FROM `blogs` WHERE id=?";
    $sqlCode = $database->prepare($query);
    $sqlCode->bind_param("i", $id);
    $sqlCode->execute();

    $query = "DELETE FROM `tags` WHERE blog_id=?";
    $sqlCode = $database->prepare($query);
    $sqlCode->bind_param("i", $id);
    $sqlCode->execute();
?>