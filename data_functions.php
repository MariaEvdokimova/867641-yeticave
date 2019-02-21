<?php

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