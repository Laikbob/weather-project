<?php
// news/news_card.php
function renderNewsCard($title, $link, $pubDate, $description = '', $category = '', $image = '') {
echo "<div class='news-item'>";

    // Картинка
    if ($image) {
    echo "<img src='" . htmlspecialchars($image) . "' alt='' style='max-width:100%; border-radius:8px; margin-bottom:10px;'>";
    }

    // Заголовок
    echo "<h3><a href='" . htmlspecialchars($link) . "' target='_blank'>" . htmlspecialchars($title) . "</a></h3>";

    // Описание
    if ($description) {
    echo "<p>" . htmlspecialchars($description) . "</p>";
    }

    // Категория и дата
    if ($category || $pubDate) {
    echo "<small>";
        if ($category) echo "Категория: " . htmlspecialchars($category) . " | ";
        if ($pubDate) echo "Дата: " . date('d.m.Y H:i', $pubDate);
        echo "</small>";
    }

    echo "</div>";
}
?>