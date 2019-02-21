<?php

function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

function formatting_price($value)
{
    $value = ceil($value);
    if ($value >= 1000){
        $value = number_format($value, 0, '',' ');
    }
    $value .= ' &#8381;';
    return $value;
}

function lot_timer($dt_end)
{
    $date_end = date_create($dt_end);
    $dt_now = date_create("now");

    $timer = date_diff($dt_now,$date_end);
    $timer = date_interval_format($timer, "%d дней %H:%I");
    return $timer;
}

function get_categories($link)
{
    $sql = 'SELECT id_category, category_name FROM categories ORDER BY id_category';
    $result = mysqli_query($link, $sql);

    $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $categories;
}

function get_announcement_list($link)
{
    $sql = 'SELECT l.id_lot, l.lot_name, l.start_price, l.img_url, l.step_bet, c.category_name, l.end_datetime
                FROM lot l LEFT JOIN categories c ON l.id_category = c.id_category
                WHERE l.end_datetime > NOW() ORDER BY l.creation_date DESC LIMIT 9';
    $result = mysqli_query($link, $sql);

    $announcement_list = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $announcement_list;
}

function get_user_name($link)
{
    $sql = 'SELECT id_user, name FROM users LIMIT 1';
    $result = mysqli_query($link, $sql);

    $user_name = mysqli_fetch_assoc($result);
    return $user_name;
}