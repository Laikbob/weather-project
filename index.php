<?php
// -----------------------------
// lang.php — переводы и язык
// -----------------------------
$text = [
    'et' => [
        'title' => 'Ilmajaam',
        'news' => 'Uudised',
        'select_language' => 'Vali keel',
    ],
    'ru' => [
        'title' => 'Погодная станция',
        'news' => 'Новости',
        'select_language' => 'Выберите язык',
    ],
];

// Определяем язык: GET-параметр или по умолчанию эстонский
$lang = $_GET['lang'] ?? 'et';
$available_languages = ['et', 'ru'];
if (!in_array($lang, $available_languages)) {
    $lang = 'et';
}

// -----------------------------
// Подключаем функцию для вывода карточки новости
// -----------------------------
include __DIR__ . "/news/news_card.php";

// -----------------------------
// RSS-каналы
// -----------------------------
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

// -----------------------------
// Функция загрузки новостей из RSS
// -----------------------------
function fetchNews($url, $limit = 10) {
    $rss = @simplexml_load_file($url);
    $items = [];
    $count = 0;

    if ($rss && isset($rss->channel->item)) {
        foreach ($rss->channel->item as $item) {
            $items[] = [
                'title' => (string)($item->title ?? ''),
                'link' => (string)($item->link ?? ''),
                'pubDate' => isset($item->pubDate) ? strtotime($item->pubDate) : time(),
                'description' => (string)($item->description ?? ''),
                'category' => isset($item->category) ? (string)$item->category : '',
                'image' => isset($item->enclosure['url']) ? (string)$item->enclosure['url'] : ''
            ];
            $count++;
            if ($count >= $limit) break;
        }
    }

    return $items;
}

// -----------------------------
// Загружаем и сортируем все новости
// -----------------------------
$allNews = [];
foreach ($feeds as $name => $url) {
    $news = fetchNews($url, 10);
    $allNews = array_merge($allNews, $news);
}

// Сортировка по дате (новые сверху)
usort($allNews, function($a, $b) {
    return $b['pubDate'] - $a['pubDate'];
});
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($text[$lang]['title']) ?></title>
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<div class="container">
    <h1><?= htmlspecialchars($text[$lang]['title']) ?></h1>

    <div class="lang-switch">
        <a href="?lang=et">🇪🇪 Eesti</a> |
        <a href="?lang=ru">🇷🇺 Русский</a>
    </div>

    <h2><?= htmlspecialchars($text[$lang]['news']) ?></h2>

    <div class="news-items">
        <?php
        // Выводим новости через renderNewsCard
        foreach ($allNews as $item) {
            renderNewsCard(
                $item['title'] ?? '',
                $item['link'] ?? '',
                $item['pubDate'] ?? time(),
                $item['description'] ?? '',
                $item['category'] ?? '',
                $item['image'] ?? ''
            );
        }
        ?>
    </div>
</div>

</body>
</html>