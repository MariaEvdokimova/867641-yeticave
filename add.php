<?php

require_once('boot.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lot = $_POST;
    $errors = validate_lot($lot);

    if (count($errors)) {
        $page_content = include_template('add-lot.php', [
            'categories' => get_categories(),
            'lot' => $lot,
            'errors' => $errors
        ]);
    }
    else {
        $sql = "INSERT INTO lot (lot_name, description, img_url, start_price, end_datetime, step_bet, id_author, id_category)
            VALUES (?, ?, ?, ?, ?, ?, 1, ?)";

        $link = get_link();

        $stmt = db_get_prepare_stmt($link, $sql, [
            $lot['lot_name'], $lot['description'], $lot['img_url'], intval($lot['start_price']), $lot['lot_date'], intval($lot['step_bet']), intval($lot['id_category'])
        ]);

        $res = mysqli_stmt_execute($stmt);

        if ($res) {
            $id_lot = mysqli_insert_id($link);

            header("Location: lot.php?id=" . $id_lot);
        } else {
            $content = include_template('error.php', ['error' => mysqli_error($link)]);
        }
    }
}
else {
    $page_content = include_template('add-lot.php', [
        'categories' => get_categories()
    ]);
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Добавление нового лота',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => get_categories()
]);

print($layout_content);
