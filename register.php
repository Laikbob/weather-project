<?php
global $conn;
session_start();
require "config/zonedb.php";
require __DIR__ . '/config/lang.php';

$user = $_SESSION['user'] ?? null; // ✅ ВАЖНО

list($lang, $text) = getLang();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']);
    $passwordRaw = $_POST['password'];

    if(strlen($username) < 3 || strlen($passwordRaw) < 4){
        $message = "❌ " . $text[$lang]['reg1']; // ✅
    } else {

        $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "❌ " . $text[$lang]['reg2']; // ✅
        } else {

            $password = password_hash($passwordRaw, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();

            $_SESSION['user'] = [
                    "id" => $stmt->insert_id,
                    "username" => $username
            ];

            header("Location: index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/register.css">
</head>
<body>
<!-- HEADER -->
<div class="header-bar">
    <div class="logo"><?= $text[$lang]['title'] ?></div>

    <div class="user-panel">
        <?php if ($user): ?>
            <span><?= $text[$lang]['wel'] ?> 👋 <?= htmlspecialchars($user['username']) ?></span>
            <a href="weather.php?lang=<?= $lang ?>">🌤️<?= $text[$lang]['weather'] ?></a>
            <a href="favorites.php">⭐ <?= $text[$lang]['fav'] ?></a>
            <div class="lang-switch">
                <div class="dropdown">
                    <button class="dropbtn">
                        🌐 <?= strtoupper($lang) ?>
                    </button>
                    <div class="dropdown-content">
                        <a href="?lang=et" class="<?= $lang == 'et' ? 'active' : '' ?>">
                            🇪🇪 Eesti
                        </a>
                        <a href="?lang=ru" class="<?= $lang == 'ru' ? 'active' : '' ?>">
                            🇷🇺 Русский
                        </a>
                    </div>
                </div>
            </div>
            <a href="logout.php" class="logout-btn">
                <?= $text[$lang]['out'] ?>
            </a>
        <?php else: ?>
            <a href="index.php?lang=<?= $lang ?>">📰<?= $text[$lang]['news'] ?></a>
            <a href="weather.php?lang=<?= $lang ?>">🌤️<?= $text[$lang]['weather'] ?></a>
            <div class="lang-switch">
                <div class="dropdown">
                    <button class="dropbtn">
                        🌐 <?= strtoupper($lang) ?>
                    </button>
                    <div class="dropdown-content">
                        <a href="?lang=et" class="<?= $lang == 'et' ? 'active' : '' ?>">
                            🇪🇪 Eesti
                        </a>
                        <a href="?lang=ru" class="<?= $lang == 'ru' ? 'active' : '' ?>">
                            🇷🇺 Русский
                        </a>
                    </div>
                </div>
            </div>
            <a href="login.php"><?= $text[$lang]['login'] ?></a>
        <?php endif; ?>
    </div>
</div>
<div class="register-container">

    <h2><?= $text[$lang]['reg'] ?></h2>

    <?php if(!empty($message)): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="register-form">
        <input name="username" placeholder="<?= $text[$lang]['username'] ?>" required>

        <div class="password-box">
            <input name="password" type="password" id="password" placeholder="<?= $text[$lang]['password'] ?>" required>
            <span class="toggle-pass" onclick="togglePass()">👁️</span>
        </div>

        <button type="submit"><?= $text[$lang]['reg3'] ?></button>
    </form>

</div>

<script>
    function togglePass(){
        const pass = document.getElementById('password');
        pass.type = pass.type === 'password' ? 'text' : 'password';
    }
</script>

</body>
<script src="assets/js/favorites.js"></script>
</html>