<?php

require_once('../boot.php');
$form = array();
$errors= array();
$link = get_link();
$categories = get_categories();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = $_POST;
    $required = ['email', 'password'];

    validate_available($form, $required, $errors);
    $form = fix_tags($form);

    $user = get_user_by_email($form['email'], $link);
    validate_user('email', $user,$errors);

    if (count($errors) === 0 and $user) {
        available_password($form['password'], $user['password'], $errors);
    }
    if (count($errors) === 0) {
        $_SESSION['user'] = $user;
        header("Location: /index.php");
        exit();
    }
}
$page_content = include_template('login.php', [
    'categories' => $categories,
    'form' => $form,
    'errors' => $errors
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Вход',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories
]);

print($layout_content);