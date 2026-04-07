<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Weather Project</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="assets/css/weather.css">
</head>
<body>

<nav>
    <a href="index.php">Новости</a>
    <a href="weather.php">Погода</a>
</nav>

<h1>Weather Project 🌤️</h1>

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