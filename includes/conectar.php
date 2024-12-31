<?php
function conectar() {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=ai0', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die('Error: ' . $e->getMessage());
    }
}
?>