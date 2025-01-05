<?php
include_once 'conectar.php';

$pdo = conectar();

try {
    $sql = "SELECT id, name FROM genre";
    $res = $pdo->query($sql);

    if ($res === false) {
        
        throw new PDOException("Error en la consulta: " . $pdo->errorInfo()[2]);
    }

    $genres = $res->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($genres);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>