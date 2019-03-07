<?php

require_once('../boot.php');
$id_user = 1;
$sign = array();
$errors= array();
$link = get_link();
$file_dir = '../uploads/users/id' . $id_user . '/avatar';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sign = $_POST;
    $required = ['email', 'name', 'password', 'contacts'];

    validate_text($sign, $required,$errors);
    validate_email($sign, 'email', $errors);
    validate_img('avatar', $errors);

    if (empty($errors['email'])) {
        $res = get_user_by_email($sign['email'], $link);
        if (mysqli_num_rows($res) > 0) {
            $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
        }
    }

    if (count($errors) == 0) {
        if (empty($_FILES['avatar']['name'])) {
            $sign['avatar'] = "";
        } else{
            $file_dir = create_directory($file_dir);
            $sign['avatar'] = change_filename('avatar', $file_dir);
        }
        $sign['password'] = password_hash($sign['password'], PASSWORD_DEFAULT);
        $res = create_user($sign, $link);
        if ($res) {
            header("Location: login.php");
            exit();
        }else {
            print_mysql_err($link);
        }
    }
}
$page_content = include_template('sign-up.php', [
    'categories' => get_categories(),
    'sign' => $sign,
    'errors' => $errors
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Регистрация',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => get_categories()
]);

print($layout_content);