<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo file_get_contents('loginform.html'); 
    exit;
}

require_once '../includes/conectar.php';
// login.php
if (isset($_COOKIE['user_id'])) {
    header('Location: ../assets/pages/main.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = conectar();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $username = $_POST['username'];
        // La contraseña que se envía, se guarda en $password y luego se comparará su hash
        $password = sha1($_POST['password']);

        // Selecciono la fila entera y se guardará en el array $user 
        $query = "SELECT * FROM users WHERE name = '$username' AND passwd = '$password'";
        $result = $pdo->query($query);
        $user = $result->fetch(PDO::FETCH_ASSOC);

        if ($user ) {
            setcookie('user_id', $user['id'], time() + 3600, '/');
            header('Location: ../assets/pages/main.php');
            exit;
        } else {
            echo "Usuario o contraseña incorrectos.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>