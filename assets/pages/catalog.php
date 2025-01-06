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
$genero_seleccionado = $_GET['genero_input'] ?? null;
$filtrado_seleccionado = $_GET['sort_input'] ?? null;
$peliculas_por_pagina = 30;
$offset = ($pagina_actual - 1) * $peliculas_por_pagina;

$peliculas = [];
$hay_pagina_siguiente = false;

try {
    $orden = 'm.title ASC';
    if ($filtrado_seleccionado !== null) {
        $orden = match ($filtrado_seleccionado) {
            'title' => 'm.title ASC',
            'rating' => 'AVG(us.score) DESC',
            'id' => 'm.id ASC',
            default => 'm.title ASC'
        };
    }

    $baseQuery = "FROM movie m";
    $whereClause = "";
    $params = [];

    if ($genero_seleccionado !== null) {
        $baseQuery .= " JOIN moviegenre mg ON m.id = mg.movie_id";
        $whereClause = " WHERE mg.genre = :genero";
        $params[':genero'] = $genero_seleccionado;
    }
    if ($filtrado_seleccionado == 'rating') {
        $baseQuery .= " JOIN user_score us ON m.id = us.id_movie";
    }

    $query_count = "SELECT COUNT(DISTINCT m.id) as total " . $baseQuery . $whereClause;
    $stmt_count = $pdo->prepare($query_count);
    $stmt_count->execute($params);
    $total_peliculas = $stmt_count->fetch(PDO::FETCH_ASSOC)["total"];

    $query_peliculas = "SELECT m.id, m.title " .
        $baseQuery .
        $whereClause .
        " GROUP BY m.id, m.title " .
        " ORDER BY " . $orden .
        " LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($query_peliculas);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_INT);
    }
    $stmt->bindValue(':limit', $peliculas_por_pagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $peliculas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hay_pagina_siguiente = $total_peliculas > ($pagina_actual * $peliculas_por_pagina);

} catch (PDOException $e) {
    error_log("Error en la base de datos: " . $e->getMessage());
    echo "Ha ocurrido un error al cargar los datos. Por favor, inténtelo de nuevo.";
    exit();
}
function getGenreName($id)
{
    switch ($id) {
        case 0:
            return "Desconocido";
        case 1:
            return "Acción";
        case 2:
            return "Aventura";
        case 3:
            return "Animación";
        case 4:
            return "Infantil";
        case 5:
            return "Comedia";
        case 6:
            return "Crimen";
        case 7:
            return "Documental";
        case 8:
            return "Drama";
        case 9:
            return "Fantasía";
        case 10:
            return "Cine Negro";
        case 11:
            return "Terror";
        case 12:
            return "Musical";
        case 13:
            return "Misterio";
        case 14:
            return "Romance";
        case 15:
            return "Ciencia Ficción";
        case 16:
            return "Suspenso";
        case 17:
            return "Guerra";
        case 18:
            return "Western";
    }
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
    <link rel="stylesheet" href="/assets/css/core.css">
    <link rel="stylesheet" href="/assets/css/catalogo.css">
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

    <script src="/assets/js/dropdownLogout.js"></script>
    <main>
        <h1>Busca películas con nuestro catálogo.</h1>

        <section>
            <h2>Nuestro catálogo:</h2>

            <!-- Contenedor de Géneros -->
            <div class="catalog-container">
                <form method="GET" id="genre-form">
                    <button type="button" id="dropdown-button-genre">
                        Géneros <span id="dropdown-arrow-genre">▼</span>
                    </button>
                    <div id="dropdown-list-genre" class="hidden">
                        <div class="column">
                            <div>
                                <input type="submit" name="genero_input" value="1"> Acción
                            </div>
                            <div>
                                <input type="submit" name="genero_input" value="2"> Aventura
                            </div>
                            <div>
                                <input type="submit" name="genero_input" value="3"> Animación
                            </div>
                            <div>
                                <input type="submit" name="genero_input" value="15"> Ciencia Ficción
                            </div>
                            <div>
                                <input type="submit" name="genero_input" value="10"> Cine Negro
                            </div>
                            <div>
                                <input type="submit" name="genero_input" value="5"> Comedia
                            </div>
                            <div>
                                <input type="submit" name="genero_input" value="6"> Crimen
                            </div>
                        </div>
                        <div class="column">
                            <div>
                                <input type="submit" name="genero_input" value="0"> Desconocido
                            </div>
                            <div>
                                <input type="submit" name="genero_input" value="7"> Documental
                            </div>
                            <div>
                                <input type="submit" name="genero_input" value="8"> Drama
                            </div>
                            <div>
                                <input type="submit" name="genero_input" value="9"> Fantasía
                            </div>
                            <div>
                                <input type="submit" name="genero_input" value="17"> Guerra
                            </div>
                            <div>
                                <input type="submit" name="genero_input" value="4"> Infantil
                            </div>
                            <div>
                                <input type="submit" name="genero_input" value="13"> Misterio
                            </div>
                        </div>
                        <div class="column">
                            <div>
                                <input type="submit" name="genero_input" value="12"> Musical
                            </div>
                            <div>
                                <input type="submit" name="genero_input" value="14"> Romance
                            </div>
                            <div>
                                <input type="submit" name="genero_input" value="16"> Suspenso
                            </div>
                            <div>
                                <input type="submit" name="genero_input" value="11"> Terror
                            </div>
                            <div>
                                <input type="submit" name="genero_input" value="18"> Western
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="sort_input" id="sort_input"
                        value="<?php echo htmlspecialchars($filtrado_seleccionado); ?>">
                </form>


                <!-- Contenedor de Filtrados -->
                <div class="sort-container">
                    <form method="GET" id="sort-form">
                        <button type="button" id="dropdown-button-sort">
                            Ordenar por: <span id="dropdown-arrow-sort">▼</span>
                        </button>
                        <div id="dropdown-list-sort" class="hidden">
                            <div class="column">
                                <div>
                                    <input type="submit" name="sort_input" value="id" > ID
                                </div>
                                <div>
                                    <input type="submit" name="sort_input" value="title" > Título
                                </div>
                                <div>
                                    <input type="submit" name="sort_input" value="rating" > Puntuación
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="genero_input" id="genero_input"
                            value="<?php echo htmlspecialchars($genero_seleccionado); ?>">
                    </form>

                </div>

                <!-- Contenedor de Películas -->
                <div class="movies-container">
                    <?php if ($genero_seleccionado !== null): ?>
                        <div class="genre-title">
                            <?php
                            $name = getGenreName($genero_seleccionado);
                            echo "<p>Estás buscando: {$name}</p>";
                            ?>
                        </div>

                        <div class="movies-grid">
                            <?php if (count($peliculas) > 0): ?>
                                <?php
                                $displayed_ids = [];
                                foreach ($peliculas as $pelicula):
                                    if (!in_array($pelicula['id'], $displayed_ids)):
                                        $displayed_ids[] = $pelicula['id'];
                                        ?>
                                        <div class="movie-card" data-movie-id="<?php echo $pelicula["id"]; ?>"
                                            data-movie-title="<?php echo htmlspecialchars($pelicula["title"]); ?>">
                                            <a href="pelicula.php?pelicula=<?php echo $pelicula['id']; ?>">
                                                <img src="<?php echo !empty($pelicula['cover']) ?
                                                    htmlspecialchars($pelicula['cover']) : '../images/placeholder.jpg'; ?>"
                                                    alt="<?php echo htmlspecialchars($pelicula['title']); ?>" class="lazy-image">
                                                <h3><?php echo htmlspecialchars($pelicula['title']); ?></h3>
                                            </a>
                                                <p> ⭐<?php echo get_movie_rating($pelicula['id']); ?> / 5 </p>
                                        </div>
                                        <?php
                                    endif;
                                endforeach;
                                ?>
                            <?php else: ?>
                                <p>No se encontraron películas para este género.</p>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <p>Selecciona un género para ver las películas disponibles.</p>
                    <?php endif; ?>
                </div>
                <!-- Paginación -->
                <div class="pagination">
                    <?php if ($genero_seleccionado !== null): ?>
                        <?php if ($pagina_actual > 1): ?>
                            <a href="catalog.php?genero_input=<?php echo $genero_seleccionado; ?>&sort_input=<?php echo $filtrado_seleccionado; ?>&pagina=<?php echo $pagina_actual - 1; ?>"
                                class="pagination-btn">Página Anterior</a>
                        <?php endif; ?>
                        <?php if ($hay_pagina_siguiente): ?>
                            <a href="catalog.php?genero_input=<?php echo $genero_seleccionado; ?>&sort_input=<?php echo $filtrado_seleccionado; ?>&pagina=<?php echo $pagina_actual + 1; ?>"
                                class="pagination-btn">Página Siguiente</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
        </section>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const movieCards = document.querySelectorAll('.movie-card');
                const genreForm = document.getElementById('genre-form');
                const sortForm = document.getElementById('sort-form');
                movieCards.forEach(card => {
                    const movieTitle = card.dataset.movieTitle;

                    fetch(`get_movie_info.php?title=${encodeURIComponent(movieTitle)}`)
                        .then(response => response.json())
                        .then(data => {
                            const img = card.querySelector('img');
                            img.src = data.cover;
                        });
                });
                /* Botones de género y filtro */
                var dropdownButtonGenre = document.getElementById('dropdown-button-genre');
                var dropdownButtonSort = document.getElementById('dropdown-button-sort');

                /* Elementos dropdown */
                var dropdownListGenre = document.getElementById('dropdown-list-genre');
                var dropdownArrowGenre = document.getElementById('dropdown-arrow-genre');

                var dropdownListSort = document.getElementById('dropdown-list-sort');
                var dropdownArrowSort = document.getElementById('dropdown-arrow-sort');

                /* Inputs ocultos */
                var generoInput = document.getElementById('genero_input');
                var sortInput = document.getElementById('sort_input');

                dropdownButtonGenre.addEventListener('click', () => {
                    if (dropdownListGenre.style.display === 'none' || dropdownListGenre.style.display === '') {
                        dropdownListGenre.style.display = 'block';
                        dropdownArrowGenre.textContent = '▲';
                    } else {
                        dropdownListGenre.style.display = 'none';
                        dropdownArrowGenre.textContent = '▼';
                    }
                });

                dropdownButtonSort.addEventListener('click', () => {
                    if (dropdownListSort.style.display === 'none' || dropdownListSort.style.display === '') {
                        dropdownListSort.style.display = 'block';
                        dropdownArrowSort.textContent = '▲';
                    } else {
                        dropdownListSort.style.display = 'none';
                        dropdownArrowSort.textContent = '▼';
                    }
                });

                dropdownListGenre.addEventListener('click', (event) => {
                    if (event.target && event.target.matches('div[data-id]')) {
                        const genreId = event.target.getAttribute('data-id');
                        generoInput.value = genreId;
                        dropdownListGenre.classList.add('hidden');
                        dropdownArrowGenre.textContent = '▼';
                        document.getElementById('genre-form').submit();
                    }
                });

                dropdownListSort.addEventListener('click', (event) => {
                    if (event.target && event.target.matches('div[sort-id]')) {
                        const sortId = event.target.getAttribute('sort-id');
                        sortInput.value = sortId;
                        dropdownListSort.classList.add('hidden');
                        dropdownArrowSort.textContent = '▼';
                        document.getElementById('sort-form').submit();
                    }
                });

                // Cerrar dropdown al pulsar
                document.addEventListener('click', (event) => {
                    if (!dropdownButtonGenre.contains(event.target) && !dropdownListGenre.contains(event.target)) {
                        dropdownListGenre.style.display = 'none';
                        dropdownListGenre.classList.add('hidden');
                        dropdownArrowGenre.textContent = '▼';
                    }
                });

                document.addEventListener('click', (event) => {
                    if (!dropdownButtonSort.contains(event.target) && !dropdownListSort.contains(event.target)) {
                        dropdownListSort.style.display = 'none';
                        dropdownListSort.classList.add('hidden');
                        dropdownArrowSort.textContent = '▼';
                    }
                });

                // Corregir el listener del botón de ordenamiento
                dropdownButtonSort.addEventListener('click', (event) => {
                    event.stopPropagation();
                    dropdownListSort.classList.toggle('hidden');
                    dropdownListSort.style.display = dropdownListSort.classList.contains('hidden') ? 'none' : 'block';
                    dropdownArrowSort.textContent = dropdownListSort.classList.contains('hidden') ? '▼' : '▲';
                });

            });
        </script>
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

</html>
