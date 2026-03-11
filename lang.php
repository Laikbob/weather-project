<?php
// lang.php

// Определяем язык: GET-параметр или по умолчанию эстонский
$lang = $_GET['lang'] ?? 'et';
$available_languages = ['et', 'ru'];

if (!in_array($lang, $available_languages)) {
    $lang = 'et';
}

// Массив переводов
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
?>