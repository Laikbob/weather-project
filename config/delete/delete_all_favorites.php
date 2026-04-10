<?php
global $conn;
session_start();
require "../db.php";

if (!isset($_SESSION['user'])) {
    die("Нет доступа");
}

$user_id = $_SESSION['user']['id'];

$conn->query("DELETE FROM favorites WHERE user_id=$user_id");

// Пересчёт AUTO_INCREMENT
$result = $conn->query("SELECT IFNULL(MAX(id),0) as max_id FROM favorites");
$row = $result->fetch_assoc();
$next_id = $row['max_id'] + 1;
$conn->query("ALTER TABLE favorites AUTO_INCREMENT = $next_id");

header("Location: ../../favorites.php");
exit;