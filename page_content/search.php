<?php

require_once('../boot.php');

$link = get_link();
$lots = array();
$errors = array();
$categories = get_categories();
$pages = 0;
$pages_count = 0;
$cur_page = 0;
$search = isset($_GET['search']) ? htmlspecialchars(trim($_GET['search'])) : '';

validate_str_len($search,$errors, 'search', 200);

if ($search and count($errors) === 0) {
    $cur_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $page_items = 9;

    $items_count = get_count_lots($link, $search);

    $pages_count = ceil($items_count / $page_items);
    $offset = ($cur_page - 1) * $page_items;

    $pages = range(1, $pages_count);

    $lots = lots_search($link, $search, $page_items, $offset);
}
    $page_content = include_template('search.php', [
        'categories' => $categories,
        'lots' => $lots,
        'search' => $search,
        'pages' => $pages,
        'pages_count' => $pages_count,
        'cur_page' => $cur_page,
        'errors' => $errors
    ]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Результаты поиска',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories,
    'errors' => $errors
]);

print($layout_content);