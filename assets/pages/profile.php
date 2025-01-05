<?php
require_once "../../includes/conectar.php";
require_once "../../auth/check_session.php";


$user_id = (int) $_COOKIE["user_id"];
$pdo = conectar();
$query = "SELECT * FROM users WHERE id = $user_id";
$result = $pdo->query($query);
$user = $result->fetch(PDO::FETCH_ASSOC);

$passwd = $user["passwd"];
$username = htmlspecialchars($user["name"]);
$userpic = !empty($user["pic"]) ? htmlspecialchars($user["pic"]) : "../images/userdefault.png";

$query = "SELECT COUNT(*) AS count FROM moviecomments WHERE user_id=$user_id";
$result = $pdo->query($query);
$comments = $result->fetch(PDO::FETCH_ASSOC);

$query = "SELECT COUNT(*) AS count FROM user_score WHERE id_user=$user_id";
$result = $pdo->query($query);
$movies = $result->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($user["name"]);?></title>

    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="../css/core.css">
</head>

<body>
    <main>
        <div class="user-container">
            <div class="user-cover">
                <img src="<?php echo $userpic; ?>" alt="Foto de perfil">
            </div>
            <div class="profile-info">
            <p class="user-title">Perfil de usuario de  <?php echo $username; ?></p>
            <p> <strong> Edad: </strong> <?php echo htmlspecialchars($user["edad"]); ?></p>
            <p> <strong> Género: </strong> <?php echo htmlspecialchars($user["sex"]); ?></p>
            <p> <strong> Ocupación: </strong> <?php echo htmlspecialchars($user["ocupación"]); ?></p>
            <p> <strong> Comentarios escritos: </strong> <?php echo htmlspecialchars($comments["count"]); ?></p>
            <p> <strong> Películas puntuadas: </strong> <?php echo htmlspecialchars($movies["count"]); ?> </p>

            </div>
        </div>

        <div class="user-container">
            <div class="profile-info">
                <p class="user-title">Comprueba tu contraseña</p>
                <form id="checkPasswordForm">
                    <input type="password" id="passwordInput" class="cs-input" name="password" placeholder="Introduce tu contraseña">
                    <button type="submit" class="cs-button-solid">Comprobar</button>
                </form>
                <p id="passwordResult"></p>
                <a href="edit_profile.php"><button class="cs-button-solid rounded">Editar perfil</button></a>
            </div>
        </div>
    </main>
</body>
</main>

<script>
document.getElementById('checkPasswordForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const passwordInput = document.getElementById('passwordInput').value;

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../php/check_password.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            const resultElement = document.getElementById('passwordResult');
            if (response.success) {
                resultElement.textContent = response.message;
                resultElement.style.color = 'green';
            } else {
                resultElement.textContent = response.message;
                resultElement.style.color = 'red';
            }
        }
    };

    xhr.send(`password=${encodeURIComponent(passwordInput)}`);
});
</script>
