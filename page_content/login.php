<?php

require_once('../boot.php');
session_start();
$form = array();
$errors= array();
$link = get_link();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form = $_POST;
    $required = ['email', 'password'];

    validate_text($form, $required,$errors);

    $res = get_user_by_email($form['email'], $link);
    $user = $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : null;

    if (count($errors) == 0 and $user) {
        if (password_verify($form['password'], $user['password'])) {
            $_SESSION['user'] = $user;
        }
        else {
            $errors['password'] = 'Неверный пароль';
        }
    }
    else {
        $errors['email'] = 'Такой пользователь не найден';
    }

    if (count($errors) == 0) {
        header("Location: /index.php");
        exit();
    }
}
$page_content = include_template('login.php', [
    'categories' => get_categories(),
    'form' => $form,
    'errors' => $errors
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Вход',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => get_categories()
]);

print($layout_content);