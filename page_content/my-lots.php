<?php

require_once('../boot.php');

$page_content = include_template('my-lots.php', [
    'categories' => get_categories()
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Мои ставки',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => get_categories()
]);

print($layout_content);