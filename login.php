<?php
global $conn;
session_start();
require "config/zonedb.php";
require "config/lang.php";
require_once __DIR__ . '/config/rss_fetch.php';

// ===== язык =====
$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'et');
$_SESSION['lang'] = $lang;

list($lang, $text) = getLang();
$t = $text[$lang];

// ===== login =====
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = "❌ Введите username и password";
    } else {

        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {

            // 🔥 ВАЖНО: правильная сессия под весь проект
            $_SESSION['user'] = [
                    "id" => $user['id'],
                    "username" => $user['username']
            ];

            if(isset($_GET['redirect'])){
                header("Location: " . $_GET['redirect']);
            } else {
                header("Location: index.php");
            }
            exit;

        } else {
            $error = "❌ " . $t['error'];
        }
    }
}


// ===== LAST NEWS (RSS) =====

if ($lang === "ru") {
    $feeds = [
            "ERR" => "https://rus.err.ee/rss",
            "Postimees" => "https://rus.postimees.ee/rss"
    ];
} else {
    $feeds = [
            "ERR" => "https://www.err.ee/rss",
            "Postimees" => "https://www.postimees.ee/rss",
            "Õhtuleht" => "https://www.ohtuleht.ee/rss"
    ];
}

$allNews = [];

foreach ($feeds as $name => $url) {
    $allNews = array_merge($allNews, fetchNews($url, $name));
}

if (!empty($allNews)) {
    usort($allNews, fn($a, $b) => $b['pubDate'] <=> $a['pubDate']);
    $lastNews = $allNews[0]['title'];
} else {
    $lastNews = "Нет новостей";
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>
<!-- HEADER -->
<div class="header-bar">
    <div class="logo"><?= $text[$lang]['title'] ?></div>

    <div class="user-panel">
        <div class="time">
            📅 <?= date("d.m.Y") ?> | 🕒 <span id="clock"></span>
        </div>
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
    </div>
</div>
<div class="page">
    <div class="box">

        <div class="top">
            <h1><?= $t['login'] ?></h1>

            <?php if (isset($_GET['registered'])): ?>
                <div class="message success">
                    ✅ Регистрация успешна! Теперь войдите
                </div>
            <?php endif; ?>

            <!-- LOGIN FORM -->
            <form method="POST">

                <input name="username" placeholder="<?= $t['username'] ?>">
                <div class="password-box">
                    <input name="password" type="password" id="password" placeholder="<?= $text[$lang]['password'] ?>" required>
                    <span class="toggle-pass" onclick="togglePass()">👁️</span>
                </div>

                <button type="submit"><?= $t['login'] ?></button>
                <a href="register.php">
                    <button type="button"><?= $t['reg'] ?></button>
                </a>

            </form>

            <!-- ERROR -->
            <?php if ($error): ?>
                <div style="color:red;">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <!-- WEATHER -->
            <div class="info" id="weatherBox">
                🌤 Загружаем погоду...
            </div>

            <!-- LAST NEWS -->
            <div class="info">
                📰 <?= $t['last_news'] ?>:<br>
                <b><?= htmlspecialchars($lastNews) ?></b>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/login.js"></script>
<script src="assets/js/login_weather.js"></script>

</body>
</html>