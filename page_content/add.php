<?php
require_once('../boot.php');
$categories = get_categories();

if (!isset($_SESSION['user'])) {
    print_session_err($categories);
}

$id_user = $_SESSION['user']['id_user'];
$lot = array();
$errors = array();
$link = get_link();
$file_dir = '/uploads/users/id' . $id_user . '/lots';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lot = $_POST;
    $required = ['lot_name', 'description', 'lot_date', 'start_price', 'step_bet', 'id_category'];

    validate_available($lot, $required, $errors);
    $lot = fix_tags($lot);
    available_in_array($lot['id_category'], $categories, 'id_category', $errors);
    validate_number($lot['start_price'], 'start_price', $errors);
    validate_number($lot['step_bet'], 'step_bet', $errors);
    validate_date($lot['lot_date'], 'lot_date', $errors);
    actual_date($lot, 'lot_date',$errors);
    available_img('img_url', $errors);
    validate_img('img_url', $errors);
    validate_str_len($lot['lot_name'], $errors, 'lot_name', 128);
    validate_str_len($lot['description'], $errors, 'description', 300);

    if (count($errors) === 0) {
        create_directory($file_dir);
        $lot['img_url'] = change_filename('img_url', $file_dir);
        $date = date_create_from_format('d.m.Y', $lot['lot_date']);
        $lot['lot_date'] = date_format($date, 'Y-m-d');
        $lot['id_author'] = $id_user;
        $res = create_lot($lot, $link);
        if ($res) {
            $id_lot = mysqli_insert_id($link);
            header("Location: lot.php?id=" . $id_lot);
        }
        print_mysql_err($link);
    }
}
$page_content = include_template('add-lot.php', [
    'categories' => $categories,
    'lot' => $lot,
    'errors' => $errors
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Добавление нового лота',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories
]);
print($layout_content);