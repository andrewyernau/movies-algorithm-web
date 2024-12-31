<?php
require_once('../../auth/check_session.php');
require_once('../../includes/conectar.php');

try {
    $pdo = conectar();
    $user_id = (int) $_COOKIE['user_id'];

    $query = "SELECT name, pic FROM users WHERE id = $user_id";
    $result = $pdo->query($query);
    $user = $result->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        setcookie('user_id', '', time() - 3600, '/');
        header('Location: ../../index.html');
        exit;
    }

    $username = htmlspecialchars($user['name']);
    $userpic = !empty($user['pic']) ? htmlspecialchars($user['pic']) : '../images/userdefault.png';

} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMDb Clone</title>
    <link href='https://fonts.googleapis.com/css?family=DM Sans' rel='stylesheet'>
    <style>
        body {
            font-family: 'DM Sans';
            font-size: 22px;
        }
    </style>
    <link rel="stylesheet" href="../css/main.css">
    <script src="../js/dropdownLogout.js"></script>
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="#" style="font-weight: bold; font-size: 1.5em;">IMDb</a></li>
                <li><a href="#">Movies</a></li>
                <li><a href="#">TV Shows</a></li>
                <li><a href="#">Celebrities</a></li>
                <li><a href="#">News</a></li>
            </ul>
        </nav>
        <div class="user-info">
            <img src="<?php echo $userpic; ?>" alt="User Avatar">
            <span><?php echo $username; ?></span>
            <div class="dropdown-menu">
                <a href="../../auth/logout.php">Log out</a>
            </div>
        </div>
    </header>

    <main>
        <h1>Welcome to IMDb Clone</h1>

        <section>
            <h2>Featured Movies</h2>
            <div class="movie-grid">
                <div class="movie-card">
                    <img src="/placeholder.svg?height=300&width=200" alt="Movie 1">
                    <h3>Movie Title 1</h3>
                    <p>Rating: 8.5/10</p>
                </div>
                <div class="movie-card">
                    <img src="/placeholder.svg?height=300&width=200" alt="Movie 2">
                    <h3>Movie Title 2</h3>
                    <p>Rating: 7.9/10</p>
                </div>
                <div class="movie-card">
                    <img src="/placeholder.svg?height=300&width=200" alt="Movie 3">
                    <h3>Movie Title 3</h3>
                    <p>Rating: 8.2/10</p>
                </div>
            </div>
        </section>

        <section>
            <h2>Top Rated TV Shows</h2>
            <div class="movie-grid">
                <div class="movie-card">
                    <img src="/placeholder.svg?height=300&width=200" alt="TV Show 1">
                    <h3>TV Show Title 1</h3>
                    <p>Rating: 9.2/10</p>
                </div>
                <div class="movie-card">
                    <img src="/placeholder.svg?height=300&width=200" alt="TV Show 2">
                    <h3>TV Show Title 2</h3>
                    <p>Rating: 8.8/10</p>
                </div>
                <div class="movie-card">
                    <img src="/placeholder.svg?height=300&width=200" alt="TV Show 3">
                    <h3>TV Show Title 3</h3>
                    <p>Rating: 9.0/10</p>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 IMDb Clone. All rights reserved.</p>
        <nav>
            <a href="#">Privacy Policy</a> |
            <a href="#">Terms of Service</a> |
            <a href="#">Contact Us</a>
        </nav>
    </footer>
</body>

</html>