<?php
function conectar() {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=aitdb', 'ait', 'password');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>