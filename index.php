<?php

require_once('boot.php');
$announcement_list = get_announcement_list();
$categories = get_categories();

$page_content = include_template('index.php', [
    'categories' => $categories,
    'announcement_list' => $announcement_list
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Главная',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories
]);

print($layout_content);