<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo file_get_contents('registerform.html'); 
    exit;
}

function initialize_params($post_data) {
    $params = array();
    $params[0] = mt_rand(0, 9999999);
    $params[1] = htmlspecialchars($post_data['name']);
    $params[2] = isset($post_data['edad']) ? intval($post_data['edad']) : null;
    $params[3] = htmlspecialchars($post_data['sex'] ?? '');
    $params[4] = htmlspecialchars($post_data['ocupacion'] ?? '');
    $params[5] = htmlspecialchars($post_data['pic'] ?? '');    
    $params[6] = sha1($post_data['password']);
    return $params;
}

require_once '../includes/conectar.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = conectar();
   
        $params = initialize_params($_POST);

        // Creamos una fila en la tabla.
        $query = "INSERT INTO users (id, name, edad, sex, ocupacion, pic, passwd ) VALUES ($params[0], '$params[1]', $params[2], '$params[3]', '$params[4]', '$params[5]', '$params[6]')";
        $pdo->query($query);

        
        $message = '<div style="text-align: center; color: green; margin-bottom: 20px;">¡Registrado! Ahora inicia sesión</div>';
        $button = '<div style="text-align: center;"><a href="../index.html" style="text-decoration: none; color: #000; background-color: #f1f1f1; padding: 10px 20px; border-radius: 5px;">Iniciar sesión</a></div>';
        echo $message;
        echo $button;
        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }



}
?>
