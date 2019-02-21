<?php

require_once('boot.php');

$page_content = include_template('index.php', [
    'categories' => get_categories($link),
    'announcement_list' => get_announcement_list($link)
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Главная',
    'user_name' => get_user_name($link),
    'categories' => get_categories($link)
]);

print($layout_content);