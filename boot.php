<?php

$is_auth=rand(0,1);
$user_name = 'Евдокимова Мария';

date_default_timezone_set("Europe/Moscow");
setlocale(LC_ALL, 'ru_RU');

require_once('functions/functions.php');
require_once ('functions/data_functions.php');
require_once('functions/mysql_helper.php');