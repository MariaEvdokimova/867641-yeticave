<?php

session_start();
$user_name = '';
$is_auth = 0;
if (isset($_SESSION['user'])) {
    $user_name = $_SESSION['user']['name'];
    $is_auth = 1;
}

date_default_timezone_set("Europe/Moscow");
setlocale(LC_ALL, 'ru_RU');

require_once('functions/functions.php');
require_once ('functions/data_functions.php');
require_once('functions/mysql_helper.php');