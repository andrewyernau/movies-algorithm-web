<?php
require_once "../../includes/conectar.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $texto = $_POST['texto'];
    $user_id = (int)$_POST['user_id'];
    $movie_id = (int)$_POST['movie_id'];

    $pdo = conectar();
    $texto = htmlspecialchars($texto);
    $query = "INSERT INTO moviecomments (user_id, movie_id, comment) VALUES ($user_id, $movie_id, '$texto')";
    $result = $pdo->query($query);
    header("Location: ../pages/pelicula.php?id=$movie_id");
} else {
    echo "No se ha recibido ningún dato.";
}
?>
