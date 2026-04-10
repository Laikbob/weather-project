const box = document.getElementById("weatherBox");

// сначала пробуем геолокацию
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(success, fallback);
} else {
    fallback();
}

function success(pos) {
    loadWeather(pos.coords.latitude, pos.coords.longitude);
}

function fallback() {
    // если гео запрещено → используем Tallinn
    loadWeatherByCity("Tallinn");
}

function loadWeather(lat, lon) {
    fetch(`/config/weather_api.php?lat=${lat}&lon=${lon}`)
        .then(r => r.json())
        .then(show);
}

function loadWeatherByCity(city) {
    fetch(`/config/weather_api.php?city=${city}`)
        .then(r => r.json())
        .then(show);
}

function show(data) {
    if (data.error) {
        box.innerHTML = "❌ Ошибка погоды";
        return;
    }

    box.innerHTML = `
        🌤 <b>${data.city}</b><br>
        🌡 ${data.temp}°C<br>
        ⛅ ${data.desc}
    `;
}