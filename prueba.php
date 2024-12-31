<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Portada de Película</title>
</head>
<body>
<form method="POST" action="">
    <label for="movie_name">Nombre de la Película:</label>
    <input type="text" id="movie_name" name="movie_name" required>
    <button type="submit" name="search">Buscar</button>
</form>

<?php
    if (isset($_POST['search'])) {
        $movie_name = urlencode($_POST['movie_name']);
        $env = parse_ini_file('.env');
        $api_key = $env['TMDB_API_KEY'];
        // URL de la API para buscar la película por nombre
        $url = "https://api.themoviedb.org/3/search/movie?api_key=$api_key&query=$movie_name";

        // Realizar la petición a la API
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        // Verificar si se encontraron resultados
        if (!empty($data['results'])) {
            $first_result = $data['results'][0];
            $poster_path = $first_result['poster_path'];

            // Mostrar la imagen de la portada
            if ($poster_path) {
                $image_url = "https://image.tmdb.org/t/p/w500$poster_path";
                echo "<img src='$image_url' alt='Portada de la película'>";
} else {
echo "<p>No se encontró una portada para esta película.</p>";
}
} else {
echo "<p>No se encontraron resultados para la búsqueda.</p>";
}
}
?>
</body>
</html>