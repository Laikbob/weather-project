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
            'weather er' => 'Ilmastiku viga',
            'last_news' => 'Viimased uudised',
            'error' => 'Sisselogimisviga',
            'no fav' => 'Lemmikuid pole',
            'linn 1' => 'Sisesta linn',
            'loc' => 'Geolokatsioon',
            'city' => 'Linn või riik',
            'tem' => 'Temperatuur',
            'des' => 'Kirjeldus',
            'wel' => 'Tere tulemast',
            'del_all' => 'Kustuta kõik uudised',
            'search_result' => 'Otsingu tulemused päringule',
            'confirm' => 'Kas oled kindel, et soovid kõik lemmikud kustutada?',
            'reg1' => 'Sisselogimisnimi või parool on liiga lühike',
            'reg2' => 'Kasutaja on juba olemas',
            'reg3' => 'Registreeru',






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
            'weather er' => 'Ошибка погоды',
            'last_news' => 'Последняя новость',
            'error' => 'Ошибка входа',
            'no fav' => 'Нет избранных',
            'linn 1' => 'Введите город',
            'loc' => 'Геолокация',
            'city' => 'Город или страна',
            'tem' => 'Температура',
            'des' => 'Oписание',
            'wel' => 'Добро пожаловать',
            'del_all' => 'Удалить всe новости',
            'search_result' => 'Результаты поиска по запросу',
            'confirm' => 'Ты уверен, что хочешь удалить все избранное?',
            'reg1' => 'Имя пользователя или пароль слишком короткие',
            'reg2' => 'Пользователь уже существует',
            'reg3' => 'Зарегистрироваться',

        ],
    ];

    return [$lang, $text];
}