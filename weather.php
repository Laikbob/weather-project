<?php
require __DIR__ . '/config/db.php';
global $conn;
require __DIR__ . '/config/lang.php';
list($lang, $text) = getLang();

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Weather Project</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="assets/css/weather.css">
</head>
<body>

<h1>Weather Project 🌤️</h1>

<div class="lang-switch">
    <a href="?lang=et">🇪🇪 Eesti</a>
    <a href="?lang=ru">🇷🇺 Русский</a>
</div>

<!-- меню -->
<nav class="top-nav">
    <ul>
        <li><a href="index.php?lang=<?= $lang ?>"><?= $text[$lang]['news'] ?></a></li>
        <li><a href="favorites.php?lang=<?= $lang ?>"><?= $text[$lang]['fav'] ?></a></li>
    </ul>
</nav>

<form id="searchForm">
    <input type="text" id="cityInput" placeholder="Введите город">
    <button type="submit">Поиск</button>
    <button type="button" id="geoBtn">📍 Геолокация</button>
</form>

<div class="weather-box" id="weather">
    <h2>Weather Info</h2>
    <p><strong>Город:</strong> <span id="city">--</span></p>
    <p><strong>Температура:</strong> <span id="temp">--</span> °C</p>
    <p><strong>Описание:</strong> <span id="desc">--</span></p>
</div>

<div id="forecast"></div>
<div id="map"></div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="assets/js/weather.js"></script>
</body>
</html>