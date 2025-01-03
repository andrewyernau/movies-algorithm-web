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
    <header>
        <nav>
            <ul>
                <li><a href="main.php" style="font-weight: bold; font-size: 1.5em;">Inicio</a></li>
                <li><a href="catalog.php">Catálogo</a></li>
                <li><a href="#">TV Shows</a></li>
                <li><a href="#">Celebrities</a></li>
                <li><a href="#">News</a></li>
            </ul>
        </nav>
        <div class="user-info">
            <img src="<?php echo $userpic; ?>" alt="User Avatar">
            <span><?php echo $username; ?></span>
            <div class="dropdown-menu">
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
    </main>

    <script src="../js/dropdownLogout.js"></script>
</body>

</html>