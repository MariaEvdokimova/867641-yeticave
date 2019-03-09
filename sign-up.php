<?php

require_once('boot.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

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