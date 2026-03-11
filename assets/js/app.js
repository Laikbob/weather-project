document.getElementById('refresh-btn').addEventListener('click', () => {
    // Заглушка: здесь можно подключить API для погоды
    document.getElementById('temp').textContent = '22°C';
    document.getElementById('humidity').textContent = '55%';
});