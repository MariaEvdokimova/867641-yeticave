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
    $sql = "SELECT l.id_lot, l.lot_name, l.description, l.start_price, l.img_url, l.step_bet, c.category_name, l.end_datetime, l.id_author, l.id_winner
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

function change_filename($key, $file_dir)
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
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = db_get_prepare_stmt($link, $sql, [
        $arr['lot_name'], $arr['description'], $arr['img_url'], intval($arr['start_price']), $arr['lot_date'], intval($arr['step_bet']), $arr['id_author'], intval($arr['id_category'])
    ]);
    $res = mysqli_stmt_execute($stmt);
    return $res;
}

function get_user_by_email($value, $link)
{
    $value = mysqli_real_escape_string($link, $value);
    $sql = "SELECT * FROM users WHERE email = '{$value}'";
    $res = mysqli_query($link, $sql);
    $res = mysqli_fetch_array($res, MYSQLI_ASSOC);
    return $res;
}

function validate_email($arr, $key, &$errors, $link)
{
    if (empty($errors[$key])) {
        if (!filter_var($arr[$key], FILTER_VALIDATE_EMAIL)) {
            $errors[$key] = 'Email должен быть корректным';
        }
        if (get_user_by_email($arr[$key], $link)) {
            $errors[$key] = 'Пользователь с этим email уже зарегистрирован';
        }
    }
}

function create_user($arr, $link)
{
    $sql = 'INSERT INTO users (email, name, password, contacts) VALUES (?, ?, ?, ?)';
    $stmt = db_get_prepare_stmt($link, $sql, [
        $arr['email'], $arr['name'], $arr['password'], $arr['contacts']
    ]);
    $res = mysqli_stmt_execute($stmt);
    return $res;
}

function update_user_avatar($avatar, $id_user, $link)
{
    $sql = "UPDATE users SET avatar = '{$avatar}' WHERE id_user = {$id_user}";
    mysqli_query($link, $sql);
}

function validate_user($key, $value, &$errors)
{
    if (empty($errors[$key]) and !$value) {
        $errors[$key] = 'Такой пользователь не найден';
    }
}

function available_password($form_pas, $user_pas, &$errors)
{
    if (!password_verify($form_pas, $user_pas)) {
        $errors['password'] = 'Неверный пароль';
    }
}

function validate_sum_bet($form_cost, $start_price, $step_bet, &$errors)
{
    if(empty($errors['cost']) and $form_cost <= $start_price + $step_bet){
        $errors['cost'] = 'Значение должно быть больше, чем текущая цена лота + шаг ставки';
    }
}

function create_bet_lot($arr, $link)
{
    $sql = 'INSERT INTO bet (sum_bet, id_user, id_lot) VALUES (?, ?, ?)';
    $stmt = db_get_prepare_stmt($link, $sql, [
        $arr['cost'], $arr['id_user'], $arr['id_lot']
    ]);
    $res = mysqli_stmt_execute($stmt);
    return $res;
}

function get_bet_by_lot($value, $link)
{
    $value = intval($value);
    $sql = "SELECT b.*, u.name
            FROM bet b INNER JOIN users u ON b.id_user = u.id_user 
            WHERE id_lot = {$value}
            ORDER BY b.creation_date DESC";
    $res = mysqli_query($link, $sql);
    $res = mysqli_fetch_all($res, MYSQLI_ASSOC);

    return $res;
}

function user_is_bet($arr, $id_user)
{
    foreach ($arr as $value)
    {
        if($id_user == $value['id_user']){
            return true;
        }
    }
    return false;
}

/**
 * Преобразует дату и время в "человеческом" формате
 *
 * @param $arr array() Массив данных
 * @param $key string Ключ для поля с датой
 *
 * @return array() Преобразованный массив
 */
function human_timing(&$arr, $key)
{
    foreach ($arr as &$value){
        $time_bet = strtotime($value[$key]);
        $time = time() - $time_bet;
        $time = ($time < 60) ? 60 : $time;
        if ($time < 3600){
            $number_of_units = floor($time / 60);
            $value[$key] = $number_of_units . ' ' . 'минут назад';
        }
        else if ($time < 86400) {
            $number_of_units = floor($time / 3600);
            $value[$key] = $number_of_units . ' ' . 'час назад';
        }
        else{
            $value[$key] = date("d.m.y", $time_bet) . ' в ' . date("H:i", $time_bet);
        }
    }
}

/**
 * Выбирает максимальную ставку по лоту
 *
 * @param $link mysqli Ресурс соединения
 * @param $value int идентификатор лота
 *
 * @return array() Максимальную ставку и id лота
 */
function get_max_bet($link, $value)
{
    $value = mysqli_real_escape_string($link, $value);
    $sql = "SELECT b.id_lot, max(b.sum_bet) as max_bet  FROM bet b WHERE id_lot = {$value} GROUP BY b.id_lot";
    $res = mysqli_query($link, $sql);
    $res = mysqli_fetch_array($res, MYSQLI_ASSOC);
    return $res;
 }

 function validate_str_len($str, &$errors, $key, $len)
{
    if (strlen($str) > $len) {
        $errors[$key] = 'Длинна строки не более ' . $len . ' символов';
    }
}

function print_session_err($categories)
{
    $page_content = include_template('403.php', [
        'categories' => $categories
    ]);

    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'title' => 'Ошибка',
        'is_auth' => 0,
        'user_name' => '',
        'categories' => get_categories()
    ]);
    print($layout_content);
    die();
}
