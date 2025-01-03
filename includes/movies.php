<?php
include_once 'conectar.php';

$pdo = conectar();
// Verificar que se reciban los parámetros
$genre = isset($_GET['genre']) ? $_GET['genre'] : null;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$itemsPerPage = 30; // Elementos por página
$start = ($page - 1) * $itemsPerPage; // Establecer el inicio de la página

// Verificar si se seleccionó un género
if ($genre) {
    // Consulta SQL para obtener las películas basadas en el género y la paginación
    $sql = "
        SELECT m.id, m.title, m.date
        FROM movie m
        INNER JOIN moviegenre mg ON m.id = mg.movie_id
        INNER JOIN genre g ON mg.genre = g.id
        WHERE g.id = :genre
        LIMIT :start, :limit
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':genre', $genre, PDO::PARAM_INT);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $itemsPerPage, PDO::PARAM_INT);
} else {
    // Consulta SQL para obtener todas las películas
    $sql = "
        SELECT m.id, m.title, m.date
        FROM movie m
        LIMIT :start, :limit
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $itemsPerPage, PDO::PARAM_INT);
}

try {
    // Ejecutar la consulta
    $stmt->execute();
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consultar el total de películas para la paginación
    $totalSql = "SELECT COUNT(*) FROM movie";
    $totalStmt = $pdo->prepare($totalSql);
    $totalStmt->execute();
    $totalMovies = $totalStmt->fetchColumn();

    // Devolver las películas y el total como JSON
    echo json_encode([
        'movies' => $movies,
        'total' => $totalMovies
    ]);
} catch (PDOException $e) {
    // Error de consulta
    echo json_encode(["error" => $e->getMessage()]);
}
?>
