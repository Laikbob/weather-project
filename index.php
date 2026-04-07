<?php
require __DIR__ . '/config/lang.php';
list($lang, $text) = getLang();
require __DIR__ . '/config/rss_fetch.php';

// ===== RSS ИСТОЧНИКИ =====
if ($lang === "ru") {
    $feeds = [
            "ERR" => "https://rus.err.ee/rss",
            "Postimees" => "https://rus.postimees.ee/rss"
    ];
    $sources = array_keys($feeds);
} else {
    $feeds = [
            "ERR" => "https://www.err.ee/rss",
            "Postimees" => "https://www.postimees.ee/rss",
            "Õhtuleht" => "https://www.ohtuleht.ee/rss"
    ];
    $sources = array_keys($feeds);
}

// ===== СОБИРАЕМ ВСЕ НОВОСТИ =====
$allNews = [];

foreach ($feeds as $name => $url) {
    $allNews = array_merge($allNews, fetchNews($url, $name));
}


// ===== ПОИСК =====
$search = $_GET['search'] ?? '';

if ($search) {
    $allNews = array_filter($allNews, function($item) use ($search) {
        return stripos($item['title'], $search) !== false ||
                stripos($item['description'], $search) !== false;
    });
}
$source = $_GET['source'] ?? '';

if ($source) {
    $allNews = array_filter($allNews, function($item) use ($source) {
        return $item['source'] === $source;
    });
}

// ===== СОРТИРОВКА =====
$sort = $_GET['sort'] ?? 'new';

usort($allNews, function($a, $b) use ($sort) {
    if ($sort === 'old') {
        return $a['pubDate'] - $b['pubDate'];
    }
    return $b['pubDate'] - $a['pubDate'];
});

?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $text[$lang]['title'] ?></title>

    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<div class="container">

    <h1><?= $text[$lang]['title'] ?></h1>

    <div class="lang-switch">
        <a href="?lang=et">🇪🇪 Eesti</a>
        <a href="?lang=ru">🇷🇺 Русский</a>
    </div>

    <h2><?= $text[$lang]['news'] ?></h2>

    <!-- ПОИСК И СОРТИРОВКА -->
    <form method="GET" class="controls">

        <input type="hidden" name="lang" value="<?= $lang ?>">

        <input type="text" name="search"
               placeholder="<?= $text[$lang]['search'] ?>"
               value="<?= htmlspecialchars($search) ?>">

        <select name="source">
            <option value=""><?= $text[$lang]['source'] ?></option>

            <?php foreach ($sources as $s): ?>
                <option value="<?= $s ?>"
                        <?= $source == $s ? 'selected' : '' ?>>

                    <?= htmlspecialchars($s) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="sort">
            <option value="new" <?= $sort=='new'?'selected':'' ?>>
                <?= $text[$lang]['new'] ?>
            </option>
            <option value="old" <?= $sort=='old'?'selected':'' ?>>
                <?= $text[$lang]['old'] ?>
            </option>
        </select>

        <button>OK</button>

    </form>


    <!-- НОВОСТИ -->
    <?php foreach($allNews as $item): ?>

        <div class="news-card">

            <?php if($item['image']): ?>
                <img src="<?= $item['image'] ?>">
            <?php endif; ?>

            <h3><?= $item['title'] ?></h3>

            <p><?= $item['description'] ?></p>

            <small><?= date("d.m.Y H:i", $item['pubDate']) ?></small><br>

            <span class="source"><?= $item['source'] ?></span><br>

            <a href="<?= $item['link'] ?>" target="_blank">
                <?= $text[$lang]['read'] ?>
            </a>

        </div>

    <?php endforeach; ?>

</div>

</body>
</html>