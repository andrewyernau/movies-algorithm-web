<?php
require_once "../../includes/conectar.php";
require_once "../../auth/check_session.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $user_id = (int) $_COOKIE["user_id"];
    $pdo = conectar();
    $query = "SELECT passwd FROM users WHERE id = $user_id";
    $result = $pdo->query($query);
    $user = $result->fetch(PDO::FETCH_ASSOC);

    $inputPassword = sha1($_POST['password']);
    $storedPassword = $user["passwd"];

    if ($inputPassword === $storedPassword) {
        echo json_encode(["success" => true, "message" => "Contraseña correcta"]);
    } else {
        echo json_encode(["success" => false, "message" => "Contraseña incorrecta"]);
    }
    exit;
}
?>
