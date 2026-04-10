<?php
global $conn;
session_start();
require "config/db.php";

if (!isset($_SESSION['user'])) {
    die("Сначала войдите");
}

$user_id = $_SESSION['user']['id'];

// Получаем данные новости из POST
$title       = $_POST['title'] ?? '';
$link        = $_POST['link'] ?? '';
$description = $_POST['description'] ?? '';
$image       = $_POST['image'] ?? '';
$pubDate     = $_POST['pubDate'] ?? '';
$categories  = $_POST['categories'] ?? '';
$source = $_POST['source'] ?? '';

if ($title && $link) {
    // Проверка на дубликаты
    $stmtCheck = $conn->prepare("SELECT id FROM favorites WHERE user_id=? AND link=?");
    $stmtCheck->bind_param("is", $user_id, $link);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows == 0) {
        // Добавляем полную новость
        $stmt = $conn->prepare("
            INSERT INTO favorites 
            (user_id, title, link, description, image, pubDate, categories, source) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "issssiss",
            $user_id,
            $title,
            $link,
            $description,
            $image,
            $pubDate,
            $categories,
            $source
        );
        $stmt->execute();
    }
}

// Возврат на страницу
$return = $_POST['return_url'] ?? 'index.php';
header("Location: $return");
exit;