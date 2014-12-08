<?php
function chinese_excerpt($text, $lenth=100) {
    $text = mb_substr($text,0, $lenth,'utf-8');
    return $text;
}
function chinese_content($text, $lenth=100) {
    $text = mb_substr($text,0, $lenth,'utf-8');
    return $text;
}
//add_filter('the_content', 'chinese_content');
//add_filter('the_excerpt()', 'chinese_excerpt');
