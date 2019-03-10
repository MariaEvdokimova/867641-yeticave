<?php

require_once('../boot.php');

$link = get_link();
$lots = array();
$search = htmlspecialchars($_GET['search']);

if ($search) {
    $sql = "SELECT l.id_lot, l.lot_name, l.start_price, l.img_url, l.step_bet, c.category_name, l.end_datetime FROM lot l
            JOIN categories c ON c.id_category = l.id_category
            WHERE MATCH(l.lot_name, l.description) AGAINST(?)";
    $stmt = db_get_prepare_stmt($link, $sql, [$search]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);


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