<?php
global $conn;
session_start();
require "config/db.php";
require "config/lang.php";

if (!isset($_SESSION['user'])) {
    die("Нет доступа");
}

$user = $_SESSION['user'];
list($lang, $text) = getLang();
$user_id = $user['id'];

// ===== Фильтры =====
$search = $_GET['search'] ?? '';
$sourceFilter = $_GET['source'] ?? '';
$categoryFilter = $_GET['category'] ?? '';

// ===== Получаем избранное =====
$stmt = $conn->prepare("
    SELECT title, link, description, image, pubDate, categories, source
    FROM favorites
    WHERE user_id=?
    ORDER BY id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$allFavorites = [];
$sources = [];
$allCategories = [];

while ($row = $result->fetch_assoc()) {
    // Добавляем в массив
    $allFavorites[] = $row;

    // Источники
    if (!empty($row['source']) && !in_array($row['source'], $sources)) {
        $sources[] = $row['source'];
    }

    // Категории
    if (!empty($row['categories'])) {
        $cats = json_decode($row['categories'], true);
        if (is_array($cats)) {
            foreach ($cats as $cat) {
                if (!in_array($cat, $allCategories)) {
                    $allCategories[] = $cat;
                }
            }
        }
    }
}

// Сортируем категории и источники
sort($sources);
sort($allCategories);

// ===== Фильтрация =====
if ($search) {
    $allFavorites = array_filter($allFavorites, function($item) use ($search) {
        return stripos($item['title'], $search) !== false || stripos($item['description'], $search) !== false;
    });
}

if ($sourceFilter) {
    $allFavorites = array_filter($allFavorites, function($item) use ($sourceFilter) {
        return $item['source'] === $sourceFilter;
    });
}

if ($categoryFilter) {
    $allFavorites = array_filter($allFavorites, function($item) use ($categoryFilter) {
        if (empty($item['categories'])) return false;
        $cats = json_decode($item['categories'], true);
        return in_array($categoryFilter, $cats);
    });
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $text[$lang]['fav'] ?></title>
    <link rel="stylesheet" href="assets/css/favorites.css">
</head>
<body>
<div class="header-bar">
    <div class="logo"><?= $text[$lang]['title 1'] ?></div>
    <div class="user-panel">
        <span>Tere👋 <?= htmlspecialchars($user['username']) ?></span>
        <a href="index.php">📰<?= $text[$lang]['news'] ?></a>
        <a href="logout.php"><?= $text[$lang]['out'] ?></a>
    </div>
</div>

<div class="container">
    <h1>⭐ <?= $text[$lang]['fav'] ?></h1>

    <div class="lang-switch">
        <a href="?lang=et">🇪🇪 Eesti</a>
        <a href="?lang=ru">🇷🇺 Русский</a>
    </div>

    <!-- меню -->
    <nav class="top-nav">
        <ul>
            <li><a href="index.php?lang=<?= $lang ?>"><?= $text[$lang]['news'] ?></a></li>
            <li><a href="weather.php?lang=<?= $lang ?>"><?= $text[$lang]['weather'] ?></a></li>
        </ul>
    </nav>

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <!-- поиск -->
        <form method="GET">
            <input type="hidden" name="lang" value="<?= $lang ?>">

            <!-- Поле поиска -->
            <input type="text" name="search"
                   placeholder="<?= $text[$lang]['search'] ?>"
                   value="<?= htmlspecialchars($search) ?>">

            <!-- Кнопка поиска -->
            <button type="submit" class="btn">🔍 <?= $text[$lang]['search 1'] ?></button>

            <!-- Кнопка сброса -->
            <?php if ($search): ?>
                <button type="submit" name="search" value="" class="btn reset-search">
                    ✖ <?= $text[$lang]['search off'] ?>
                </button>
            <?php endif; ?>
        </form>

        <!-- источники -->
        <h4><?= $text[$lang]['source'] ?></h4>
        <div class="filter-buttons">
            <a href="?lang=<?= $lang ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($categoryFilter) ?>"
               class="btn <?= $sourceFilter==''?'active':'' ?>"><?= $text[$lang]['all'] ?></a>

            <?php foreach ($sources as $s): ?>
                <a href="?source=<?= urlencode($s) ?>&lang=<?= $lang ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($categoryFilter) ?>"
                   class="btn <?= $sourceFilter==$s?'active':'' ?>">
                    <?= htmlspecialchars($s) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- категории -->
        <h4 class="toggle-cats"><?= $text[$lang]['category'] ?> <span class="arrow">▼</span></h4>
        <div class="filter-buttons categories-block">
            <a href="?lang=<?= $lang ?>&search=<?= urlencode($search) ?>&source=<?= urlencode($sourceFilter) ?>"
               class="btn <?= $categoryFilter==''?'active':'' ?>">
                <?= $text[$lang]['all'] ?>
            </a>

            <?php foreach ($allCategories as $cat): ?>
                <a href="?category=<?= urlencode($cat) ?>&lang=<?= $lang ?>&search=<?= urlencode($search) ?>&source=<?= urlencode($sourceFilter) ?>"
                   class="btn <?= $categoryFilter==$cat?'active':'' ?>">
                    <?= htmlspecialchars($cat) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <a href="?lang=<?= $lang ?>" class="btn reset-all">
            <?= $text[$lang]['reset_all'] ?>
        </a>

    </aside>

    <?php if (empty($allFavorites)): ?>
        <p><?= $text[$lang]['fav'] ?> пустой.</p>
    <?php else: ?>
        <?php foreach ($allFavorites as $row): ?>
            <div class="news-card" id="news-<?= md5($row['link']) ?>">
                <?php if (!empty($row['image'])): ?>
                    <img src="<?= htmlspecialchars($row['image']) ?>" alt="news image">
                <?php endif; ?>

                <h3><?= htmlspecialchars($row['title']) ?></h3>
                <?php if (!empty($row['description'])): ?>
                    <p><?= htmlspecialchars($row['description']) ?></p>
                <?php endif; ?>

                <?php if (!empty($row['categories'])):
                    $cats = json_decode($row['categories'], true);
                    if (is_array($cats)): ?>
                        <div class="categories">
                            <?php foreach ($cats as $cat): ?>
                                <span class="category"><?= htmlspecialchars($cat) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; endif; ?>

                <?php if (!empty($row['pubDate']) && $row['pubDate'] > 0): ?>
                    <small><?= date("d.m.Y H:i", $row['pubDate']) ?></small><br>
                <?php endif; ?>

                <?php if (!empty($row['source'])): ?>
                    <span class="source"><?= htmlspecialchars($row['source']) ?></span><br>
                <?php endif; ?>

                <a href="<?= htmlspecialchars($row['link']) ?>" target="_blank" rel="noopener noreferrer">
                    <?= $text[$lang]['read'] ?>
                </a>

                <form method="POST" action="config/delete/delete_favorite.php">
                    <input type="hidden" name="link" value="<?= htmlspecialchars($row['link']) ?>">
                    <button class="delete-btn">❌ <?= $text[$lang]['del'] ?></button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
<script src="assets/js/index.js"></script>
</html>