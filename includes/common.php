<?php
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
?>
