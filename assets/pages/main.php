<?php
require_once "../../auth/check_session.php";
require_once "../../includes/conectar.php";
require_once "../../includes/common.php";

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
    $userpic = !empty($user["pic"])
        ? htmlspecialchars($user["pic"])
        : "../images/userdefault.png";
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit();
}

try {
    $pagina_actual = isset($_GET["pagina"]) ? (int) $_GET["pagina"] : 1;

    $peliculas_por_pagina = 30;

    $offset = ($pagina_actual - 1) * $peliculas_por_pagina;

    $query = "SELECT id, title FROM movie LIMIT $peliculas_por_pagina OFFSET $offset";
    $result = $pdo->query($query);

    $peliculas = $result->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
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
    <link rel="stylesheet" href="../css/main.css">
    <script src="../js/dropdownLogout.js"></script>
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="#" style="font-weight: bold; font-size: 1.5em;">IMDb</a></li>
                <li><a href="#">Movies</a></li>
                <li><a href="#">TV Shows</a></li>
                <li><a href="#">Celebrities</a></li>
                <li><a href="#">News</a></li>
            </ul>
        </nav>
        <div class="user-info">
            <img src="<?php echo $userpic; ?>" alt="User Avatar">
            <span><?php echo $username; ?></span>
            <div class="dropdown-menu">
                <a href="../../auth/logout.php">Log out</a>
            </div>
        </div>
    </header>

    <main>
        <h1>Bienvenidos al Recomendador de películas.</h1>

        <section>
            <script src="../js/carousel.js"></script>
            <h2>Featured Movies</h2>
            <div class="carousel-container">
                <button class="carousel-btn left-btn">❮</button>
                <div class="carousel">
                    <?php foreach ($peliculas as $pelicula) {
                        $movie_data = get_movie_data($pelicula["title"]);
                        echo '
    <div class="movie-card">
    <a href="pelicula.php?pelicula=' .
                            $pelicula["id"] .
                            '">
        <img data-src="' .
                            htmlspecialchars($movie_data["cover"]) .
                            '" alt="' .
                            htmlspecialchars($pelicula["title"]) .
                            '" class="lazy-image">
        <h3>' .
                            htmlspecialchars($pelicula["title"]) .
                            '</h3>
        <p>Rating: N/A</p>
        </a>
    </div>';
                    } ?>
                </div>
                <button class="carousel-btn right-btn">❯</button>
            </div>
        </section>

        <section>
            <h2>Top Rated TV Shows</h2>
            <div class="movie-grid">
                <div class="movie-card">
                    <img src="/placeholder.svg?height=300&width=200" alt="TV Show 1">
                    <h3>TV Show Title 1</h3>
                    <p>Rating: 9.2/10</p>
                </div>
                <div class="movie-card">
                    <img src="/placeholder.svg?height=300&width=200" alt="TV Show 2">
                    <h3>TV Show Title 2</h3>
                    <p>Rating: 8.8/10</p>
                </div>
                <div class="movie-card">
                    <img src="/placeholder.svg?height=300&width=200" alt="TV Show 3">
                    <h3>TV Show Title 3</h3>
                    <p>Rating: 9.0/10</p>
                </div>

            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 IMDb Clone. All rights reserved.</p>
        <nav>
            <a href="#">Privacy Policy</a> |
            <a href="#">Terms of Service</a> |
            <a href="#">Contact Us</a>
        </nav>
    </footer>
</body>

</html>
