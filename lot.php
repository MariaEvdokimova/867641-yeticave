<?php

require_once('boot.php');

$id = intval($_GET['id']);

$page_content = include_template('lot.php', [
    'categories' => get_categories(),
    'lot' => get_lot_by_id($id)
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Главная',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => get_categories()
]);

print($layout_content);