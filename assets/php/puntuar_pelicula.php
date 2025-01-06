<?php
require_once "../../includes/conectar.php";

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $puntuacion = $_POST["puntuacion"];
    $user_id = $_POST["user_id"];
    $movie_id = $_POST["movie_id"];

    $timestamp = date("Y-m-d H:i:s");

    $pdo = conectar();

    $query = "INSERT INTO user_score (id_user, id_movie, score, time) VALUES ($user_id, $movie_id, $puntuacion, '$timestamp')";
    $pdo->query($query);
    header("Location: ../pages/pelicula.php?pelicula=$movie_id");
    exit();
}
else{
    header("Location: ../pages/main.php");
    exit();
}
?>
