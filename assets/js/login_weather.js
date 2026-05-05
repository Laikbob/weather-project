const box = document.getElementById("weatherBox");
const lang = document.documentElement.lang || "et";

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
    loadWeatherByCity("Tallinn");
}

function loadWeather(lat, lon) {
    fetch("config/weather_api.php?lat=" + lat + "&lon=" + lon + "&lang=" + lang)
        .then(r => r.json())
        .then(show);
}

function loadWeatherByCity(city) {
    fetch(`/config/weather_api.php?city=${city}&lang=${lang}`)
        .then(r => r.json())
        .then(show);
}
function show(data) {
    if (data.error) {

        let msg = {
            ru: "❌ Ошибка погоды",
            et: "❌ Ilma viga"
        };

        box.innerHTML = msg[lang] || "❌ Weather error";
        return;
    }

    box.innerHTML = `
        🌤 <b>${data.city}</b><br>
        🌡 ${data.temp}°C<br>
         ${data.desc}
    `;
}