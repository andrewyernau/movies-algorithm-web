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
            font-family: 'DM Sans';font-size: 22px;
        }
    </style>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #121212;
            color: white;
            padding: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
        }
        nav ul li {
            display: inline;
            margin-right: 20px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
        }
        .user-info {
            display: flex;
            align-items: center;
        }
        .user-info img {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }
        main {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .movie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        .movie-card {
            background-color: white;
            border-radius: 5px;
            padding: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .movie-card img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }
        footer {
            background-color: #121212;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
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
