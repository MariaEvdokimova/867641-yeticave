<?php

/**
 * Функция-шаблонизатор.
 *
 * @param $name string имя файла шаблона
 * @param $data array ассоциативный массив с данными для этого шаблона
 *
 * @return string итоговый HTML-код с подставленными данными
 */
function include_template($name, $data) {
    $name = $_SERVER['DOCUMENT_ROOT'] . '/templates/' . $name;
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

/**
 * Форматирует цену и обавляет знак рубля.
 *
 * @param $value int значение цены
 *
 * @return string отформатированное значение
 */
function formatting_price($value)
{
    $value = ceil($value);
    if ($value >= 1000){
        $value = number_format($value, 0, '',' ');
    }
    $value .= ' &#8381;';
    return $value;
}

/**
 * Вывоит сколько осталось времени в формате "количество дней ЧЧ:ММ".
 *
 * @param $dt_end string дата
 *
 * @return string отформатированное значение
 */
function lot_timer($dt_end)
{
    $date_end = date_create($dt_end);
    $dt_now = date_create("now");

    $timer = date_diff($dt_now,$date_end);
    $timer = date_interval_format($timer, "%d дней %H:%I");
    return $timer;
}