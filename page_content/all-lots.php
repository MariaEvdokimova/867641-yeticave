<?php

require_once('../boot.php');
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$link = get_link();
$lots = array();
$categories = get_categories();
$cur_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$page_items = 9;
$items_count = count_lots_by_category($link, $id);
$items_count = isset($items_count) ? $items_count : 0;
$pages_count = ceil($items_count / $page_items);
$offset = ($cur_page - 1) * $page_items;
$pages = range(1, $pages_count);

$lots = get_lot_by_category($id, $page_items, $offset);

$page_content = include_template('all-lots.php', [
    'categories' => $categories,
    'lots' => $lots,
    'pages' => $pages,
    'pages_count' => $pages_count,
    'cur_page' => $cur_page
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Все лоты',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories
]);

print($layout_content);