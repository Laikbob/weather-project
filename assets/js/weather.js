let map, weatherMarker;

function updateWeather(data){
    if(data.error){
        alert(data.error);
        return;
    }

    const cityEl = document.getElementById('city');
    const tempEl = document.getElementById('temp');
    const descEl = document.getElementById('desc');

    cityEl.textContent = data.city;
    tempEl.textContent = data.temp;
    descEl.textContent = data.desc;

    // Пульсация температуры
    tempEl.style.transition = 'transform 0.3s';
    tempEl.style.transform = 'scale(1.3)';
    setTimeout(() => tempEl.style.transform = 'scale(1)', 300);

    // Карта
    if(data.lat && data.lon){
        document.getElementById('map').style.display = 'block';
        if(!map){
            map = L.map('map').setView([data.lat, data.lon], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            weatherMarker = L.marker([data.lat, data.lon]).addTo(map)
                .bindPopup(`<b>${data.city}</b><br>${data.temp} °C, ${data.desc}`).openPopup();

            // Пульсирующая точка
            const pulsingCircle = L.circle([data.lat, data.lon], {
                color: '#0072ff',
                fillColor: '#0072ff',
                fillOpacity: 0.5,
                radius: 200
            }).addTo(map);
            let growing = true;
            setInterval(() => {
                let r = pulsingCircle.getRadius();
                pulsingCircle.setRadius(growing ? r + 10 : r - 10);
                if(r >= 300) growing = false;
                if(r <= 200) growing = true;
            }, 200);

        } else {
            map.setView([data.lat, data.lon], 12);
            weatherMarker.setLatLng([data.lat, data.lon]);
            weatherMarker.bindPopup(`<b>${data.city}</b><br>${data.temp} °C, ${data.desc}`).openPopup();
        }
    }

    // Прогноз на 5 дней с эффектом fade-in
    const container = document.getElementById('forecast');
    container.innerHTML = '';
    let delay = 0;
    for(let day in data.forecast){
        let f = data.forecast[day];
        const card = document.createElement('div');
        card.className = 'forecast-card';
        card.innerHTML = `<h4>${day}</h4><p>${f.temp} °C</p><p>${f.desc}</p>`;
        container.appendChild(card);

        setTimeout(() => {
            card.style.opacity = 1;
            card.style.transform = 'translateY(0)';
        }, delay);
        delay += 100;
    }
}

// Поиск города
document.getElementById('searchForm').addEventListener('submit', function(e){
    e.preventDefault();
    let city = document.getElementById('cityInput').value.trim();
    if(!city) return;
    fetch(`config/weather_api.php?city=${encodeURIComponent(city)}`)
        .then(res => res.json())
        .then(data => updateWeather(data))
        .catch(err => console.error(err));
});

// Геолокация
document.getElementById('geoBtn').addEventListener('click', function(){
    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(function(position){
            let lat = position.coords.latitude;
            let lon = position.coords.longitude;
            fetch(`config/weather_api.php?lat=${lat}&lon=${lon}`)
                .then(res => res.json())
                .then(data => updateWeather(data))
                .catch(err => console.error(err));
        }, function(){ alert('Не удалось определить местоположение'); });
    } else alert('Геолокация не поддерживается');
});