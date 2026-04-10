<?php
function getLang() {
    $lang = $_GET['lang'] ?? 'et';
    $available_languages = ['et', 'ru'];

    if (!in_array($lang, $available_languages)) {
        $lang = 'et';
    }

    $text = [
        'et' => [
            'title' => '📰 Uudiste ja 🌤️ Ilmakogu',
            'title 2' => 'Uudiste kogu',
            'news' => 'Uudised',
            'search' => 'Otsi...',
            'new' => 'Uuemad ees',
            'old' => 'Vanemad ees',
            'read' => 'Loe edasi',
            'source' => ' Kõik Allikad',
            'weather' => ' Ilm',
            'not-login' => ' ❌ Kasutaja on juba hõivatud',
            'login' => ' Sisselogimine',
            'out' => ' Väljumine',
            'reg' => ' Registreerimine',
            'fav' => ' Lemmikud',
            'greetings' => ' Lemmikud',
            'all' => ' Kõik',
            'filters' => 'Filtrid',
            'search 1' => 'Otsi',
            'search off' => 'Lähtesta otsing',
            'reset_all' => ' Lähtesta kõik',
            'category' => ' Kategooria ',
            'del' => ' Kustuta ',
            'fav 1' => ' Juba lemmikute hulgas ',
            'last' => ' Viimane ',
            'username' => 'Logi sisse',
            'password' => 'Parool',
            'weather 1' => 'Ilm täna',
            'last_news' => 'Viimased uudised',





        ],
        'ru' => [
            'title' => '📰 Новости и 🌤️ Прогноз погоды',
            'title 2' => 'Cборник новостей',
            'news' => 'Новости',
            'search' => 'Поиск...',
            'new' => 'Сначала новые',
            'old' => 'Сначала старые',
            'read' => 'Читать',
            'source' => ' Все источники',
            'weather' => ' Погода',
            'not-login' => ' ❌ Пользователь уже занят',
            'login' => ' Вход',
            'out' => ' Выход',
            'reg' => ' Регистрация',
            'fav' => ' Избранное',
            'all' => ' Все',
            'filters' => 'Фильтры',
            'search 1' => 'Поиск',
            'search off' => 'Сбросить поиск',
            'reset_all' => ' Сбросить всё',
            'category' => ' Kатегория ',
            'del' => ' Удалить ',
            'fav 1' => ' Уже в избранном ',
            'last' => ' Последняя ',
            'username' => 'Логин',
            'password' => 'Пароль',
            'weather 1' => 'Погода сегодня',
            'last_news' => 'Последняя новость',
        ],
    ];

    return [$lang, $text];
}