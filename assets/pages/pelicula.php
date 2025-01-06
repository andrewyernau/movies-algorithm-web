<?php
require_once "../../auth/check_session.php";
require_once "../../includes/conectar.php";
require_once "../../includes/common.php";

session_start();

try {
    $pdo = conectar();
    $user_id = (int) $_COOKIE["user_id"];

    $query = "SELECT name, pic FROM users WHERE id = $user_id";
    $result = $pdo->query($query);
    $user = $result->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        setcookie("user_id", "", time() - 3600, "/");
        header("Location: ../../index.html");
        exit();
    }

    $username = htmlspecialchars($user["name"]);
    $userpic = !empty($user["pic"]) ? htmlspecialchars($user["pic"]) : "../images/userdefault.png";
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit();
}

try {
    $pdo = conectar();
    $id_pelicula = isset($_GET["pelicula"]) ? (int) $_GET["pelicula"] : 1;
    $query = "SELECT title, date, `desc` FROM movie WHERE id = $id_pelicula";
    $result = $pdo->query($query);
    $pelicula = $result->fetch(PDO::FETCH_ASSOC);
    $titulo = htmlspecialchars($pelicula["title"]);
    $fecha = htmlspecialchars($pelicula["date"]);
    $descripcion = htmlspecialchars($pelicula["desc"]);
    $movie_data = get_movie_data(quitarParentesis($titulo));
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit();
}

// comprobar si ya se había puntuado la película
$query = "SELECT id_movie, score FROM user_score WHERE id_user = $user_id";
$result = $pdo->query($query);
$movies = $result->fetchAll(PDO::FETCH_ASSOC);
$puntuado = false;
$rating = null;

foreach ($movies as $movie) {
    if ($movie["id_movie"] == $id_pelicula) {
        $puntuado = true;
        $rating = $movie["score"]; // Corregir el error tipográfico
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> - Descripción</title>
    <link rel="icon" type="image/x-icon" href="/assets/images/ico.ico">
    <link href='https://fonts.googleapis.com/css?family=DM Sans' rel='stylesheet'>
    <style>
        body {
            font-family: 'DM Sans';
        }
    </style>
    <link rel="stylesheet" href="../css/pelicula.css">
    <link rel="stylesheet" href="../css/core.css">
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="main.php" style="font-weight: bold; font-size: 1.5em;">Inicio</a></li>
                <li><a href="./catalog.php">Catálogo</a></li>
            </ul>
        </nav>
        <div class="user-info">
            <img src="<?php echo $userpic; ?>" alt="User Avatar">
            <span><?php echo $username; ?></span>
            <div class="dropdown-menu">
                <a href="profile.php"> Mi perfil </a>
                <a href="../../auth/logout.php">Cerrar sesión</a>
            </div>
        </div>
    </header>

    <main>
        <div class="movie-container">
            <div class="movie-cover">
                <img src="<?php echo htmlspecialchars(
                    $movie_data["cover"]
                ); ?>" alt="Cover de la Película">
                <div class="movie-rating">⭐
                    <?php echo get_movie_rating($id_pelicula); ?> / 5
                <?php echo "</br>";
                    echo get_movie_rating_count($id_pelicula); ?> valoraciones
                </div>
                <div class="movie-rating-count"></div>
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
                    <p><strong>Duración:</strong> <?php
                    $horas = floor($movie_data["runtime"] / 60);
                    $minutos = $movie_data["runtime"] % 60;
                    echo htmlspecialchars("{$horas}h {$minutos}min");
                    ?></p>
                    <p><strong>Fecha de Estreno:</strong> <?php echo $fecha; ?></p>
                    <p><strong>Reparto:</strong> <?php echo htmlspecialchars(
                        implode(", ", $movie_data["cast"])
                    ); ?></p>
                </div>
                <a href="https://www.youtube.com/results?search_query=<?php echo urlencode(
                    $titulo . " Trailer"
                ); ?>">
                    <button class="rounded cs-button-solid">Ver Tráiler</button>
                </a>
            </div>
        </div>
        <div class="comments-container">
            <h2>¡Puntúa la película!</h2>
            <form action="../php/puntuar_pelicula.php" method="post">
                <select id="puntuacion" name="puntuacion">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?php echo $i; ?>">⭐<?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
                <input type="hidden" name="user_id" id="user_id"
                value="<?php echo htmlspecialchars($user_id); ?>">
                <input type="hidden" name="movie_id" id="movie_id"
                value="<?php echo htmlspecialchars($id_pelicula); ?>">
                <button type="submit" class="rounded cs-button-solid">Enviar puntuación</button>
            </form>

            <?php if ($puntuado): ?>
                <p>Ya has puntuado esta película con una nota de  <?php echo $rating; ?>⭐, puedes modificarlo</p>
            <?php endif; ?>

            <h2> ¡Crea tu comentario! </h2>
            <form action="../php/crear_comentario.php" method="post">
                <input type="text" id="texto" name="texto">
                <input type="hidden" name="user_id" id="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                <input type="hidden" name="movie_id" id="movie_id"
                    value="<?php echo htmlspecialchars($id_pelicula); ?>">
                <button type="submit" class="rounded cs-button-solid">Enviar comentario</button>
            </form>
            <h2> Comentarios sobre la película: </h2>
            <div id="comments-list"></div>
        </div>
    </main>

    <script src="../js/dropdownLogout.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var movieId = <?php echo json_encode($id_pelicula); ?>;
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../php/cargar_comentarios.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById('comments-list').innerHTML = xhr.responseText;
                }
            };
            xhr.send('movie_id=' + movieId);
        });
    </script>
