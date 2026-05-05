<?php
session_start();

require __DIR__ . '/config/zonedb.php';
require __DIR__ . '/config/lang.php';

list($lang, $text) = getLang();

$user = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title>Weather Project</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="assets/css/weather.css">
</head>
<body>

<!-- HEADER -->
<div class="header-bar">
    <div class="logo">
        <?= $text[$lang]['title'] ?? 'Weather' ?>
    </div>

    <div class="user-panel">
        <?php if ($user): ?>
        <span>
            <?= $text[$lang]['wel'] ?? 'Hi' ?> 👋
            <?= $user ? htmlspecialchars($user['username']) : '' ?>
        </span>

        <a href="index.php">📰 <?= $text[$lang]['news'] ?? 'News' ?></a>
        <a href="favorites.php">⭐ <?= $text[$lang]['fav'] ?? 'Favorite' ?></a>

        <!-- LANGUAGE DROPDOWN -->
        <div class="lang-switch">
            <div class="dropdown">
                <button class="dropbtn">🌐 <?= strtoupper($lang) ?></button>

                <div class="dropdown-content">
                    <a href="?lang=et" class="<?= $lang === 'et' ? 'active' : '' ?>">
                        🇪🇪 Eesti
                    </a>
                    <a href="?lang=ru" class="<?= $lang === 'ru' ? 'active' : '' ?>">
                        🇷🇺 Русский
                    </a>
                </div>
                <a href="logout.php" class="logout-btn"><?= $text[$lang]['out'] ?? 'Logout' ?></a>
            </div>
        </div>
        <?php else: ?>
            <a href="index.php">📰 <?= $text[$lang]['news'] ?? 'News' ?></a>
            <!-- LANGUAGE DROPDOWN -->
            <div class="lang-switch">
                <div class="dropdown">
                    <button class="dropbtn">🌐 <?= strtoupper($lang) ?></button>

                    <div class="dropdown-content">
                        <a href="?lang=et" class="<?= $lang === 'et' ? 'active' : '' ?>">
                            🇪🇪 Eesti
                        </a>
                        <a href="?lang=ru" class="<?= $lang === 'ru' ? 'active' : '' ?>">
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

<!-- TITLE -->
<h1>🌤️<?= $text[$lang]['weather'] ?></h1>

<!-- SEARCH -->
<form id="searchForm">
    <input type="text" id="cityInput" placeholder="<?= $text[$lang]['linn 1'] ?? 'City' ?>">
    <button type="submit"><?= $text[$lang]['search 1'] ?? 'Search' ?></button>
    <button type="button" id="geoBtn">📍 <?= $text[$lang]['loc'] ?? 'Location' ?></button>
</form>

<!-- WEATHER -->
<div class="weather-box">
    <h2><?= $text[$lang]['weather 1'] ?? 'Weather' ?></h2>

    <p><strong><?= $text[$lang]['city'] ?>:</strong> <span id="city">--</span></p>
    <p><strong><?= $text[$lang]['tem'] ?>:</strong> <span id="temp">--</span> °C</p>
    <p><strong><?= $text[$lang]['des'] ?>:</strong> <span id="desc">--</span></p>
</div>

<!-- FORECAST -->
<div id="forecast"></div>

<!-- MAP -->
<div id="map"></div>

<!-- SCRIPTS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="assets/js/weather.js"></script>

<script>
    const currentLang = "<?= $lang ?>";
</script>

</body>
</html>