<?php
if(!isset($_COOKIE['user_id'])){
    header('Location: ../../index.html');
    exit;
}

require_once('../../includes/conectar.php');
try{
    $pdo = conectar();
    $user_id = $_COOKIE['user_id'];
    $query = "SELECT id FROM users WHERE id = $user_id";
    $result = $pdo->query($query);
    $user = $result->fetch(PDO::FETCH_ASSOC);

    if(!$user){//si el usuario no existe, lo lleva al index.html

        setcookie('user_id', '', time() - 3600, '/');
        header('Location: ../../index.html');
        exit;
    }

    //extiende la sesión a 1 hora de nuevo
    setcookie('user_id', $user_id, time() + 3600, '/');
}
catch(PDOException $e){
    echo $e->getMessage();
    exit;
}
?>