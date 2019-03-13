<?php

require_once('../boot.php');
$categories = get_categories();

$page_content = include_template('my-lots.php', [
    'categories' => $categories
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Мои ставки',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories
]);

print($layout_content);