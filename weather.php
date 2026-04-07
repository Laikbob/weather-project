<?php
// Подключаем API
define('API_KEY', '');

$lat = $_GET['lat'] ?? null;
$lon = $_GET['lon'] ?? null;
$searchCity = $_GET['city'] ?? null;

// Заглушки
$city = 'Unknown';
$temp = '--';
$desc = 'No data';
$forecast = [];

if (!empty(API_KEY)) {
    if ($lat && $lon) {
        $url = "https://api.openweathermap.org/data/2.5/forecast?lat=$lat&lon=$lon&units=metric&lang=ru&appid=" . API_KEY;
    } elseif ($searchCity) {
        $url = "https://api.openweathermap.org/data/2.5/forecast?q=" . urlencode($searchCity) . "&units=metric&lang=ru&appid=" . API_KEY;
    }
    if (!empty($url)) {
        $data_json = @file_get_contents($url);
        if ($data_json) {
            $data = json_decode($data_json, true);
            $city = $data['city']['name'] ?? $city;
            $temp = $data['list'][0]['main']['temp'] ?? $temp;
            $desc = $data['list'][0]['weather'][0]['description'] ?? $desc;

            // Прогноз на 5 дней
            $days = [];
            foreach($data['list'] as $item) {
                $date = explode(' ', $item['dt_txt'])[0];
                if (!isset($days[$date])) {
                    $days[$date] = [
                            'temp' => $item['main']['temp'],
                            'desc' => $item['weather'][0]['description']
                    ];
                }
            }
            $forecast = $days;
        }
    }
}

// Передаем данные в JS
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Weather Project</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 30px; background: #f0f8ff; }
        nav { margin-bottom: 20px; }
        nav a { margin: 0 10px; text-decoration: none; color: #0072ff; font-weight: bold; }
        .weather-box { border: 1px solid #ccc; padding: 20px; display: inline-block; margin-top: 20px; border-radius: 10px; background: #fff; }
        input, button { padding: 8px; margin: 5px; border-radius: 5px; border: 1px solid #ccc; cursor: pointer; }
        #map { height: 400px; width: 100%; margin-top: 20px; border-radius: 10px; }
        #forecast { margin-top: 20px; }
    </style>
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
    <p><strong>City:</strong> <span id="city"><?= htmlspecialchars($city) ?></span></p>
    <p><strong>Temperature:</strong> <span id="temp"><?= htmlspecialchars($temp) ?></span> °C</p>
    <p><strong>Description:</strong> <span id="desc"><?= htmlspecialchars($desc) ?></span></p>
</div>

<div id="forecast"></div>
<div id="map"></div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let city = <?= json_encode($city) ?>;
    let temp = <?= json_encode($temp) ?>;
    let desc = <?= json_encode($desc) ?>;
    let lat = <?= json_encode($lat ?: 59.437) ?>; // Таллинн по умолчанию
    let lon = <?= json_encode($lon ?: 24.7535) ?>;
    let forecast = <?= json_encode($forecast) ?>;

    // Инициализация карты
    let map = L.map('map').setView([lat, lon], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(map);
    let weatherMarker = L.marker([lat, lon]).addTo(map).bindPopup(`<b>${city}</b><br>${temp} °C, ${desc}`).openPopup();

    // Обновление погоды и карты
    function updateWeather(cityNew, tempNew, descNew, latNew, lonNew, forecastNew){
        document.getElementById('city').textContent = cityNew;
        document.getElementById('temp').textContent = tempNew;
        document.getElementById('desc').textContent = descNew;

        if(latNew && lonNew){
            map.setView([latNew, lonNew], 12);
            weatherMarker.setLatLng([latNew, lonNew]);
            weatherMarker.bindPopup(`<b>${cityNew}</b><br>${tempNew} °C, ${descNew}`).openPopup();
        }

        // Прогноз
        const container = document.getElementById('forecast');
        container.innerHTML = '<h3>Прогноз на 5 дней</h3>';
        for(let day in forecastNew){
            let f = forecastNew[day];
            container.innerHTML += `<p><b>${day}:</b> ${f.temp}°C, ${f.desc}</p>`;
        }
    }
    updateWeather(city, temp, desc, lat, lon, forecast);

    // Поиск города
    document.getElementById('searchForm').addEventListener('submit', function(e){
        e.preventDefault();
        let searchCity = document.getElementById('cityInput').value;
        if(!searchCity) return;

        fetch(`config/weather_api.php?city=${encodeURIComponent(searchCity)}`)
            .then(res => res.json())
            .then(data => updateWeather(data.city, data.temp, data.desc, data.lat, data.lon, data.forecast));
    });

    // Геолокация
    document.getElementById('geoBtn').addEventListener('click', function(){
        if(navigator.geolocation){
            navigator.geolocation.getCurrentPosition(function(position){
                let lat = position.coords.latitude;
                let lon = position.coords.longitude;

                fetch(`config/weather_api.php?lat=${lat}&lon=${lon}`)
                    .then(res => res.json())
                    .then(data => updateWeather(data.city, data.temp, data.desc, data.lat, data.lon, data.forecast));
            }, function(){ alert('Не удалось определить местоположение'); });
        } else alert('Геолокация не поддерживается');
    });
</script>
</body>
</html>