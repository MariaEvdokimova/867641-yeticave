<?php

$link = mysqli_init();
mysqli_options($link, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);

$link = mysqli_connect("localhost", "root", "", "yeticave");
mysqli_set_charset($link, "utf8");

$categories = [];
$announcement_list = [];
