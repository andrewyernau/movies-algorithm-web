<?php
// login.php
if (isset($_COOKIE['user_id'])) {
    header('Location: main.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=authdb', 'ait', 'password');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $username = $_POST['username'];
        // La contraseña que se envía, se guarda en $password y luego se comparará su hash
        $password = $_POST['password'];

        // Selecciono la fila entera y se guardará en el array $user 
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = $pdo->query($query);
        $user = $result->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            setcookie('user_id', $user['id'], time() + 3600, '/');
            header('Location: main.html');
            exit;
        } else {
            echo "Usuario o contraseña incorrectos.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>