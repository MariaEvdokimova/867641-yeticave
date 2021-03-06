<?php

require_once('../boot.php');
$id = intval($_GET['id']);
$form = array();
$errors= array();
$link = get_link();
$categories = get_categories();
$lot = get_lot_by_id($id);
$id_user = isset($_SESSION['user']['id_user']) ? $_SESSION['user']['id_user'] : 0;
$max_bet = get_max_bet($link, $id);
$history_bet = get_bet_by_lot($lot['id_lot'], $link);
$user_is_bet = user_is_bet($history_bet, $id_user);
human_timing($history_bet, 'creation_date');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = $_POST;
    $required = ['cost'];
    validate_available($form, $required, $errors);
    $form = fix_tags($form);
    validate_number($form['cost'], 'cost', $errors);
    validate_sum_bet($form['cost'], $lot['start_price'], $lot['step_bet'], $max_bet['max_bet'], $errors);

    if (count($errors) === 0) {
        $arr = ['cost'  => $form['cost'], 'id_user' => $id_user, 'id_lot' => $lot['id_lot']];
        $res = create_bet_lot($arr, $link);
        if ($res) {
            $id_bet = mysqli_insert_id($link);
            header("Location: my-lots.php?id=" . $id_bet);
        }
        print_mysql_err($link);
    }
}

$page_content = include_template('lot.php', [
    'categories' => $categories,
    'lot' => $lot,
    'form' => $form,
    'errors' => $errors,
    'user_is_bet' => $user_is_bet,
    'history_bet' => $history_bet,
    'max_bet' => $max_bet
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Главная',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories
]);

print($layout_content);