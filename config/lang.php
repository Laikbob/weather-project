<?php
function getLang() {
    $lang = $_GET['lang'] ?? 'et';
    $available_languages = ['et', 'ru'];

    if (!in_array($lang, $available_languages)) {
        $lang = 'et';
    }

    $text = [
        'et' => [
            'title' => 'Uudiste ja ilmakogu',
            'news' => 'Uudised',
            'search' => 'Otsi...',
            'new' => 'Uuemad ees',
            'old' => 'Vanemad ees',
            'read' => 'Loe edasi',
            'source' => ' Kõik Allikad',
        ],
        'ru' => [
            'title' => 'Cборник новостей и погоды',
            'news' => 'Новости',
            'search' => 'Поиск...',
            'new' => 'Сначала новые',
            'old' => 'Сначала старые',
            'read' => 'Читать',
            'source' => ' Все источники',
        ],
    ];

    return [$lang, $text];
}