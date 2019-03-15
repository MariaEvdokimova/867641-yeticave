<?php

require_once('../boot.php');

if (!isset($_SESSION['user'])) {
    print_session_err($categories);
}

$categories = get_categories();
$link = get_link();
$lots = array();
$id_user = isset($_SESSION['user']['id_user']) ? $_SESSION['user']['id_user'] : 0;

$lots = get_bet_by_user($id_user, $link);
human_timing($lots, 'creation_date');

$page_content = include_template('my-lots.php', [
    'categories' => $categories,
    'lots' => $lots
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Мои ставки',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories
]);

print($layout_content);