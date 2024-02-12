<?php
    header("location: blogMenu.php");
    error_reporting(E_ALL);
    $database = new mysqli("localhost", "root", "", "blogs");

    $id = $_POST['id'];
    
    $query = "DELETE FROM `tags` WHERE blog_id=?";
    $sqlCode = $database->prepare($query);
    $sqlCode->bind_param("i", $id);
    $sqlCode->execute();

    if (isset($_POST['tags'])) {
        $tags = $_POST['tags'];
        foreach ($tags as $tag) {
            $query = "INSERT INTO `tags` (`blog_id`, `tag`) VALUES (?, ?);";
            $sqlCode = $database->prepare($query);
            $sqlCode->bind_param("is", $id, $tag);
            $sqlCode->execute();
        }
    }
?>