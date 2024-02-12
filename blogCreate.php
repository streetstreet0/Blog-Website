<?php
    $id = $_POST['id'];
    header("location: blogMenu.php?mode=view&article=$id");
    error_reporting(E_ALL);
    $database = new mysqli("localhost", "root", "", "blogs");

    $title = $_POST['title'];
    # fix the title so html code can't be inserted
    $title = htmlspecialchars($title);
    # fix an empty title (temporary fix until I add javascript)
    if ($title == '') {
        $title = "article $id";
    }

    $body = $_POST['body'];
    # fix the body so html code can't be inserted
    $body = htmlspecialchars($body);
    $body = str_replace("\n", "<br>", "$body");
    
    $date = $_POST['date'];
    if ($date == '') {
        $dateArray = getdate();
        $date = "{$dateArray['year']}-{$dateArray['mon']}-{$dateArray['mday']}";
    }

    $query = "INSERT INTO `blogs` (title, body, `date`, id) VALUES (?, ?, ?, ?)";
    $sqlCode = $database->prepare($query);
    $sqlCode->bind_param("sssi", $title, $body, $date, $id);
    if ($sqlCode->execute()) {
        echo "<p>Blog Created Successfully</p>";
    }
    else {
        echo "<p>An Error Has Occured</p>";
    }
?>