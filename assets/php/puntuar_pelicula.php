<?php
require_once "../../includes/conectar.php";

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $puntuacion = $_POST["puntuacion"];
    $user_id = $_POST["user_id"];
    $movie_id = $_POST["movie_id"];

    $timestamp = date("Y-m-d H:i:s");

    $pdo = conectar();

    $query = "SELECT id_movie FROM user_score WHERE id_user = $user_id";
    $result = $pdo->query($query);
    $movies = $result->fetchAll(PDO::FETCH_ASSOC);
    foreach ($movies as $movie){
        if ($movie["id_movie"] == $movie_id){
            $update_movie = TRUE;
        }
        else{
            $update_movie = FALSE;
        }
    }

    if ($update_movie){
        $query = "UPDATE user_score SET score = $puntuacion, time = '$timestamp' WHERE id_user = $user_id AND id_movie = $movie_id";
        $pdo->query($query);
        header("Location: ../pages/pelicula.php?pelicula=$movie_id");
        exit();
    }
    else{

        $query = "INSERT INTO user_score (id_user, id_movie, score, time) VALUES ($user_id, $movie_id, $puntuacion, '$timestamp')";
        $pdo->query($query);
        header("Location: ../pages/pelicula.php?pelicula=$movie_id");
        exit();
    }
}
else{
    header("Location: ../pages/main.php");
    exit();
}
?>
