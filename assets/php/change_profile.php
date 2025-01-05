<?php
// movies-algorithm-web/assets/php/change_profile.php
require_once "../../includes/conectar.php";
require_once "../../auth/check_session.php";

$user_id = (int) $_COOKIE["user_id"];
$pdo = conectar();

// Obtener la información actual del usuario
$query = "SELECT * FROM users WHERE id = $user_id";
$result = $pdo->query($query);
$user = $result->fetch(PDO::FETCH_ASSOC);

// Procesar el formulario de edición
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $updates = [];

    // Verificar cada campo y agregarlo al array si ha sido modificado
    if (isset($_POST["name"]) && $_POST["name"] !== $user["name"]) {
        $updates[] = "name = '" . htmlspecialchars($_POST["name"]) . "'";
    }
    if (isset($_POST["edad"]) && $_POST["edad"] != $user["edad"]) {
        $updates[] = "edad = " . (int) $_POST["edad"];
    }
    if (isset($_POST["sex"]) && $_POST["sex"] !== $user["sex"]) {
        $updates[] = "sex = '" . htmlspecialchars($_POST["sex"]) . "'";
    }
    if (isset($_POST["ocupacion"]) && $_POST["ocupacion"] !== $user["ocupación"]) {
        $updates[] = "ocupación = '" . htmlspecialchars($_POST["ocupacion"]) . "'";
    }
    if (isset($_POST["pic"]) && $_POST["pic"] !== $user["pic"]) {
        $updates[] = "pic = '" . htmlspecialchars($_POST["pic"]) . "'";
    }
    echo var_dump($updates);

    // Si hay campos para actualizar, ejecutar la consulta
    if (!empty($updates)) {
        $update_query = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = $user_id";
        $pdo->query($update_query);
    }

    header("Location: ../pages/profile.php");
    exit();
}
?>
