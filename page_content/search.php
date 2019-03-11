<?php

require_once('../boot.php');

$link = get_link();
$lots = array();
$search = htmlspecialchars($_GET['search']);

if ($search) {
    $lots = lots_search($link, $search);
}

$page_content = include_template('search.php', [
    'categories' => get_categories(),
    'lots' => $lots,
    'search' => $search
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Результаты поиска',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => get_categories()
]);

print($layout_content);