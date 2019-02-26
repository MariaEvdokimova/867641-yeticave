<?php

require_once('boot.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $lot = $_POST['lot'];

  // ("image/png", "image/jpeg", "image/jpg");
    $filename = uniqid() . '.jpg';
    $lot['img_url'] = $filename;
    move_uploaded_file($_FILES['lot_img']['tmp_name'], 'img/' . $filename);

    $sql = "INSERT INTO lot (lot_name, description, img_url, start_price, end_datetime, step_bet, id_author, id_category)
                VALUES (?, ?, ?, ?, ?, ?, 1, ?)";

    $stmt = db_get_prepare_stmt(get_link(), $sql, [
        $lot['lot_name'], $lot['description'], $lot['img_url'], intval($lot['start_price']), $lot['lot_date'], intval($lot['step_bet']), intval($lot['id_category'])
    ]);

    $res = mysqli_stmt_execute($stmt);

    if ($res) {
        $gif_id = mysqli_insert_id(get_link());

        header("Location: lot.php?id=" . $id_lot);
    }
    else {
        $content = include_template('error.php', ['error' => mysqli_error(get_link())]);
    }
}


$page_content = include_template('add-lot.php', [
    'categories' => get_categories()
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Добавление нового лота',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => get_categories()
]);

print($layout_content);
