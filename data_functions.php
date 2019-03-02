<?php

function get_link()
{
    $link = mysqli_init();
    mysqli_options($link, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);

    $link = mysqli_connect("localhost", "root", "", "yeticave");
    mysqli_set_charset($link, "utf8");

    if (!$link) {
        $error = mysqli_connect_error();
        $layout_content = include_template('error.php', ['error' => $error]);

        print($layout_content);
        exit(1);
    }
    else {
        return $link;
    }
}

function get_categories()
{
    $sql = 'SELECT id_category, category_name FROM categories ORDER BY id_category';
    $result = mysqli_query(get_link(), $sql);

    $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $categories;
}

function get_announcement_list()
{
    $sql = 'SELECT l.id_lot, l.lot_name, l.start_price, l.img_url, l.step_bet, c.category_name, l.end_datetime
        FROM lot l LEFT JOIN categories c ON l.id_category = c.id_category
        WHERE l.end_datetime > NOW() ORDER BY l.creation_date DESC LIMIT 9';
    $result = mysqli_query(get_link(), $sql);

    $announcement_list = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $announcement_list;
}

function get_lot_by_id($id)
{
    $sql = "SELECT l.id_lot, l.lot_name, l.description, l.start_price, l.img_url, l.step_bet, c.category_name, l.end_datetime
        FROM lot l LEFT JOIN categories c ON l.id_category = c.id_category
        WHERE l.id_lot = '%s' ";
    $sql = sprintf($sql, $id);
    if ($result = mysqli_query(get_link(), $sql)) {

        if (!mysqli_num_rows($result)) {
            http_response_code(404);
            $content = include_template('error.php', ['error' => 'Лот с этим идентификатором не найден']);
            print($content);
            exit(1);
        }
        else {
            $lot = mysqli_fetch_array($result, MYSQLI_ASSOC);
            return $lot;
        }
    }
    else {
        show_error('Ошибка подключения к базе', mysqli_error(get_link()));
    }
}

function validate_lot(&$lot)
{
    $required = ['lot_name', 'description', 'lot_date', 'start_price', 'step_bet', 'id_category'];
    $errors = [];

    foreach ($required as $key) {
        if (empty($lot[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }

    if (!is_numeric($lot['start_price']) or $lot['start_price'] <= 0) {
        $errors['start_price'] = 'Это поле надо заполнить';
    }

    if (!is_numeric($lot['step_bet']) or $lot['step_bet'] <= 0) {
        $errors['step_bet'] = 'Это поле надо заполнить';
    }

    $format = 'd.m.Y';
    $d = DateTime::createFromFormat($format, $lot['lot_date']);
    if(!($d && $d->format($format) == $lot['lot_date'])){
        $errors['lot_date'] = 'Это поле надо заполнить';
    }
    else{
        $lot['lot_date'] = $d->format('Y-m-d');
    }

    if (isset($_FILES['lot_img']['name']) and !empty($_FILES['lot_img']['name'])) {
        $tmp_name = $_FILES['lot_img']['tmp_name'];
        $path = $_FILES['lot_img']['name'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);

        if ($file_type !== "image/png" AND $file_type !== "image/jpeg" AND $file_type !== "image/jpg") {
            $errors['lot_img'] = 'Загрузите картинку в формате png, jpeg или jpg.';
        }
        else {
            $filename = uniqid() . '.' . pathinfo($path, PATHINFO_EXTENSION);
            $lot['img_url'] = $filename;
            move_uploaded_file($tmp_name, 'img/' . $filename);
            $lot['img_url'] = 'img/' . $filename;
        }
    }
    return $errors;
}
