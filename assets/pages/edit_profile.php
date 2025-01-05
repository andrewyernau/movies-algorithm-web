```php
<?php
require_once "../../includes/conectar.php";
require_once "../../auth/check_session.php";

$user_id = (int) $_COOKIE["user_id"];
$pdo = conectar();

// Obtener la información actual del usuario
$query = "SELECT * FROM users WHERE id = $user_id";
$result = $pdo->query($query);
$user = $result->fetch(PDO::FETCH_ASSOC);

$username = htmlspecialchars($user["name"]);
$userpic = !empty($user["pic"]) ? htmlspecialchars($user["pic"]) : "../images/userdefault.png";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil de <?php echo $username; ?></title>

    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="../css/core.css">
</head>

<body>
    <main>
        <div class="user-container">
            <div class="user-cover">
                <img src="<?php echo $userpic; ?>" alt="Foto de perfil">
            </div>
            <div class="user-info">
                <p class="user-title">Editar Perfil de <?php echo $username; ?></p>
                <form method="POST" action="../php/change_profile.php">
                    <p><strong>Nombre:</strong> <input type="text" name="name" value="<?php echo htmlspecialchars($user["name"]); ?>"></p>
                    <p><strong>Edad:</strong> <input type="number" name="edad" value="<?php echo htmlspecialchars($user["edad"]); ?>"></p>
                    <p><strong>Género:</strong>
                        <select name="sex">
                            <option value="Masculino" <?php echo $user["sex"] == "Masculino" ? "selected" : ""; ?>>Masculino</option>
                            <option value="Femenino" <?php echo $user["sex"] == "Femenino" ? "selected" : ""; ?>>Femenino</option>
                            <option value="Otro" <?php echo $user["sex"] == "Otro" ? "selected" : ""; ?>>Otro</option>
                        </select>
                    </p>
                    <p><strong>Ocupación:</strong> <input type="text" name="ocupacion" value="<?php echo htmlspecialchars($user["ocupación"]); ?>"></p>
                    <p><strong>Imagen de perfil (URL):</strong> <input type="text" name="pic" value="<?php echo htmlspecialchars($user["pic"]); ?>"></p>
                    <button type="submit" class="cs-button-solid">Guardar Cambios</button>
                </form>
            </div>
        </div>
        <div class="user-container">
            <div class="user-info">
                <p class="user-title">Cambiar Contraseña</p>
                <form id="changePasswordForm">
                    <input type="password" id="currentPassword" class="cs-input" name="currentPassword" placeholder="Contraseña actual">
                    <input type="password" id="newPassword" class="cs-input" name="newPassword" placeholder="Nueva contraseña">
                    <input type="password" id="confirmPassword" class="cs-input" name="confirmPassword" placeholder="Confirmar nueva contraseña">
                    <button type="submit" class="cs-button-solid">Cambiar Contraseña</button>
                </form>
                <p id="passwordChangeResult"></p>
            </div>
        </div>
    </main>
</body>

</html>
