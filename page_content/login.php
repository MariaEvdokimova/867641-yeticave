<?php

require_once('../boot.php');
$form = array();
$errors= array();
$link = get_link();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form = $_POST;
    $required = ['email', 'password'];

    validate_available($form, $required, $errors);
    $form = fix_tags($form);

    $res = get_user_by_email($form['email'], $link);

    var_dump($res);

    $user = available_user($res, $form['email'], $errors);

    if (count($errors) == 0 and $user) {
        available_password($user, $form['password'], $user['password'], $errors);
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