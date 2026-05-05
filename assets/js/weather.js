let map, weatherMarker;
let currentLang = document.documentElement.lang;


function updateWeather(data){
    if(data.error){
        alert(data.error);
        return;
    }

    document.getElementById('city').textContent = data.city;
    document.getElementById('temp').textContent = "🌡️ " + data.temp;
    document.getElementById('desc').textContent = data.desc;

    // animation
    const tempEl = document.getElementById('temp');
    tempEl.style.transition = 'transform 0.3s';
    tempEl.style.transform = 'scale(1.3)';
    setTimeout(() => tempEl.style.transform = 'scale(1)', 300);

    // MAP
    if(data.lat && data.lon){
        document.getElementById('map').style.display = 'block';

        if(!map){
            map = L.map('map').setView([data.lat, data.lon], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            weatherMarker = L.marker([data.lat, data.lon]).addTo(map);
        }

        map.setView([data.lat, data.lon], 12);

        weatherMarker
            .setLatLng([data.lat, data.lon])
            .bindPopup(`<b>${data.city}</b><br>${data.temp} °C, ${data.desc}`)
            .openPopup();
    }

    // FORECAST
    const container = document.getElementById('forecast');
    container.innerHTML = '';

    let delay = 0;
    for(let date in data.forecast){
        const f = data.forecast[date];

        const card = document.createElement('div');
        card.className = 'forecast-card';
        card.innerHTML = `
            <h4>${f.date}</h4>
            <p>🌡️ ${f.temp} °C</p>
            <p>${f.desc}</p>
        `;

        container.appendChild(card);

        setTimeout(() => {
            card.style.opacity = 1;
            card.style.transform = 'translateY(0)';
        }, delay);

        delay += 100;
    }
}

// SEARCH
document.getElementById('searchForm').addEventListener('submit', function(e){
    e.preventDefault();

    const city = document.getElementById('cityInput').value.trim();
    if(!city) return;

    fetch(`config/weather_api.php?city=${encodeURIComponent(city)}&lang=${currentLang}`)
        .then(res => res.json())
        .then(updateWeather)
        .catch(console.error);
});
// открываюшийся список
document.querySelectorAll('.dropdown').forEach(drop => {
    const btn = drop.querySelector('.dropbtn');
    const menu = drop.querySelector('.dropdown-content');

    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        menu.classList.toggle('show');
    });
});

document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown-content').forEach(menu => {
        menu.classList.remove('show');
    });
});

// GEO
document.getElementById('geoBtn').addEventListener('click', function(){

    if(!navigator.geolocation){
        alert('Геолокация не поддерживается');
        return;
    }

    navigator.geolocation.getCurrentPosition(pos => {

        const lat = pos.coords.latitude;
        const lon = pos.coords.longitude;

        fetch(`config/weather_api.php?lat=${lat}&lon=${lon}&lang=${currentLang}`)
            .then(res => res.json())
            .then(updateWeather)
            .catch(console.error);

    }, () => {
        alert('Не удалось определить местоположение');
    });
});

document.addEventListener("DOMContentLoaded", () => {

    // авто загрузка по гео при загрузке страницы
    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(pos => {

            const lat = pos.coords.latitude;
            const lon = pos.coords.longitude;

            fetch(`config/weather_api.php?lat=${lat}&lon=${lon}&lang=${currentLang}`)
                .then(res => res.json())
                .then(updateWeather);

        });
    }

});

