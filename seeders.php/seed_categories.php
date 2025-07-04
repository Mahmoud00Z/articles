<?php 
require("../connection/connection.php");

$categories = ['World news', 'Sports', 'Technology', 'Arts and culture', 'E-sports'];
$stmt = $mysqli->prepare("INSERT INTO categories (name) VALUES (?)");

foreach($categories as $name) {
    $stmt->bind_param("s", $name);
    $stmt->execute();
}

