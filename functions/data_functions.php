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

function validate_available($lot, $required, &$errors)
{
    foreach ($required as $key) {
        if (empty($lot[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }
}

/**
 * Преобразует специальные символы в HTML-сущности
 *
 * @param $arr array() Массив данных
 *
 * @return array() Преобразованный массив
 */
function fix_tags($arr)
{
    foreach ($arr as $key => $value) {
        if (!empty($arr[$key])) {
            $arr[$key] = htmlspecialchars($value);
        }
    }
    return $arr;
}

function available_in_array($value, $arr, $key, &$errors)
{
    if(!empty($value)) {
        $category_id = array_column($arr, $key);
        if (!in_array($value, $category_id)) {
            $errors[$key] = 'Такой категории нет. Выберите категорию из списка.';
        }
    }
}

function validate_number($value, $key, &$errors)
{
    if (!is_numeric($value) or $value <= 0) {
        $errors[$key] = 'Это поле целое положительно число';
    }
}

function validate_date($value, $key, &$errors)
{
    $format = 'd.m.Y';
    $date = DateTime::createFromFormat($format, $value);
    if (!($date && $date->format($format) == $value)) {
        $errors[$key] = 'Это поле надо заполнить';
    }
}

function validate_img($key, &$errors)
{
    if (isset($_FILES[$key]['name']) and !empty($_FILES[$key]['name'])) {
        $tmp_name = $_FILES[$key]['tmp_name'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);

        if ($file_type !== "image/png" AND $file_type !== "image/jpeg" AND $file_type !== "image/jpg") {
            $errors[$key] = 'Загрузите картинку в формате png, jpeg или jpg.';
        }
      }
    else {
        $errors[$key] = 'Это поле надо заполнить: загрузите картинку.';
    }
}

function create_directory($file_dir)
{
    if (!file_exists($file_dir)) {
        mkdir($file_dir, 0777, true);
    }
    return $file_dir;
}

function change_filename($arr, $key, $file_dir)
{
    $tmp_name = $_FILES[$key]['tmp_name'];
    $path = $_FILES[$key]['name'];
    $filename = uniqid() . '.' . pathinfo($path, PATHINFO_EXTENSION);
    $arr[$key] = $filename;
    move_uploaded_file($tmp_name, $file_dir . '/' . $filename);
    $arr[$key] = $file_dir . '/' . $filename;
    return $arr[$key];
}

function print_mysql_err($link)
{
    $page_content = include_template('error.php', ['error' => mysqli_error($link)]);
    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'title' => 'Ошибка',
//        'is_auth' => $is_auth,
//        'user_name' => $user_name,
        'categories' => get_categories()
    ]);
    print($layout_content);
    die();
}

function create_lot($arr, $link)
{
    $sql = "INSERT INTO lot (lot_name, description, img_url, start_price, end_datetime, step_bet, id_author, id_category)
            VALUES (?, ?, ?, ?, ?, ?, 1, ?)";
    $stmt = db_get_prepare_stmt($link, $sql, [
        $arr['lot_name'], $arr['description'], $arr['img_url'], intval($arr['start_price']), $arr['lot_date'], intval($arr['step_bet']), intval($arr['id_category'])
    ]);
    $res = mysqli_stmt_execute($stmt);
    return $res;
}