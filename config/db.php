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
?>