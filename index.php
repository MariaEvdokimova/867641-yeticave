<?php

require_once('boot.php');

if (!$link) {
    $error = mysqli_connect_error();
    $layout_content = include_template('error.php', ['error' => $error]);
}
else {
    $sql = 'SELECT id_category, category_name FROM categories ORDER BY id_category';
    $result = mysqli_query($link, $sql);

    $sql = 'SELECT l.id_lot, l.lot_name, l.start_price, l.img_url, l.step_bet, c.category_name, l.end_datetime
                FROM lot l LEFT JOIN categories c ON l.id_category = c.id_category
                WHERE l.end_datetime > NOW() ORDER BY l.creation_date DESC LIMIT 9';
    $result_lot = mysqli_query($link, $sql);

    if ($result and  $result_lot) {
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $announcement_list = mysqli_fetch_all($result_lot, MYSQLI_ASSOC);

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
    }
    else {

        $error = mysqli_error($link);
        $layout_content = include_template('error.php', ['error' => $error]);
    }
}

print($layout_content);