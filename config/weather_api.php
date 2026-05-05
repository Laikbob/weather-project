<?php
header('Content-Type: application/json');

require __DIR__ . '/env.php';
loadEnv(__DIR__ . '/../.env');

define('API_KEY', getenv('OPENWEATHER_KEY'));

$lang = $_GET['lang'] ?? 'et';
if (!in_array($lang, ['ru','et','en'])) $lang = 'et';

$lat = $_GET['lat'] ?? null;
$lon = $_GET['lon'] ?? null;
$city = $_GET['city'] ?? null;

if (!$lat && !$lon && !$city) {
    echo json_encode(['error' => 'No input']);
    exit;
}

$url = $lat && $lon
    ? "https://api.openweathermap.org/data/2.5/forecast?lat=$lat&lon=$lon&units=metric&lang=en&appid=" . API_KEY
    : "https://api.openweathermap.org/data/2.5/forecast?q=" . urlencode($city) . "&units=metric&lang=en&appid=" . API_KEY;

$json = @file_get_contents($url);
$data = json_decode($json, true);

if (!$data || $data['cod'] != 200) {
    echo json_encode(['error' => 'API error']);
    exit;
}

// ICONS
$icons = [
    "clear sky" => "☀️",
    "few clouds" => "🌤️",
    "scattered clouds" => "⛅",
    "broken clouds" => "☁️",
    "overcast clouds" => "☁️",
    "light rain" => "🌦️",
    "rain" => "🌧️",
    "thunderstorm" => "⛈️",
    "snow" => "❄️",
    "mist" => "🌫️"
];

// TRANSLATIONS
$translations = [
    "clear sky" => ["ru"=>"Ясно","et"=>"Selge"],
    "few clouds" => ["ru"=>"Малооблачно","et"=>"Vähene pilvisus"],
    "scattered clouds" => ["ru"=>"Облачно","et"=>"Hajus pilvisus"],
    "broken clouds" => ["ru"=>"Облачно","et"=>"Pilvine"],
    "overcast clouds" => ["ru"=>"Пасмурно","et"=>"Lauspilvisus"],
    "light rain" => ["ru"=>"Дождь","et"=>"Nõrk vihm"],
    "rain" => ["ru"=>"Дождь","et"=>"Vihm"],
    "thunderstorm" => ["ru"=>"Гроза","et"=>"Äike"],
    "snow" => ["ru"=>"Снег","et"=>"Lumi"],
    "mist" => ["ru"=>"Туман","et"=>"Udu"]
];

$dayTranslations = [
    'Mon' => ['ru'=>'Пн','et'=>'Esm'],
    'Tue' => ['ru'=>'Вт','et'=>'Tei'],
    'Wed' => ['ru'=>'Ср','et'=>'Kol'],
    'Thu' => ['ru'=>'Чт','et'=>'Nelj'],
    'Fri' => ['ru'=>'Пт','et'=>'Ree'],
    'Sat' => ['ru'=>'Сб','et'=>'Lau'],
    'Sun' => ['ru'=>'Вс','et'=>'Püh']
];

function formatWeather($raw, $lang, $translations, $icons){
    $text = $translations[$raw][$lang] ?? $raw;
    $icon = $icons[$raw] ?? "🌡️";
    return $icon . " " . ucfirst($text);
}

// CURRENT
$raw = $data['list'][0]['weather'][0]['description'];

$response = [
    'city' => $data['city']['name'],
    'temp' => round($data['list'][0]['main']['temp']),
    'desc' => formatWeather($raw, $lang, $translations, $icons),
    'lat' => $data['city']['coord']['lat'],
    'lon' => $data['city']['coord']['lon'],
    'forecast' => []
];

// FORECAST
$days = [];

foreach ($data['list'] as $item) {

    $ts = strtotime($item['dt_txt']);
    $dateKey = date('Y-m-d', $ts);

    // берём только 12:00
    if (date('H', $ts) != 12) continue;

    if (isset($days[$dateKey])) continue;

    $raw = $item['weather'][0]['description'];

    $dayEn = date('D', $ts);
    $day = $dayTranslations[$dayEn][$lang] ?? $dayEn;

    $days[$dateKey] = [
        'date' => $day . ' ' . date('d.m', $ts),
        'temp' => round($item['main']['temp']),
        'desc' => formatWeather($raw, $lang, $translations, $icons)
    ];

    if (count($days) >= 5) break;
}

$response['forecast'] = array_values($days);

echo json_encode($response);