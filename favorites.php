<?php
global $conn;
session_start();
require "config/zonedb.php";
require "config/lang.php";

if (empty($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['id'];

list($lang, $text) = getLang();

// ===== ФИЛЬТРЫ =====
$search = $_GET['search'] ?? '';
$sourceFilter = $_GET['source'] ?? '';
$categoryFilter = $_GET['category'] ?? '';

// ===== ЗАПРОС ИЗБРАННОГО =====
$stmt = $conn->prepare("
    SELECT id, title, link, description, image, pubDate, categories, source
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
    $allFavorites[] = $row;

    if (!empty($row['source']) && !in_array($row['source'], $sources)) {
        $sources[] = $row['source'];
    }

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

sort($sources);
sort($allCategories);

// ===== ФИЛЬТРАЦИЯ =====
if ($search) {
    $allFavorites = array_filter($allFavorites, function($item) use ($search) {
        return stripos($item['title'], $search) !== false
                || stripos($item['description'], $search) !== false;
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
        return is_array($cats) && in_array($categoryFilter, $cats);
    });
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $text[$lang]['fav'] ?? 'Favorites' ?></title>
    <link rel="stylesheet" href="assets/css/favorites.css">
</head>
<body>

<div class="header-bar">
    <div class="logo"><?= $text[$lang]['title']?></div>

    <div class="user-panel">
        <span><?= $text[$lang]['wel'] ?>👋 <?= htmlspecialchars($user['username']) ?></span>
        <a href="index.php">📰 <?= $text[$lang]['news']?></a>
        <a href="weather.php">🌤️ <?= $text[$lang]['weather']?></a>
        <div class="lang-switch">
            <div class="dropdown">
                <button class="dropbtn">
                    🌐 <?= strtoupper($lang) ?>
                </button>
                <div class="dropdown-content">
                    <a href="?lang=et" class="<?= $lang == 'et' ? 'active' : '' ?>">
                        🇪🇪 Eesti
                    </a>
                    <a href="?lang=ru" class="<?= $lang == 'ru' ? 'active' : '' ?>">
                        🇷🇺 Русский
                    </a>
                </div>
            </div>
        </div>
        <a href="logout.php" class="logout-btn"><?= $text[$lang]['out']?></a>
    </div>
</div>

<div class="container">
    <h1>⭐ <?= $text[$lang]['fav'] ?></h1>
    <div class="layout">

        <!-- SIDEBAR -->
        <div class="sidebar-content">

            <!-- поиск -->
            <div class="search-box">
                <form method="GET">
                    <input type="hidden" name="lang" value="<?= $lang ?>">

                    <input type="text" name="search"
                           placeholder="<?= $text[$lang]['search'] ?>"
                           value="<?= htmlspecialchars($search) ?>">

                    <button type="submit" class="btn">🔍 <?= $text[$lang]['search 1'] ?></button>

                    <?php if ($search): ?>
                        <button type="submit" name="search" value="" class="btn reset-search">
                            ✖ <?= $text[$lang]['search off'] ?>
                        </button>
                    <?php endif; ?>
                </form>
            </div>

            <!-- SOURCES -->
            <h4><?= $text[$lang]['source'] ?></h4>

            <div class="filter-buttons">
                <a href="favorites.php?lang=<?= $lang ?>&search=<?= urlencode($search) ?>"
                   class="btn <?= $sourceFilter==''?'active':'' ?>">
                    <?= $text[$lang]['all'] ?>
                </a>

                <?php foreach ($sources as $s): ?>
                    <a href="favorites.php?source=<?= urlencode($s) ?>&lang=<?= $lang ?>&search=<?= urlencode($search) ?>"
                       class="btn <?= $sourceFilter==$s?'active':'' ?>">
                        <?= htmlspecialchars($s) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- CATEGORIES -->
            <h4 class="toggle-cats"><?= $text[$lang]['category'] ?><span class="arrow">▼</span></h4>

            <div class="filter-buttons categories-block">

                <a href="favorites.php?lang=<?= $lang ?>&search=<?= urlencode($search) ?>&source=<?= urlencode($sourceFilter) ?>"
                   class="btn <?= $categoryFilter==''?'active':'' ?>">
                    <?= $text[$lang]['all'] ?>
                </a>

                <?php foreach ($allCategories as $cat): ?>
                    <a href="favorites.php?category=<?= urlencode($cat) ?>&lang=<?= $lang ?>&search=<?= urlencode($search) ?>&source=<?= urlencode($sourceFilter) ?>"
                       class="btn <?= $categoryFilter==$cat?'active':'' ?>">
                        <?= htmlspecialchars($cat) ?>
                    </a>
                <?php endforeach; ?>

            </div>

            <!-- RESET -->
            <a href="favorites.php?lang=<?= $lang ?>" class="btn reset-all">
                <?= $text[$lang]['reset_all'] ?>
            </a>

            <a href="config/delete/delete_all_favorites.php?lang=<?= $lang ?>"
               class="btn delete-btn"
               onclick="return confirm('<?= $text[$lang]['confirm'] ?>');">
                <?= $text[$lang]['del_all'] ?>
            </a>
        </div>

        <!-- FAVORITES LIST -->
        <div class="news-list">

            <?php if (empty($allFavorites)): ?>
                <p><?= $text[$lang]['no fav'] ?></p>
            <?php else: ?>

                <?php foreach ($allFavorites as $row): ?>
                    <div class="news-card">

                        <?php if (!empty($row['image'])): ?>
                            <img src="<?= htmlspecialchars($row['image']) ?>">
                        <?php endif; ?>

                        <h3><?= htmlspecialchars($row['title']) ?></h3>

                        <?php if (!empty($row['description'])): ?>
                            <p><?= htmlspecialchars($row['description']) ?></p>
                        <?php endif; ?>

                        <?php
                        if (!empty($row['categories'])):
                            $cats = json_decode($row['categories'], true);
                            if (is_array($cats)):
                                ?>
                                <div class="categories">
                                    <?php foreach ($cats as $cat): ?>
                                        <span class="category">#<?= htmlspecialchars($cat) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; endif; ?>

                        <?php if (!empty($row['pubDate'])): ?>
                            <?php
                            $date = is_numeric($row['pubDate'])
                                    ? $row['pubDate']
                                    : strtotime($row['pubDate']);
                            ?>
                            <small><?= date("d.m.Y H:i", $date) ?></small><br>
                        <?php endif; ?>

                        <?php if (!empty($row['source'])): ?>
                            <span class="source"><?= htmlspecialchars($row['source']) ?></span><br>
                        <?php endif; ?>

                        <a href="<?= htmlspecialchars($row['link']) ?>" target="_blank" class="read-link">
                            <?= $text[$lang]['read'] ?>
                        </a>

                        <form method="POST" action="config/delete/delete_favorite.php"
                              class="delete-form"
                              onsubmit="return confirm('Delete this item?')">

                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button class="delete-btn">❌ <?= $text[$lang]['del'] ?></button>

                        </form>

                    </div>
                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const title = document.querySelector(".toggle-cats");
            const block = document.querySelector(".categories-block");

            title.addEventListener("click", function () {
                block.classList.toggle("active");
                title.classList.toggle("active");
            });

        });
    </script>
</div>

</body>
<script src="assets/js/favorites.js"></script>
</html>