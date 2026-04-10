<?php
require __DIR__ . '/config/db.php';
global $conn;
require __DIR__ . '/config/lang.php';
list($lang, $text) = getLang();
require __DIR__ . '/config/rss_fetch.php';

session_start();
$user = $_SESSION['user'] ?? null;

$favLinks = [];

if ($user) {
    $stmtFav = $conn->prepare("SELECT link FROM favorites WHERE user_id=?");
    $stmtFav->bind_param("i", $user['id']);
    $stmtFav->execute();
    $resultFav = $stmtFav->get_result();
    while ($row = $resultFav->fetch_assoc()) {
        $favLinks[] = $row['link'];
    }
}

// ===== RSS =====
if ($lang === "ru") {
    $feeds = [
            "ERR" => "https://rus.err.ee/rss",
            "Postimees" => "https://rus.postimees.ee/rss"
    ];
} else {
    $feeds = [
            "ERR" => "https://www.err.ee/rss",
            "Postimees" => "https://www.postimees.ee/rss",
            "Õhtuleht" => "https://www.ohtuleht.ee/rss"
    ];
}
$sources = array_keys($feeds);

// ===== NEWS =====
$allNews = [];
foreach ($feeds as $name => $url) {
    $allNews = array_merge($allNews, fetchNews($url, $name));
}
usort($allNews, function($a, $b) {
    return $b['pubDate'] <=> $a['pubDate']; // новые сверху
});

// ===== FILTERS =====
$search = $_GET['search'] ?? '';
$source = $_GET['source'] ?? '';
$sort   = $_GET['sort'] ?? 'new';
$category = $_GET['category'] ?? '';
if ($search) {
    $allNews = array_filter($allNews, function($item) use ($search) {

        $searchLower = mb_strtolower($search);

        // ===== ПОИСК В ТЕКСТЕ =====
        $inText =
                stripos($item['title'], $search) !== false ||
                stripos($item['description'], $search) !== false;

        // ===== ПОИСК ПО КАТЕГОРИИ =====
        $inCategory = false;
        if (!empty($item['categories'])) {
            foreach ($item['categories'] as $cat) {
                if (stripos($cat, $search) !== false) {
                    $inCategory = true;
                    break;
                }
            }
        }

        return $inText || $inCategory;
    });
}

// источник
if ($source) {
    $allNews = array_filter($allNews, function($item) use ($source) {
        return $item['source'] === $source;
    });
}

if ($category) {
    $allNews = array_filter($allNews, function($item) use ($category) {
        if (empty($item['categories'])) return false;
        return in_array($category, $item['categories']);
    });
}

$allCategories = [];

foreach ($allNews as $item) {
    if (!empty($item['categories'])) {
        foreach ($item['categories'] as $cat) {
            $allCategories[] = $cat;
        }
    }
}

// убираем дубликаты
$allCategories = array_unique($allCategories);
sort($allCategories);
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $text[$lang]['title'] ?></title>
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<!-- HEADER -->
<div class="header-bar">
    <div class="logo"><?= $text[$lang]['title'] ?></div>

    <div class="user-panel">
        <?php if ($user): ?>
            <span>Tere👋 <?= htmlspecialchars($user['username']) ?></span>
            <a href="favorites.php">⭐<?= $text[$lang]['fav'] ?></a>
            <a href="logout.php" class="logout-btn"><?= $text[$lang]['out'] ?></a>
        <?php else: ?>
            <a href="login.php"><?= $text[$lang]['login'] ?></a>
            <a href="register.php" class="register-btn"><?= $text[$lang]['reg'] ?></a>
        <?php endif; ?>
    </div>
</div>

<div class="container">

    <h1><?= $text[$lang]['title 2'] ?></h1>

    <!-- язык -->
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

    <h2><?= $text[$lang]['filters'] ?></h2>

    <div class="layout">

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
                <a href="?lang=<?= $lang ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>"
                   class="btn <?= $source==''?'active':'' ?>"><?= $text[$lang]['all'] ?></a>

                <?php foreach ($sources as $s): ?>
                    <a href="?source=<?= urlencode($s) ?>&lang=<?= $lang ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>"
                       class="btn <?= $source==$s?'active':'' ?>">
                        <?= htmlspecialchars($s) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- категории -->
            <h4 class="toggle-cats"><?= $text[$lang]['category'] ?><span class="arrow">▼</span></h4>
            <div class="filter-buttons categories-block">
                <a href="?lang=<?= $lang ?>&search=<?= urlencode($search) ?>&source=<?= urlencode($source) ?>&sort=<?= $sort ?>"
                   class="btn <?= $category==''?'active':'' ?>">
                    <?= $text[$lang]['all'] ?>
                </a>

                <?php foreach ($allCategories as $cat): ?>
                    <a href="?category=<?= urlencode($cat) ?>&lang=<?= $lang ?>&search=<?= urlencode($search) ?>&source=<?= urlencode($source) ?>&sort=<?= $sort ?>"
                       class="btn <?= $category==$cat?'active':'' ?>">
                        <?= htmlspecialchars($cat) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <a href="?lang=<?= $lang ?>" class="btn reset-all">
                 <?= $text[$lang]['reset_all'] ?>
            </a>

        <!-- CONTENT -->
        <main class="content">

            <?php foreach($allNews as $item): ?>
                <div class="news-card" id="news-<?= md5($item['link']) ?>">

                    <?php if($item['image']): ?>
                        <img src="<?= $item['image'] ?>">
                    <?php endif; ?>

                    <h3><?= $item['title'] ?></h3>
                    <p><?= $item['description'] ?></p>

                    <?php if (!empty($item['categories'])): ?>
                        <div class="categories">
                            <?php foreach ($item['categories'] as $cat): ?>
                                <span class="category"><?= htmlspecialchars($cat) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <small><?= date("d.m.Y H:i", $item['pubDate']) ?></small><br>
                    <span class="source"><?= $item['source'] ?></span><br>

                    <a href="<?= $item['link'] ?>" target="_blank">
                        <?= $text[$lang]['read'] ?>
                    </a>

                    <?php if ($user): ?>

                        <?php if (in_array($item['link'], $favLinks)): ?>

                            <!-- Уже в избранном -->
                            <button disabled>⭐ <?= $text[$lang]['fav 1'] ?></button>

                            <!-- Удаление -->
                            <form method="POST" action="config/delete/delete_favorite.php" style="display:inline;">
                                <input type="hidden" name="link" value="<?= htmlspecialchars($item['link']) ?>">
                                <input type="hidden" name="return_url" value="<?= $_SERVER['REQUEST_URI'] ?>">
                                <button class="delete-btn">❌ <?= $text[$lang]['del'] ?></button>
                            </form>

                        <?php else: ?>

                            <!-- Добавление -->
                            <form method="POST" action="favorite.php" style="display:inline;">
                                <input type="hidden" name="title" value="<?= htmlspecialchars($item['title']) ?>">
                                <input type="hidden" name="link" value="<?= $item['link'] ?>">
                                <input type="hidden" name="description" value="<?= htmlspecialchars($item['description']) ?>">
                                <input type="hidden" name="image" value="<?= htmlspecialchars($item['image']) ?>">
                                <input type="hidden" name="pubDate" value="<?= $item['pubDate'] ?>">
                                <input type="hidden" name="categories" value="<?= htmlspecialchars(json_encode($item['categories'])) ?>">
                                <input type="hidden" name="return_url"
                                       value="<?= $_SERVER['REQUEST_URI'] . '#news-' . md5($item['link']) ?>">
                                <input type="hidden" name="source" value="<?= htmlspecialchars($item['source']) ?>">

                                <button>⭐ <?= $text[$lang]['fav'] ?></button>
                            </form>

                        <?php endif; ?>

                    <?php endif; ?>

                </div>
            <?php endforeach; ?>

        </main>

    </div> <!-- layout -->

</div> <!-- container -->

</body>
<script src="assets/js/index.js"></script>
</html>