<?php
define('API_KEY', '032145b0ef22d046d99de8752048c3ff');

$lat = $_GET['lat'] ?? null;
$lon = $_GET['lon'] ?? null;
$searchCity = $_GET['city'] ?? null;

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

            // Прогноз на 5 дней (берем один прогноз в день)
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

echo json_encode([
    'city' => $city,
    'temp' => $temp,
    'desc' => $desc,
    'forecast' => $forecast,
    'lat' => $lat,
    'lon' => $lon
]);