<?php

require_once('boot.php');

$page_content = include_template('index.php', [
    'categories' => get_categories($link),
    'announcement_list' => get_announcement_list($link)
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Главная',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => get_categories($link)
]);

print($layout_content);