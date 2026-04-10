<?php
global $conn;
session_start();
require "../db.php";

if (!isset($_SESSION['user'])) {
    die("Нет доступа");
}

$user_id = $_SESSION['user']['id'];
$link = $_POST['link'] ?? '';

if ($link) {
    $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id=? AND link=?");
    $stmt->bind_param("is", $user_id, $link);
    $stmt->execute();

    // Пересчёт AUTO_INCREMENT
    $result = $conn->query("SELECT IFNULL(MAX(id),0) as max_id FROM favorites");
    $row = $result->fetch_assoc();
    $next_id = $row['max_id'] + 1;
    $conn->query("ALTER TABLE favorites AUTO_INCREMENT = $next_id");
}

$return = $_POST['return_url'] ?? '../../favorites.php';

// защита: разрешаем только локальные пути
if (strpos($return, 'http') === 0) {
    $return = '../../favorites.php';
}

header("Location: " . $return);
exit;
