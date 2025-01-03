<?php
// Incluye la función de conexión a la base de datos
include_once 'conectar.php'; // Asegúrate de que esta ruta sea correcta

// Llamar a la función de conexión
$pdo = conectar(); // Obtener la conexión a la base de datos

try {
    // Consulta para obtener todos los géneros
    $sql = "SELECT id, name FROM genre";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Obtener los géneros
    $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver los géneros como JSON
    echo json_encode($genres);
} catch (PDOException $e) {
    // En caso de error, devolver el mensaje de error como JSON
    echo json_encode(["error" => $e->getMessage()]);
}
?>
