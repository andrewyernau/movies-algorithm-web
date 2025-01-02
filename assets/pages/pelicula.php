<?php
require_once "../../auth/check_session.php";
require_once "../../includes/conectar.php";
require_once "../../includes/apiutils.php";
require_once "../../includes/common.php";

try {
    $pdo = conectar();
    $id_pelicula = isset($_GET["pelicula"]) ? (int) $_GET["pelicula"] : 1;
    $query = "SELECT title, date, `desc` FROM movie WHERE id = $id_pelicula";
    $result = $pdo->query($query);
    $pelicula = $result->fetch(PDO::FETCH_ASSOC);
    $titulo = htmlspecialchars($pelicula["title"]);
    $fecha = htmlspecialchars($pelicula["date"]);
    $descripcion = htmlspecialchars($pelicula["desc"]);
    $imagen = quitarParentesis(get_image_url($titulo));
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> - Descripción</title>
    <link rel="stylesheet" href="../css/pelicula.css">
    <link rel="stylesheet" href="../css/core.css">
</head>
<body>
    <div class="movie-container">
        <div class="movie-cover">
            <?php echo "<img src='$imagen' alt='Cover de la Película'>"; ?>
            <div class="movie-rating">⭐ 8.5/10</div>
        </div>
        <div class="movie-info">
            <h1 class="movie-title"><?php echo $titulo; ?></h1>
            <p class="movie-description"><?php echo $descripcion; ?></p>
            <div class="movie-genres">
                <span class="genre">Acción</span>
                <span class="genre">Aventura</span>
                <span class="genre">Drama</span>
            </div>
            <div class="movie-details">
                <p><strong>Director:</strong> Nombre del Director</p>
                <p><strong>Reparto:</strong> Actor 1, Actor 2, Actor 3</p>
                <p><strong>Duración:</strong> 2h 15min</p>
                <p><strong>Fecha de Estreno:</strong> <?php echo $fecha; ?></p>
            </div>
            <button class="rounded cs-button-solid">Ver Tráiler</button>
        </div>
    </div>
</body>
</html>
