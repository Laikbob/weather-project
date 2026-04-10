<?php
global $conn;
session_start();
require "config/db.php";
require "config/lang.php";

list($lang, $t) = getLang();

if ($_POST) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header("Location: index.php");
        exit;
    } else {
        $error = "❌ Ошибка входа";
    }
}

$news = $conn->query("SELECT title FROM favorites ORDER BY id DESC LIMIT 1");
$lastNews = $news->fetch_assoc()['title'] ?? "Нет новостей";
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>

<div class="box">

    <div class="top">
        <div class="time">
            📅 <?= date("d.m.Y") ?> | 🕒 <span id="clock"></span>
        </div>

        <h1><?= $t['title'] ?></h1>

        <!-- язык -->
        <div class="lang-switch">
            <a href="?lang=et">🇪🇪 Eesti</a>
            <a href="?lang=ru">🇷🇺 Русский</a>
        </div>

        <input name="username" placeholder="<?= $t['username'] ?>">
        <input name="password" type="password" placeholder="<?= $t['password'] ?>">

        <button><?= $t['login'] ?></button>

        <div class="info" id="weatherBox">
            🌤 Загружаем погоду...
        </div>

        <div class="info">
            📰 <?= $t['last_news'] ?>:<br>
            <b><?= htmlspecialchars($lastNews) ?></b>
        </div>

</div>

<script src="assets/js/login.js"></script>

</body>
<script src="assets/js/login_weather.js"></script>
</html>