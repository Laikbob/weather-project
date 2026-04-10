<?php
global $conn;
session_start();
require "config/db.php";
require __DIR__ . '/config/lang.php';
list($lang, $text) = getLang();

if ($_POST) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // 🔍 Проверяем существует ли пользователь
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo $text[$lang]['not-login'];
    } else {
        // ✅ Добавляем нового пользователя
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();

        echo "✅ Регистрация успешна!";
    }
}
?>

<form method="POST">
    <input name="username" placeholder="Логин" required>
    <input name="password" type="password" placeholder="Пароль" required>
    <button>Регистрация</button>
</form>