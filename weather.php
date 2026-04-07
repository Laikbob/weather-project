<?php
require_once __DIR__ . '/config/db.php'; // Подключаем $conn
global $conn;


// Получаем координаты из GET
$lat = $_GET['lat'] ?? null;
$lon = $_GET['lon'] ?? null;

if (!$lat || !$lon) {
    echo json_encode(['error' => 'Не переданы координаты']);
    exit;
}


// Если появится ключ API, раскомментировать:
$url = "https://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&units=metric&lang=et&appid=" . API_KEY;
$data_json = file_get_contents($url);
if ($data_json) {
    $data = json_decode($data_json, true);
    $city = $data['name'] ?? 'Unknown';
    $temp = $data['main']['temp'] ?? 0;
    $desc = $data['weather'][0]['description'] ?? '';
} else {
    $city = 'Test City';
    $temp = 20;
    $desc = 'Clear sky';
}


// Заглушка для разработки без API
$city = 'Test City';
$temp = 20;
$desc = 'Clear sky';

// Сохраняем в базу
$stmt = $conn->prepare("INSERT INTO weather_log (city, temperature, description) VALUES (?, ?, ?)");
$stmt->bind_param("sds", $city, $temp, $desc);
$stmt->execute();

// Возвращаем JSON клиенту
echo json_encode([
    'city' => $city,
    'temp' => $temp,
    'desc' => $desc
]);
?>