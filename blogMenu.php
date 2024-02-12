<!DOCTYPE HTML>
<!-- I hate switching between html and php in code, it makes the code look messy-->
<?php
    error_reporting(E_ALL);

	$mode = 'select';
	if (isset($_REQUEST['mode'])) {
		$mode = $_REQUEST['mode'];
	}

    $article = "0";
    if (isset($_REQUEST['article'])) {
		$article = $_REQUEST['article'];
	}

    $sort = "title";
    if (isset($_REQUEST['sort'])) {
		$sort = $_REQUEST['sort'];
	}

    $filter = "0";
    if (isset($_REQUEST['filter'])) {
		$filter = $_REQUEST['filter'];
	}

    function includeinfilter($filterTags, $tagsResult) {
        $tagsString = gettagsfromresult($tagsResult);
        $tags = array();
        if (is_null($tagsString)) {
            return false;
        }
        else {
            $tags = explode(",", $tagsString);
        }
        foreach ($tags as $tag) {
            foreach ($filterTags as $filterTag) {
                if ($tag == $filterTag) {
                    return true;
                }
            }
        }
        return false;
    }

    function arrayIncludesTag($array, $tag) {
        foreach($array as $arrayRow) {
            if ($arrayRow['tag'] == $tag) {
                return true;
            }
        }
        return false;
    }

    function gettagsfromresult($result) {
        foreach($result as $row) {
            return $row['Tags'];
        }
    }
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Assignment Blogs</title>
		<link href="style.css" rel="stylesheet" type="text/css">
        <link rel="icon" type="image/x-icon" href="/images/icon.gif">
	</head>

    <body>
        <ul id="page_menu">
            <li><a href="?mode=select">Select Article</a></li>
            <li><a href="?mode=add">Add New Article</a></li>
            <li><a href="?mode=tags">Update Tags</a></li>
            <li><a href="?mode=delete">Delete Article</a></li>
            <li><a href="index.php">Return to Homepage</a></li>
        </ul>

        <?php
            $database = new mysqli("localhost", "root", "", "blogs");
            $result = $database->query("SELECT * FROM `blogs`;");
            
            // combine the sorting if statements into one statement at the start
            if ($mode == 'select') {
                if ($sort == "date") {
                    $titleSelectedText = "";
                    $dateSelectedText = "selected ";
                    $sortString = " ORDER BY `blogs`.date;";
                }
                else {
                    $sortString = " ORDER BY `blogs`.title;";
                    $titleSelectedText = "selected ";
                    $dateSelectedText = "";
                }

                // deal with side menu filters first
                echo "<table class=\"sideMenu\"><tr><td><select id=\"sortByDropdown\" name=\"sortBy\">";
            
                echo "<option " . $titleSelectedText . "value=\"title\">Sort by Title</option>";
                echo "<option " . $dateSelectedText . "value=\"date\">Sort by Date</option>";
                echo "</select></td><td></td></tr>";
                $result = $database->query("SELECT DISTINCT tag AS tag FROM `tags` ORDER BY tag;");
                $tagNum = 0;
                $filterTags = array();
                foreach ($result as $row) {
                    // filter is a string of 0's and 1's that correspond to tags being selected
                    // if the character is a 1 the tag is selected, if the character is a 0 it is not
                    // if the filter is too short we presume the tag is selected
                    if (strlen($filter) <= $tagNum || $filter[$tagNum] == 0) {
                        $checkedText = "";
                    }
                    else {
                        $checkedText = " checked";
                        $filterTagsSize = sizeof($filterTags);
                        $filterTags["$filterTagsSize"] = $row["tag"];
                    }
                    echo "<tr><td>{$row['tag']}</td><td><input type=\"checkbox\" class=\"filterCheckbox\" id=\"$tagNum\" name=\"{$row['tag']}\"$checkedText></td></tr>";
                    $tagNum++;
                }
                echo "</table>";

                // do the selection table next
                $result = $database->query("SELECT `blogs`.id AS id, `blogs`.title AS Title, `blogs`.date AS `Date`FROM `blogs`" . $sortString);
                echo "<table>";
                echo "<tr><td><strong>Article</strong></td> <td><strong>Date</strong></td> <td><strong>Tags</strong></td></tr>";
                foreach($result as $row) {
                    $tagsQuery = $database->prepare("SELECT GROUP_CONCAT(`tags`.tag) AS Tags FROM `tags` WHERE `tags`.blog_id=? GROUP BY `tags`.blog_id;");
                    $tagsQuery->bind_param("i", $row['id']);
                    $tagsQuery->execute();
                    $tagsResult = $tagsQuery->get_result();
                    if (sizeof($filterTags) == 0 || includeinfilter($filterTags, $tagsResult)) {
                        echo "<tr><td><a href=\"?mode=view&article={$row['id']}\">{$row['Title']}</a></td>";
                        echo "<td>{$row['Date']}</td>";
                        echo "<td>";
                        foreach ($tagsResult as $tagsRow) {
                            echo "{$tagsRow['Tags']}";
                        }
                        echo "</td></tr>";
                    }
                }
                echo "</table>";
            }
            elseif ($mode == "edit") {
                $request = $database->prepare("SELECT title AS Title, `date` AS `Date`, body AS Body FROM `blogs` WHERE id=?;");
                $request->bind_param("i", $article);
                $request->execute();
                $result = $request->get_result();
                foreach ($result as $row) {
                    $title = htmlspecialchars_decode($row['Title']);
                    $body = str_replace("<br>", "\n", "{$row['Body']}");
                    $body = htmlspecialchars_decode($body);
                    echo "<form id=\"editBlog\" action=\"blogUpdate.php\" method=\"post\" class=\"centreBox\"><table class=\"formTable\">";
                    echo "<tr><td><input id=\"editBlogTitle\" name=\"title\" type=\"text\" value=\"$title\"/>";
                    echo "&nbsp;<input name=\"date\" type=\"date\" value=\"{$row['Date']}\"/></td></tr>";
                    echo "<tr><td><textarea name=\"body\">$body</textarea></td></tr>";
                    echo "<tr><td><input type=\"submit\"/ value=\"Save Changes\"></td></tr>";
                    echo "</table>";
                    echo "<input id=\"editBlogId\" type=\"hidden\" name=\"id\" value=\"{$article}\"/>";
                    echo "</form>";
                }
            }
            elseif ($mode == "view") {
                echo "<div class=\"centreBox\">";
                $request = $database->prepare("SELECT title AS Title, `date` AS `Date`, body AS Body FROM `blogs` WHERE id=?;");
                $request->bind_param("i", $article);
                $request->execute();
                $result = $request->get_result();
                foreach ($result as $row) {
                    echo "<h1>{$row['Title']} {$row['Date']}</h1>";
                    echo "<p>{$row['Body']}</p>";
                }
                echo "</div>";

                echo "<a href=\"?mode=edit&article=$article\" class=\"sideMenu\">Edit Article</a>";
            }
            elseif ($mode == "add") {
                $result = $database->query("SELECT MAX(id) AS Id FROM `blogs`;");
                foreach ($result as $row) {
                    $id = $row['Id'] + 1;
                    $date = getdate();
                    $date = "{$date['year']}-{$date['mon']}-{$date['mday']}";
                    echo "<form id=\"addBlog\" action=\"blogCreate.php\" method=\"post\" class=\"centreBox\"><table class=\"formTable\">";
                    echo "<tr><td><input id=\"addBlogTitle\" name=\"title\" type=\"text\" placeholder=\"Article Name\"/>";
                    echo "&nbsp;<input name=\"date\" type=\"date\" value=\"$date\"/></td></tr>";
                    echo "<tr><td><textarea name=\"body\" placeholder=\"Body Text\"></textarea></td></tr>";
                    echo "<tr><td><input type=\"submit\"/ value=\"Add Article\"></td></tr>";
                    echo "</table>";
                    echo "<input id=\"addBlogId\" type=\"hidden\" name=\"id\" value=\"$id\"/>";
                    echo "</form>";
                }
            }
            elseif ($mode == "delete") {
                echo "<form id=\"deleteBlog\" action=\"blogDelete.php\" method=\"post\" class=\"centreBox\"><table class=\"formTable\">";
                echo "<select id=\"deleteBlogDropdown\" name=\"id\">";
		        $result = $database->query("SELECT * FROM blogs;");
	        	foreach ($result as $row) {
                    echo "<option value=\"{$row['id']}\">{$row['title']}</option>";
                }
                echo "</select>";
                echo "<input type=\"submit\" value=\"Delete Article\"></form>";
            }
            elseif ($mode == "tags") {
                echo "<form id=\"editTags\" action=\"editTags.php\" method=\"post\" class=\"centreBox\"><table class=\"formTable\" id=\"editTagsTable\">";
                echo "<tr><td colspan=\"2\"><select id=\"editTagsBlogSelection\" name=\"id\">";
		        $result = $database->query("SELECT * FROM blogs;");
	        	foreach ($result as $row) {
                    if ($article == $row['id']) {
                        $selectedText = " selected";
                    }
                    else {
                        $selectedText = "";
                    }
                    echo "<option value=\"{$row['id']}\"" . $selectedText . ">{$row['title']}</option>";
                }
                echo "</select></td></tr>";

                echo "<tr><td colspan=\"2\"><input type=\"submit\" value=\"Update Tags\"></td></tr>";

                $result = $database->query("SELECT DISTINCT `tag` AS `tag` FROM tags ORDER BY `tag`;");
                $hasTagQuery = $database->prepare("SELECT `tag` AS `tag` FROM tags WHERE `blog_id`=?;");
                $hasTagQuery->bind_param("i", $article);
                $hasTagQuery->execute();
                $hasTagResult = $hasTagQuery->get_result();
	        	foreach ($result as $row) {
                    echo "<tr><td>{$row['tag']}</td><td>";
                    if (arrayIncludesTag($hasTagResult ,$row['tag'])) {
                        $checkedText = " checked";
                    }
                    else {
                        $checkedText = "";
                    }
                    echo "<input type=\"checkbox\"" . $checkedText . " value=\"{$row['tag']}\" class=\"tagCheckbox\" id=\"{$row['tag']}\" name=\"tags[]\">";
                    echo "</td></tr>";
                }
                echo "</table></form>";
                

                // echo "<form id=\"newTag\"class=\"sideMenu\"><table>";
                echo "<table class=\"sideMenu\">";
                echo "<tr><td><input type=\"text\" id=\"newTagName\" prompt=\"Create New Tag\"/></td>";
                echo "<td><input type=\"submit\" id=\"newTagButton\" value=\"Add New Tag\"/></td></tr>";
                echo "</table>";
                // echo "</table></form>";
            }
            else {
                echo "<div class=\"centreBox\"><h1>An Error has occured</h1></div>";
            }
        ?>
        <script type="module" src="functionality.js"></script>
    </body>
</html>