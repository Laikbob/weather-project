<?php
function fetchNews($url, $limit = 5) {
    $rss = @simplexml_load_file($url);
    $items = [];
    $count = 0;

    if ($rss && isset($rss->channel->item)) {
        foreach ($rss->channel->item as $item) {
            $items[] = [
                'title' => (string)$item->title,
                'link' => (string)$item->link,
                'pubDate' => strtotime((string)$item->pubDate), // unix timestamp для сортировки
                'description' => (string)$item->description ?? '',
                'category' => isset($item->category) ? (string)$item->category : '',
                'image' => isset($item->enclosure['url']) ? (string)$item->enclosure['url'] : ''
            ];
            $count++;
            if ($count >= $limit) break;
        }
    }

    return $items;
}
?>