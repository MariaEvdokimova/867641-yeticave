<?php

require_once('../boot.php');
$sign = array();
$errors= array();
$link = get_link();
$categories = get_categories();
$avatar = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sign = $_POST;
    $sign['name'] = isset($sign['name']) ? $sign['name'] : '';
    $sign['contacts'] = isset($sign['contacts']) ? $sign['contacts'] : '';
    $required = ['email', 'name', 'password', 'contacts'];

    validate_available($sign, $required,$errors);
    $sign = fix_tags($sign);
    validate_img('avatar', $errors);
    validate_email($sign, 'email', $errors, $link);
    validate_str_len($sign['name'], $errors, 'name', 128);
    validate_str_len($sign['contacts'], $errors, 'contacts', 300);

    if (count($errors) === 0) {
        $sign['password'] = password_hash($sign['password'], PASSWORD_DEFAULT);
        $res = create_user($sign, $link);

        if ($res) {
            if (!empty($_FILES['avatar']['name'])) {
                $id_user = mysqli_insert_id($link);
                $file_dir = '/uploads/users/id' . $id_user . '/avatar';
                create_directory($file_dir);
                $avatar = change_filename('avatar', $file_dir);
                update_user_avatar($avatar, $id_user, $link);
            }

            header("Location: login.php");
            exit();
        }
        print_mysql_err($link);
    }
}
$page_content = include_template('sign-up.php', [
    'categories' => $categories,
    'sign' => $sign,
    'errors' => $errors
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Регистрация',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories
]);

print($layout_content);