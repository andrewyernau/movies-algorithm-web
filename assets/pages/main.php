<?php
require_once('../../auth/check_session.php');
require_once('../../includes/conectar.php');
require_once('../../includes/apiutils.php');

function quitarFecha($string)
{
    $patron = '/\s*\(\d{4}\)\s*/';


    $resultado = preg_replace($patron, '', $string);

    return $resultado;
}

function get_image_url($movie_name)
{
    $movie_name = urlencode(quitarFecha($movie_name));
    $api_key = returnAPIfromenv('TMDB_API_KEY');
    $url = "https://api.themoviedb.org/3/search/movie?api_key=$api_key&query=$movie_name";
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    // Verificar si se encontraron resultados
    if (!empty($data['results'])) {
        $first_result = $data['results'][0];
        $poster_path = $first_result['poster_path'];

        // Mostrar la imagen de la portada
        if ($poster_path) {
            $image_url = "https://image.tmdb.org/t/p/w500$poster_path";
        }
    }
    return $image_url;
}

try {
    $pdo = conectar();
    $user_id = (int) $_COOKIE['user_id'];

    $query = "SELECT name, pic FROM users WHERE id = $user_id";
    $result = $pdo->query($query);
    $user = $result->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        setcookie('user_id', '', time() - 3600, '/');
        header('Location: ../../index.html');
        exit;
    }

    $username = htmlspecialchars($user['name']);
    $userpic = !empty($user['pic']) ? htmlspecialchars($user['pic']) : '../images/userdefault.png';

} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit;
}

try {
    $pagina_actual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;

    $peliculas_por_pagina = 30;

    $offset = ($pagina_actual - 1) * $peliculas_por_pagina;

    $query = "SELECT title FROM movie LIMIT $peliculas_por_pagina OFFSET $offset";
    $result = $pdo->query($query);

    $peliculas = $result->fetchAll(PDO::FETCH_ASSOC);



} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMDb Clone</title>
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
        <h1>Welcome to IMDb Clone</h1>

        <section>
            <script src="../js/carousel.js"></script>
            <h2>Featured Movies</h2>
            <div class="carousel-container">
                <button class="carousel-btn left-btn">❮</button>
                <div class="carousel">
                    <?php
                    foreach ($peliculas as $pelicula) {
                        $pelicula_imagen = get_image_url($pelicula['title']);
                        echo '
    <div class="movie-card">
        <img data-src="' . htmlspecialchars($pelicula_imagen) . '" alt="' . htmlspecialchars($pelicula['title']) . '" class="lazy-image">
        <h3>' . htmlspecialchars($pelicula['title']) . '</h3>
        <p>Rating: N/A</p>
    </div>';
                    }
                    ?>
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