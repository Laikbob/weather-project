<?php
// Настройки подключения
$host = "localhost";   // обычно localhost для XAMPP
$dbname = "news_site"; // имя вашей БД
$user = "root";        // стандартный пользователь XAMPP
$pass = "";            // стандартный пароль пустой

// Создание подключения
$conn = new mysqli($host, $user, $pass, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}