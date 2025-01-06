<?php
require_once "conectar.php";
function returnAPIfromEnv($api_name)
{
    $env = parse_ini_file("../../.env");
    return $env[$api_name];
}

function quitarParentesis($string)
{
    $patron = "/\s*\([^()]*\)\s*/";

    $resultado = preg_replace($patron, "", $string);

    $resultado = trim($resultado);

    return $resultado;
}

function get_image_url($movie_name)
{
    $movie_name = urlencode(quitarParentesis($movie_name));
    $api_key = returnAPIfromenv("TMDB_API_KEY");
    $url = "https://api.themoviedb.org/3/search/movie?api_key=$api_key&query=$movie_name";
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (!empty($data["results"])) {
        $first_result = $data["results"][0];
        $poster_path = $first_result["poster_path"];

        if ($poster_path) {
            $image_url = "https://image.tmdb.org/t/p/w500$poster_path";
        }
    }
    return $image_url;
}
function get_movie_data($movie_name)
{
    $movie_data = [];
    $movie_name = urlencode(quitarParentesis($movie_name));
    $api_key = returnAPIfromenv("TMDB_API_KEY");

    // Primero, buscar la película para obtener su ID
    $search_url = "https://api.themoviedb.org/3/search/movie?api_key=$api_key&query=$movie_name";
    $search_response = file_get_contents($search_url);
    $search_data = json_decode($search_response, true);

    if (!empty($search_data["results"])) {
        $movie_id = $search_data["results"][0]["id"];

        // Obtener los detalles de la película usando su ID
        $details_url = "https://api.themoviedb.org/3/movie/$movie_id?api_key=$api_key&append_to_response=credits";
        $details_response = file_get_contents($details_url);
        $details_data = json_decode($details_response, true);

        // Obtener la URL de la portada
        if (!empty($details_data["poster_path"])) {
            $movie_data["cover"] =
                "https://image.tmdb.org/t/p/w500" .
                $details_data["poster_path"];
        } else {
            $movie_data["cover"] = null;
        }

        // Obtener la duración
        $movie_data["runtime"] = isset($details_data["runtime"])
            ? $details_data["runtime"]
            : null;

        // Obtener el reparto (limitado a los primeros 5 actores)
        $cast = [];
        if (!empty($details_data["credits"]["cast"])) {
            $cast_count = 0;
            foreach ($details_data["credits"]["cast"] as $actor) {
                if ($cast_count >= 5) {
                    break;
                }
                $cast[] = $actor["name"];
                $cast_count++;
            }
        }
        $movie_data["cast"] = $cast;

        // Agregar más información relevante
        $movie_data["overview"] = $details_data["overview"];
        $movie_data["release_date"] = $details_data["release_date"];
        $movie_data["vote_average"] = $details_data["vote_average"];
    }

    return $movie_data;
}
function get_movie_rating($movie_id)
{
    $pdo = conectar();
    $query = "SELECT AVG(score) AS rating FROM user_score WHERE id_movie = $movie_id";
    $result = $pdo->query($query);
    $rating = $result->fetch(PDO::FETCH_ASSOC);
    return round($rating["rating"], 1);
}

function get_movie_rating_count($movie_id)
{
    $pdo = conectar();
    $query = "SELECT COUNT(DISTINCT id_user) AS countr FROM user_score WHERE id_movie = $movie_id;";
    $result = $pdo->query($query);
    $amount = $result->fetch(PDO::FETCH_ASSOC);
    return $amount["countr"];
}
    ?>
