<?php
require_once "../../auth/check_session.php";
require_once "../../includes/conectar.php";
require_once "../../includes/common.php";
require_once "./get_movie_info.php";

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

$pagina_actual = isset($_GET["pagina"]) ? (int) $_GET["pagina"] : 1;
$genero_seleccionado = isset($_GET["genero_input"]) ? (int) $_GET["genero_input"] : null;

$peliculas_por_pagina = 31;
$offset = ($pagina_actual - 1) * $peliculas_por_pagina;

$peliculas = [];
$generos = [];
$hay_pagina_siguiente = false;
$orden = isset($_GET['sort_input']) ? $_GET['sort_input'] : 'id';



if ($genero_seleccionado == null) {
    $query_peliculas = "SELECT m.id, m.title
                        FROM movie m
                        ORDER BY $orden
                        LIMIT $peliculas_por_pagina OFFSET $offset";
    $query_count = "SELECT COUNT(id) as total from movie";

} else {
    $query_peliculas = "SELECT m.id, m.title
                        FROM movie m
                        JOIN moviegenre mg ON m.id = mg.movie_id
                        WHERE mg.genre = $genero_seleccionado
                        ORDER BY $orden
                        LIMIT $peliculas_por_pagina OFFSET $offset";


    $query_count = "SELECT COUNT(DISTINCT m.id) as total
                        FROM movie m
                        JOIN moviegenre mg ON m.id = mg.movie_id
                        WHERE mg.genre = $genero_seleccionado";

}
    try {

        $result_peliculas = $pdo->query($query_peliculas);
        $peliculas = $result_peliculas->fetchAll(PDO::FETCH_ASSOC);

        foreach ($peliculas as &$pelicula) {
            echo "<!-- Getting data for: " . $pelicula['title'] . " -->";
            $pelicula["cover"] = "../images/placeholder.jpg";
            echo "<!-- Cover assigned: " . $pelicula["cover"] . " -->";
        }

        $result_count = $pdo->query($query_count);
        $total_peliculas = $result_count->fetch(PDO::FETCH_ASSOC)["total"];

        $hay_pagina_siguiente = $total_peliculas > $pagina_actual * $peliculas_por_pagina;
    } catch (PDOException $e) {
        echo "Error al cargar los datos: " . $e->getMessage();
        exit();
    }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Recomendador</title>
    <link rel="icon" type="image/x-icon" href="../images/ico.ico">
    <link href='https://fonts.googleapis.com/css?family=DM Sans' rel='stylesheet'>
    <style>
        body {
            font-family: 'DM Sans';
            font-size: 22px;
        }
    </style>
    <link rel="stylesheet" href="../css/core.css">
    <link rel="stylesheet" href="../css/catalog.css">
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
                <a href="../../auth/logout.php">Log out</a>
            </div>
        </div>
    </header>

    <main>
        <h1>Busca películas con nuestro catálogo.</h1>

        <section>
            <h2>Nuestro catálogo:</h2>
            <div class="catalog-container">
                <form method="GET" id="genre-form">
                    <button type="button" id="dropdown-button">
                        Géneros <span id="dropdown-arrow">▼</span>
                    </button>
                    <div id="dropdown-list" class="hidden">
                        <div class="column">
                            <div data-id="1">Acción</div>
                            <div data-id="2">Aventura</div>
                            <div data-id="3">Animación</div>
                            <div data-id="15">Ciencia Ficción</div>
                            <div data-id="10">Cine Negro</div>
                            <div data-id="5">Comedia</div>
                            <div data-id="6">Crimen</div>
                        </div>
                        <div class="column">
                            <div data-id="0">Desconocido</div>
                            <div data-id="7">Documental</div>
                            <div data-id="8">Drama</div>
                            <div data-id="9">Fantasía</div>
                            <div data-id="17">Guerra</div>
                            <div data-id="4">Infantil</div>
                            <div data-id="13">Misterio</div>
                        </div>
                        <div class="column">
                            <div data-id="12">Musical</div>
                            <div data-id="14">Romance</div>
                            <div data-id="16">Suspenso</div>
                            <div data-id="11">Terror</div>
                            <div data-id="18">Western</div>
                        </div>
                    </div>
                    <input type="hidden" name="genero_input" id="genero_input"
                        value="<?php echo htmlspecialchars($genero_seleccionado); ?>">
                </form>

                <div class="sort-container">
                    <form method="GET" id="sort-form">
                        <input type="hidden" name="genero_input" value="<?php echo htmlspecialchars($genero_seleccionado); ?>">
                        <button type="button" id="sort-button">
                            Ordenar por: <span id="sort-arrow">▼</span>
                        </button>
                        <div id="sort-options" class="hidden">
                            <button type="submit" name="sort_input" value="id">ID</button>
                            <button type="submit" name="sort_input" value="title">Nombre</button>
                            <button type="submit" name="sort_input" value="rating">Puntuación</button>
                        </div>
                    </form>
                </div>

                <!-- Contenedor de Películas -->
                <div class="movies-grid">
                    <?php if (count($peliculas) > 0): ?>
                        <?php
                        $displayed_ids = [];
                        foreach ($peliculas as $pelicula):
                            if (!in_array($pelicula['id'], $displayed_ids)):
                                $displayed_ids[] = $pelicula['id'];
                                ?>
                                    <div class="movie-card" data-movie-id="<?php echo $pelicula[
                                        "id"
                                    ]; ?>" data-movie-title="<?php echo htmlspecialchars(
                                         $pelicula["title"]
                                     ); ?>">
                                    <a href="pelicula.php?pelicula=<?php echo $pelicula['id']; ?>">
                                        <img src="<?php echo !empty($pelicula['cover']) ? htmlspecialchars($pelicula['cover']) : '../images/placeholder.jpg'; ?>"
                                            alt="<?php echo htmlspecialchars($pelicula['title']); ?>" class="lazy-image">
                                        <h3><?php echo htmlspecialchars($pelicula['title']); ?></h3>
                                        <p>Rating: N/A</p>
                                    </a>
                                </div>
                            <?php
                            endif;
                        endforeach;
                        ?>
                    <?php else: ?>
                        <!-- <p>No se encontraron películas para este género.</p> -->
                    <?php endif; ?>
                </div>

                <!-- Paginación -->
                <div class="pagination">
                    <?php if ($pagina_actual > 1): ?>
                        <a href="catalog.php?genre=<?php echo $genero_seleccionado; ?>&pagina=<?php echo $pagina_actual - 1; ?>"
                            class="pagination-btn">Página Anterior</a>
                    <?php endif; ?>
                    <?php if ($hay_pagina_siguiente): ?>
                        <a href="catalog.php?genre=<?php echo $genero_seleccionado; ?>&pagina=<?php echo $pagina_actual + 1; ?>"
                            class="pagination-btn">Página Siguiente</a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
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

                var dropdownButton = document.getElementById('dropdown-button');
                var dropdownList = document.getElementById('dropdown-list');
                var dropdownArrow = document.getElementById('dropdown-arrow');
                var generoInput = document.getElementById('genero_input');

                dropdownButton.addEventListener('click', () => {
                    if (dropdownList.style.display === 'none' || dropdownList.style.display === '') {
                        dropdownList.style.display = 'block';
                        dropdownArrow.textContent = '▲';
                    } else {
                        dropdownList.style.display = 'none';
                        dropdownArrow.textContent = '▼';
                    }
                });

                dropdownList.addEventListener('click', (event) => {
                    if (event.target && event.target.matches('div[data-id]')) {
                        const genreId = event.target.getAttribute('data-id');
                        generoInput.value = genreId;
                        dropdownList.classList.add('hidden');
                        dropdownArrow.textContent = '▼';
                        document.getElementById('genre-form').submit();
                    }
                });

                // Cerrar dropdown
                document.addEventListener('click', (event) => {
                    if (!dropdownButton.contains(event.target) && !dropdownList.contains(event.target)) {
                        dropdownList.style.display = 'none';
                        dropdownList.classList.add('hidden');
                        dropdownArrow.textContent = '▼';
                    }
                });

            });
        </script>
        <script src="../js/dropdownLogout.js"></script>
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
