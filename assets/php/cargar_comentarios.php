<?php
require_once "../../includes/conectar.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $movie_id = (int)$_POST['movie_id'];

    $pdo = conectar();
    $query = "SELECT u.name, mc.comment FROM moviecomments mc JOIN users u ON mc.user_id = u.id WHERE mc.movie_id = $movie_id";
    $result = $pdo->query($query);

    foreach ($result as $row) {
        echo "<h3>" . $row['name'] . "</h3>";
        echo "<p>" . $row['comment'] . "</p>";
        echo "<hr>";
    }
}
else {
    echo "No se ha recibido ningún dato.";
}

?>
