<?php
global $conn;
require __DIR__ . '/config/zonedb.php';
require __DIR__ . '/config/lang.php';
require __DIR__ . '/config/rss_fetch.php';

session_start();

$user = $_SESSION['user'] ?? null;
list($lang, $text) = getLang();

// FAVORITES LINKS

$favLinks = [];

if ($user) {
    $stmtFav =    $conn->prepare("SELECT link FROM favorites WHERE user_id=?");
    $stmtFav->bind_param("i", $user['id']);
    $stmtFav->execute();
    $resFav = $stmtFav->get_result();

    while ($row = $resFav->fetch_assoc()) {
        $favLinks[] = $row['link'];
    }
}

// RSS FEEDS

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


// FETCH NEWS
$allNews = [];

foreach ($feeds as $name => $url) {
    $allNews = array_merge($allNews, fetchNews($url, $name));
}

if (!empty($allNews)) {
    usort($allNews, fn($a, $b) => $b['pubDate'] <=> $a['pubDate']);
    $lastNews = $allNews[0]['title'];
} else {
    $lastNews = $allNews[0]['title'] ?? '';
}

// FILTERS

$search   = $_GET['search'] ?? '';
$source   = $_GET['source'] ?? '';
$category = $_GET['category'] ?? '';
$sort     = $_GET['sort'] ?? '';

if ($search) {
    $allNews = array_filter($allNews, function ($item) use ($search) {

        $inText =
                stripos($item['title'], $search) !== false ||
                stripos($item['description'], $search) !== false;

        $inCat = false;

        if (!empty($item['categories'])) {
            foreach ($item['categories'] as $cat) {
                if (stripos($cat, $search) !== false) {
                    $inCat = true;
                    break;
                }
            }
        }

        return $inText || $inCat;
    });
}

if ($source) {
    $allNews = array_filter($allNews, fn($item) => $item['source'] === $source);
}

if ($category) {
    $allNews = array_filter($allNews, function ($item) use ($category) {
        return !empty($item['categories']) && in_array($category, $item['categories']);
    });
}
// ALL CATEGORIES

$allCategories = [];

foreach ($allNews as $item) {
    if (!empty($item['categories'])) {
        foreach ($item['categories'] as $cat) {
            $allCategories[] = $cat;
        }
    }
}

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
            <span><?= $text[$lang]['wel'] ?> 👋 <?= htmlspecialchars($user['username']) ?></span>
            <a href="weather.php?lang=<?= $lang ?>">🌤️<?= $text[$lang]['weather'] ?></a>
            <a href="favorites.php">⭐ <?= $text[$lang]['fav'] ?></a>
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
            <a href="logout.php" class="logout-btn">
                <?= $text[$lang]['out'] ?>
            </a>
        <?php else: ?>
            <a href="weather.php?lang=<?= $lang ?>">🌤️<?= $text[$lang]['weather'] ?></a>
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
            <a href="login.php"><?= $text[$lang]['login'] ?></a>
            <a href="register.php"><?= $text[$lang]['reg'] ?></a>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <h1>📰<?= $text[$lang]['title 2'] ?></h1>

    <!-- SIDEBAR -->
    <aside class="sidebar">

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

        <?php if ($search || $source || $category): ?>
            <a href="?lang=<?= $lang ?>" class="btn reset-all">
                <?= $text[$lang]['reset_all'] ?>
            </a>
        <?php endif; ?>

    </aside>
    <?php if (!empty($search)): ?>
        <div class="search-info">
            <p>
                <?= $text[$lang]['search_result'] ?>:
                <strong>"<?= htmlspecialchars($search) ?>"</strong>
            </p>
        </div>
    <?php endif; ?>

    <!-- NEWS -->
    <div class="news-list">

        <?php foreach ($allNews as $item): ?>

            <div class="news-card" id="news-<?= md5($item['link']) ?>">

                <?php if (!empty($item['image'])): ?>
                    <img src="<?= htmlspecialchars($item['image']) ?>">
                <?php endif; ?>

                <h3><?= htmlspecialchars($item['title']) ?></h3>
                <p><?= htmlspecialchars($item['description']) ?></p>

                <?php if (!empty($item['categories'])): ?>
                    <div>
                        <?php foreach ($item['categories'] as $cat): ?>
                            <span>#<?= htmlspecialchars($cat) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <small><?= date("d.m.Y H:i", $item['pubDate']) ?></small><br>
                <span><?= htmlspecialchars($item['source']) ?></span><br>

                <a href="<?= htmlspecialchars($item['link']) ?>" target="_blank">
                    <?= $text[$lang]['read'] ?>
                </a>

                <br>
                <!-- FAVORITES -->
                <?php if ($user): ?>

                    <?php if (in_array($item['link'], $favLinks)): ?>

                        <button disabled>⭐ <?= $text[$lang]['fav 1'] ?></button>

                        <form method="POST" action="config/delete/delete_favorite.php" style="display:inline;">
                            <input type="hidden" name="link" value="<?= htmlspecialchars($item['link']) ?>">
                            <button>❌ <?= $text[$lang]['del'] ?></button>
                        </form>

                    <?php else: ?>
                        <form method="POST" action="favorite.php" style="display:inline;">

                            <input type="hidden" name="lang" value="<?= $lang ?>">

                            <input type="hidden" name="title" value="<?= htmlspecialchars($item['title']) ?>">
                            <input type="hidden" name="link" value="<?= htmlspecialchars($item['link']) ?>">
                            <input type="hidden" name="description" value="<?= htmlspecialchars($item['description']) ?>">
                            <input type="hidden" name="image" value="<?= htmlspecialchars($item['image']) ?>">
                            <input type="hidden" name="pubDate" value="<?= $item['pubDate'] ?>">
                            <input type="hidden" name="categories" value="<?= htmlspecialchars(json_encode($item['categories'])) ?>">
                            <input type="hidden" name="source" value="<?= htmlspecialchars($item['source']) ?>">

                            <button type="submit">⭐ <?= $text[$lang]['category'] ?></button>
                        </form>

                    <?php endif; ?>

                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    </div>

</div>

</body>
<script src="assets/js/index.js"></script>
</html>