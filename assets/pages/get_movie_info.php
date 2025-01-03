<?php
require_once "../../includes/common.php";

$title = $_GET["title"] ?? "";
if ($title) {
    $movie_data = get_movie_data($title);
    echo json_encode($movie_data);
}
?>
