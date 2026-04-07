<?php

function fetchNews($url, $source, $limit = 25) {

    $rss = @simplexml_load_file($url);
    $items = [];
    $count = 0;

    if ($rss && isset($rss->channel->item)) {
        foreach ($rss->channel->item as $item) {

            $image = "";

            if(isset($item->enclosure['url'])){
                $image = (string)$item->enclosure['url'];
            } else {
                $media = $item->children('media', true);
                if(isset($media->content)){
                    $image = (string)$media->content->attributes()->url;
                }
            }

            $items[] = [
                'title' => (string)$item->title,
                'link' => (string)$item->link,
                'pubDate' => strtotime($item->pubDate),
                'description' => strip_tags($item->description),
                'image' => $image,
                'source' => $source
            ];

            $count++;
            if ($count >= $limit) break;
        }
    }

    return $items;
}