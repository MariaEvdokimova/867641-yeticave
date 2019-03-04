<?php

require_once('boot.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sign = $_POST;
    $required = ['email', 'name', 'password', 'contacts']; //'avatar',
    $errors = [];
    $link = get_link();

    validate_text($sign, $required,$errors);
    validate_file($sign, 'avatar', $errors);
    validate_email($sign, 'email', $errors);
    validate_unique($sign, 'email', $errors, $link);

    if (count($errors)) {
        $page_content = include_template('sign-up.php', [
            'categories' => get_categories(),
            'sign' => $sign,
            'errors' => $errors
        ]);
    }
    else {
        $password = password_hash($sign['password'], PASSWORD_DEFAULT);
        $avatar = empty($sign['avatar']) ? "" : $sign['avatar'];

        $sql = 'INSERT INTO users (email, name, password, avatar, contacts) VALUES (?, ?, ?, ?, ?)';
        $stmt = db_get_prepare_stmt($link, $sql, [
            $sign['email'], $sign['name'], $password, $avatar, $sign['contacts']
         ]);
        $res = mysqli_stmt_execute($stmt);
        if ($res && empty($errors)) {
            header("Location: /login.php");
            exit();
        }else {
            $content = include_template('error.php', ['error' => mysqli_error($link)]);
        }
    }
}
else {
    $page_content = include_template('sign-up.php', [
        'categories' => get_categories()
    ]);
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Регистрация',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => get_categories()
]);

print($layout_content);