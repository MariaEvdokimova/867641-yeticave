<?php
require_once('boot.php');
$id_user = 1;
$lot = array();
$errors= array();
$link = get_link();
$file_dir = 'uploads/users/id' . $id_user . '/lots';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lot = $_POST;
    $required = ['lot_name', 'description', 'lot_date', 'start_price', 'step_bet', 'id_category'];
    validate_text($lot, $required,$errors);
    validate_number($lot['start_price'], 'start_price',$errors);
    validate_number($lot['step_bet'], 'step_bet',$errors);
    validate_date($lot['lot_date'],'lot_date',$errors);
    validate_img('img_url', $errors);
    if (count($errors) == 0) {
        $file_dir = create_directory($file_dir);
        $lot['img_url'] = change_filename($lot['img_url'], 'img_url', $file_dir);
        $date = date_create_from_format('d.m.Y', $lot['lot_date']);
        $lot['lot_date'] = date_format($date, 'Y-m-d');
        $res = create_lot($lot, $link);
        if ($res) {
            $id_lot = mysqli_insert_id($link);
            header("Location: lot.php?id=" . $id_lot);
        } else {
            print_mysql_err($link);
        }
    }
}
$page_content = include_template('add-lot.php', [
    'categories' => get_categories(),
    'lot' => $lot,
    'errors' => $errors
]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Добавление нового лота',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => get_categories()
]);
print($layout_content);