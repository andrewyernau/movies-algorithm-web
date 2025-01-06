<?php
require_once "../../includes/conectar.php";
require_once "../../auth/check_session.php";

$user_id = (int) $_COOKIE["user_id"];
$pdo = conectar();

$query = "SELECT * FROM users WHERE id = $user_id";
$result = $pdo->query($query);
$user = $result->fetch(PDO::FETCH_ASSOC);

$username = htmlspecialchars($user["name"]);
$userpic = !empty($user["pic"]) ? htmlspecialchars($user["pic"]) : "../images/userdefault.png";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        if (isset($_POST["name"]) && isset($_POST["edad"]) && isset($_POST["sex"]) && isset($_POST["ocupacion"])) {
            $name = htmlspecialchars($_POST["name"]);
            $edad = (int) $_POST["edad"];
            $sex = htmlspecialchars($_POST["sex"]);
            $ocupacion = htmlspecialchars($_POST["ocupacion"]);
            $pic = !empty($_POST["pic"]) ? htmlspecialchars($_POST["pic"]) : $userpic;

            $query = "UPDATE users SET name = :name, edad = :edad, sex = :sex, ocupacion = :ocupacion, pic = :pic WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ":name" => $name,
                ":edad" => $edad,
                ":sex" => $sex,
                ":ocupacion" => $ocupacion,
                ":pic" => $pic,
                ":id" => $user_id
            ]);

            echo "<script>alert('Datos personales actualizados con éxito');</script>";
            header("Refresh:0");
        }
        if (isset($_POST["currentPassword"]) && isset($_POST["newPassword"]) && isset($_POST["confirmPassword"])) {
            $currentPassword = $_POST["currentPassword"];
            $newPassword = $_POST["newPassword"];
            $confirmPassword = $_POST["confirmPassword"];

            if (!password_verify($currentPassword, $user["passwd"])) {
                echo "<script>alert('La contraseña actual no es correcta');</script>";
            } elseif ($newPassword !== $confirmPassword) {
                echo "<script>alert('Las nuevas contraseñas no coinciden');</script>";
            } else {
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                $query = "UPDATE users SET passwd = :passwd WHERE id = :id";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    ":passwd" => $hashedPassword,
                    ":id" => $user_id
                ]);

                echo "<script>alert('Contraseña cambiada con éxito');</script>";
            }
        }
    } catch (PDOException $e) {
        error_log("Error en la base de datos: " . $e->getMessage());
        echo "<script>alert('Ha ocurrido un error al intentar actualizar los datos');</script>";
    }
}
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
    <header>
        <nav>
            <ul>
                <li><a href="main.php" style="font-weight: bold; font-size: 1.5em;">Inicio</a></li>
                <li><a href="./catalog.php">Catálogo</a></li>
            </ul>
        </nav>
        <div class="user-info">
            <img src="<?php echo $userpic; ?>" alt="User Avatar">
            <span><?php echo $username; ?></span>
            <div class="dropdown-menu">
                <a href="profile.php"> Mi perfil </a>
                <a href="../../auth/logout.php">Cerrar sesión</a>
            </div>
        </div>
    </header>
    <main>
        <div class="user-container">
            <div class="user-cover">
                <img src="<?php echo $userpic; ?>" alt="Foto de perfil">
            </div>
            <div class="user-info">
                <p class="user-title">Editar Perfil de <?php echo $username; ?></p>
                <form method="POST" action="">
                    <p><strong>Nombre:</strong> <input type="text" name="name"
                            value="<?php echo htmlspecialchars($user["name"]); ?>"></p>
                    <p><strong>Edad:</strong> <input type="number" name="edad"
                            value="<?php echo htmlspecialchars($user["edad"]); ?>"></p>
                    <p><strong>Género:</strong>
                        <select name="sex">
                            <option value="Masculino" <?php echo $user["sex"] == "Masculino" ? "selected" : ""; ?>>
                                Masculino</option>
                            <option value="Femenino" <?php echo $user["sex"] == "Femenino" ? "selected" : ""; ?>>Femenino
                            </option>
                            <option value="Otro" <?php echo $user["sex"] == "Otro" ? "selected" : ""; ?>>Otro</option>
                        </select>
                    </p>
                    <p><strong>Ocupación:</strong>
                        <select name="ocupacion">
                            <option value="none" <?php echo $user["ocupacion"] == "none" ? "selected" : ""; ?>>None
                            </option>
                            <option value="administrator" <?php echo $user["ocupacion"] == "administrator" ? "selected" : ""; ?>>Administrator</option>
                            <option value="artist" <?php echo $user["ocupacion"] == "administrator" ? "selected" : ""; ?>>
                                Artist</option>
                            <option value="doctor" <?php echo $user["ocupacion"] == "doctor" ? "selected" : ""; ?>>Doctor
                            </option>
                            <option value="educator" <?php echo $user["ocupacion"] == "educator" ? "selected" : ""; ?>>
                                Educator</option>
                            <option value="engineer">
                                <?php echo $user["ocupacion"] == "engineer" ? "selected" : ""; ?>Engineer
                            </option>
                            <option value="entertainment" <?php echo $user["ocupacion"] == "entertainment" ? "selected" : ""; ?>>Entertainment</option>
                            <option value="executive" <?php echo $user["ocupacion"] == "executive" ? "selected" : ""; ?>>
                                Executive</option>
                            <option value="healthcare" <?php echo $user["ocupacion"] == "healthcare" ? "selected" : ""; ?>>Healthcare</option>
                            <option value="homemaker" <?php echo $user["ocupacion"] == "homemaker" ? "selected" : ""; ?>>
                                Homemaker</option>
                            <option value="lawyer" <?php echo $user["ocupacion"] == "lawyer" ? "selected" : ""; ?>>Lawyer
                            </option>
                            <option value="librarian" <?php echo $user["ocupacion"] == "librarian" ? "selected" : ""; ?>>
                                Librarian</option>
                            <option value="marketing" <?php echo $user["ocupacion"] == "marketing" ? "selected" : ""; ?>>
                                Marketing</option>
                            <option value="programmer" <?php echo $user["ocupacion"] == "programmer" ? "selected" : ""; ?>>Programmer</option>
                            <option value="retired" <?php echo $user["ocupacion"] == "retired" ? "selected" : ""; ?>>
                                Retired</option>
                            <option value="salesman" <?php echo $user["ocupacion"] == "salesman" ? "selected" : ""; ?>>
                                Salesman</option>
                            <option value="scientist" <?php echo $user["ocupacion"] == "scientist" ? "selected" : ""; ?>>
                                Scientist</option>
                            <option value="student" <?php echo $user["ocupacion"] == "student" ? "selected" : ""; ?>>
                                Student</option>
                            <option value="technician" <?php echo $user["ocupacion"] == "technician" ? "selected" : ""; ?>>Technician</option>
                            <option value="writer" <?php echo $user["ocupacion"] == "writer" ? "selected" : ""; ?>>Writer
                            </option>
                            <option value="other" <?php echo $user["ocupacion"] == "other" ? "selected" : ""; ?>>Other
                            </option>
                        </select>
                    </p>
                    <p><strong>Imagen de perfil (URL):</strong> <input type="text" name="pic"
                            value="<?php echo htmlspecialchars($user["pic"]); ?>"></p>
                    <button type="submit" class="cs-button-solid">Guardar Cambios</button>
                </form>
            </div>
        </div>
        <div class="user-container">
            <div class="user-info">
                <p class="user-title">Cambiar Contraseña</p>
                <form method="POST" action="">
                    <input type="password" name="currentPassword" placeholder="Contraseña actual">
                    <input type="password" name="newPassword" placeholder="Nueva contraseña">
                    <input type="password" name="confirmPassword" placeholder="Confirmar nueva contraseña">
                    <button type="submit" class="cs-button-solid">Cambiar Contraseña</button>
                </form>
            </div>
        </div>
    </main>
</body>

</html>