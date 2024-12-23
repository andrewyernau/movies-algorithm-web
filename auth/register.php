<?php
require_once 'conectar.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = conectar();
   
        $params = initialize_params($_POST);

        // Creamos una fila en la tabla.
        $query = "INSERT INTO users (id, name, edad, sex, ocupacion, pic, passwd ) VALUES ($params[0], '$params[1]', $params[2], '$params[3]', '$params[4]', '$params[5]', '$params[6]')";
        $pdo->query($query);

        echo "Registrado correctamente.";

        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    function initialize_params($post_data){
        
        $params = array();
        $params[0] = mt_rand(0, 9999999);
        $params[1] = $post_data['name'] ;
        $params[2] = $post_data['edad'] ?? null;
        $params[3] = $post_data['sex'] ?? null;
        $params[4] = $post_data['ocupacion'] ?? null;
        $params[5] = $post_data['pic'] ?? null;    
        $params[6] = password_hash($post_data['password'], PASSWORD_DEFAULT);

        return $params;

    }


}
?>
