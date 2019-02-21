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