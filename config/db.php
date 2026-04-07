    <?php
global $conn;
// config/db.php
// Настройки базы данных
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'weather_db');

// Подключение к базе
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения к базе: " . $conn->connect_error);
}

// Ключ API (пока пустой для разработки)
define('API_KEY', '032145b0ef22d046d99de8752048c3ff');
?>