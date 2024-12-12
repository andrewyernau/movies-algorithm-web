<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=authdb', 'ait', 'password');

        // Usamos el id="username" del formulario html
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Creamos una fila en la tabla.
        $query = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        $pdo->query($query);

        echo "Registrado correctamente.";

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
