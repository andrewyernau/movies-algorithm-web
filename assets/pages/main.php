<?php
require_once "../../auth/check_session.php";
require_once "../../includes/conectar.php";
require_once "../../includes/common.php";
require_once "./get_movie_info.php";

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
    $pagina_actual = isset($_GET["pagina"]) ? (int) $_GET["pagina"] : 1;
    $peliculas_por_pagina = 30;

    $query = "SELECT m.id, m.title, m.date FROM movie m LIMIT $peliculas_por_pagina";
    $result = $pdo->query($query);
    $peliculas = $result->fetchAll(PDO::FETCH_ASSOC);

    $check_query = "SELECT COUNT(*) as rec_count FROM recs WHERE user_id = $user_id";
    $check_result = $pdo->query($check_query);
    $rec_count = $check_result->fetch(PDO::FETCH_ASSOC)['rec_count'];
    
    if ($rec_count == 0) {
        // Para evitar una ventana vacía, pondremos peliculas aleatorias a modo de sustitución
        $query = "SELECT m.id, m.title, m.date FROM movie m ORDER BY RAND() LIMIT $peliculas_por_pagina";
    } else {
        // Las peliculas por recomendación
        $query = "SELECT m.id, m.title, m.date 
                 FROM movie m 
                 JOIN recs r ON r.movie_id = m.id 
                 WHERE r.user_id = $user_id 
                 ORDER BY r.rec_score DESC 
                 LIMIT $peliculas_por_pagina";
    }
    
    $result = $pdo->query($query);
    $peliculasrec = $result->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("User ID: $user_id");
    error_log("Recommendation count: $rec_count");
    error_log("Returned movies: " . count($peliculasrec));
} catch (PDOException $e) {
    error_log("Recommendation query error: " . $e->getMessage());
    $peliculasrec = [];
    echo "Error de conexión: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Recomendador</title>
    <link rel="icon" type="image/x-icon" href="/assets/images/ico.ico">
    <link href='https://fonts.googleapis.com/css?family=DM Sans' rel='stylesheet'>
    <style>
        body {
            font-family: 'DM Sans';
            font-size: 22px;
        }
    </style>
    <link rel="stylesheet" href="../css/core.css">
    <link rel="stylesheet" href="../css/main.css">
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
        <h1>Bienvenidos al Recomendador de películas.</h1>

        <section>
            <h2>Películas destacadas:</h2>
            <div class="carousel-container">
                <button class="carousel-btn left-btn">❮</button>
                <div class="carousel">
                    <?php foreach ($peliculas as $pelicula) { ?>
                        <div class="movie-card" data-movie-id="<?php echo $pelicula[
                            "id"
                        ]; ?>" data-movie-title="<?php echo htmlspecialchars(
                             $pelicula["title"]
                         ); ?>">
                            <a href="pelicula.php?pelicula=<?php echo $pelicula[
                                "id"
                            ]; ?>">
                                <img src="../images/placeholder.jpg" alt="Loading...">
                                <h3><?php echo htmlspecialchars(
                                    $pelicula["title"]
                                ); ?></h3>
                            </a>
                            <p> <?php echo htmlspecialchars(
                                $pelicula["date"]
                            ); ?> </p>
                        </div>
                    <?php } ?>
                </div>
                <button class="carousel-btn right-btn">❯</button>
            </div>
        </section>

        <section>
            <h2>Te recomendamos:</h2>
            <div class="carousel-container">
                <button class="carousel-btn left-btn">❮</button>
                <div class="carousel">
                    <?php foreach ($peliculasrec as $pelicula) { ?>
                        <div class="movie-card" data-movie-id="<?php echo $pelicula[
                            "id"
                        ]; ?>" data-movie-title="<?php echo htmlspecialchars(
                             $pelicula["title"]
                         ); ?>">
                            <a href="pelicula.php?pelicula=<?php echo $pelicula[
                                "id"
                            ]; ?>">
                                <img src="../images/placeholder.jpg" alt="Loading...">
                                <h3><?php echo htmlspecialchars(
                                    $pelicula["title"]
                                ); ?></h3>
                            </a>
                            <p> <?php echo htmlspecialchars(
                                $pelicula["date"]
                            ); ?> </p>
                        </div>
                    <?php } ?>
                </div>
                <button class="carousel-btn right-btn">❯</button>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 El Recomendador Inc.</p>
        <nav>
            <a href="#">Centro de ayuda</a> |
            <a href="#">Preferencias de cookies</a> |
            <a href="#">Términos de uso</a>
        </nav>
    </footer>
</body>

<script src="../js/carousel.js"></script>
<script src="../js/lazyload.js"></script>
<script src="../js/dropdownLogout.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const movieCards = document.querySelectorAll('.movie-card');

        movieCards.forEach(card => {
            const movieTitle = card.dataset.movieTitle;

            fetch(`get_movie_info.php?title=${encodeURIComponent(movieTitle)}`)
                .then(response => response.json())
                .then(data => {
                    const img = card.querySelector('img');
                    img.src = data.cover;
                });
        });
    });
</script>

</html>