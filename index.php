<?php

require_once('boot.php');

$page_content = include_template('index.php', [
    'categories' => get_categories(),
    'announcement_list' => get_announcement_list()
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Главная',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => get_categories()
]);

print($layout_content);