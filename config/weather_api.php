<?php
header('Content-Type: application/json');

require __DIR__ . '/env.php';
loadEnv(__DIR__ . '/../.env');

define('API_KEY', getenv('OPENWEATHER_KEY'));

$lat = $_GET['lat'] ?? null;
$lon = $_GET['lon'] ?? null;
$searchCity = $_GET['city'] ?? null;

$response = [
    'city' => null,
    'temp' => null,
    'desc' => null,
    'lat' => null,
    'lon' => null,
    'forecast' => [],
    'error' => null
];

// Проверка ключа
if (empty(API_KEY)) {
    $response['error'] = 'API ключ не указан';
    echo json_encode($response);
    exit;
}

// Формируем URL запроса
if ($lat && $lon) {
    $url = "https://api.openweathermap.org/data/2.5/forecast?lat=$lat&lon=$lon&units=metric&lang=ru&appid=" . API_KEY;
} elseif ($searchCity) {
    $url = "https://api.openweathermap.org/data/2.5/forecast?q=" . urlencode($searchCity) . "&units=metric&lang=ru&appid=" . API_KEY;
} else {
    $response['error'] = 'Не переданы координаты или название города';
    echo json_encode($response);
    exit;
}

// Получаем данные
$data_json = @file_get_contents($url);
if (!$data_json) {
    $response['error'] = 'Не удалось получить данные с OpenWeatherMap';
    echo json_encode($response);
    exit;
}

$data = json_decode($data_json, true);
if (isset($data['cod']) && $data['cod'] != 200) {
    $response['error'] = $data['message'] ?? 'Ошибка API';
    echo json_encode($response);
    exit;
}

// Основные данные
$response['city'] = $data['city']['name'] ?? null;
$response['temp'] = $data['list'][0]['main']['temp'] ?? null;
$response['desc'] = $data['list'][0]['weather'][0]['description'] ?? null;
$response['lat'] = $data['city']['coord']['lat'] ?? $lat;
$response['lon'] = $data['city']['coord']['lon'] ?? $lon;

// Прогноз на 5 дней (берем первый прогноз каждого дня)
$days = [];
foreach ($data['list'] as $item) {
    $date = explode(' ', $item['dt_txt'])[0];
    if (!isset($days[$date])) {
        $days[$date] = [
            'temp' => $item['main']['temp'],
            'desc' => $item['weather'][0]['description']
        ];
    }
}
$response['forecast'] = $days;

echo json_encode($response);