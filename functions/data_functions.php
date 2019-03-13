<?php

/**
 * Создает ресурс соединения с базой данных, если не успешно,
 * то перенаправляет на страницу с ошибкой
*/
function get_link()
{
    $link = mysqli_init();
    mysqli_options($link, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);

    $link = mysqli_connect("localhost", "root", "", "yeticave");
    mysqli_set_charset($link, "utf8");

    if (!$link) {
        $error = mysqli_connect_error();
        $layout_content = include_template('error.php', ['error' => $error]);

        print($layout_content);
        exit(1);
    }
    return $link;
}

/**
 * Получает данные о категориях
 *
 * @return array массив данных категорий
 */
function get_categories()
{
    $sql = 'SELECT id_category, category_name FROM categories ORDER BY id_category';
    $result = mysqli_query(get_link(), $sql);

    $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $categories;
}

/**
 * Получает данные о лотах
 *
 * @return array() массив данных лотов
 */
function get_announcement_list()
{
    $sql = 'SELECT l.id_lot, l.lot_name, l.start_price, l.img_url, l.step_bet, c.category_name, l.end_datetime
        FROM lot l LEFT JOIN categories c ON l.id_category = c.id_category
        WHERE l.end_datetime > NOW() ORDER BY l.creation_date DESC LIMIT 9';
    $result = mysqli_query(get_link(), $sql);

    $announcement_list = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $announcement_list;
}

/**
 * Получает данные о лоте по его идентификатору
 *
 * @param $id int идентификатор лота
 *
 * @return array() массив данных по конкретному лот или ошибка
 */
function get_lot_by_id($id)
{
    $sql = "SELECT l.id_lot, l.lot_name, l.description, l.start_price, l.img_url, l.step_bet, c.category_name, l.end_datetime, l.id_author, l.id_winner
        FROM lot l LEFT JOIN categories c ON l.id_category = c.id_category
        WHERE l.id_lot = '%s' ";
    $sql = sprintf($sql, $id);
    if ($result = mysqli_query(get_link(), $sql)) {

        if (!mysqli_num_rows($result)) {
            http_response_code(404);
            $content = include_template('error.php', ['error' => 'Лот с этим идентификатором не найден']);
            print($content);
            exit(1);
        }
        $lot = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return $lot;
    }
}

/**
 * Проверяет, заполнено ли поле,
 * если нет то записывает ошибуку в массив ошибок
 *
 * @param $lot array массив данных
 * @param $required array массив названий полей, которые нужно проверить
 * @param $errors array() массив ошибок
 */
function validate_available($lot, $required, &$errors)
{
    foreach ($required as $key) {
        if (empty($lot[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }
}

/**
 * Преобразует специальные символы в HTML-сущности
 *
 * @param $arr array() Массив данных
 *
 * @return array() Преобразованный массив
 */
function fix_tags($arr)
{
    foreach ($arr as $key => $value) {
        if (!empty($arr[$key])) {
            $arr[$key] = htmlspecialchars($value);
        }
    }
    return $arr;
}

/**
 * Проверяет, что выбрана категория из списка,
 * если нет то записывает ошибуку в массив ошибок
 *
 * @param $value int проверяемое значение
 * @param $arr array массив данных с категориями
 * @param $key string ключ, Для записи ошибки
 * @param $errors array() массив ошибок
 */
function available_in_array($value, $arr, $key, &$errors)
{
    if(!empty($value)) {
        $category_id = array_column($arr, $key);
        if (!in_array($value, $category_id)) {
            $errors[$key] = 'Такой категории нет. Выберите категорию из списка.';
        }
    }
}

/**
 * Проверяет, что поле целое положительно число,
 * если нет то записывает ошибуку в массив ошибок
 *
 * @param $value int проверяемое значение
 * @param $key string ключ, Для записи ошибки
 * @param $errors array() массив ошибок
 */
function validate_number($value, $key, &$errors)
{
    if (!is_numeric($value) or $value <= 0) {
        $errors[$key] = 'Это поле целое положительно число';
    }
}

/**
 * Проверяет, что формат даты 'd.m.Y',
 * если нет то записывает ошибуку в массив ошибок
 *
 * @param $value string значение поля дата
 * @param $key string ключ, имя поля дата
 * @param $errors array() массив ошибок
 */
function validate_date($value, $key, &$errors)
{
    if (empty($errors[$key])) {
        $format = 'd.m.Y';
        $date = DateTime::createFromFormat($format, $value);
        if (!($date && $date->format($format) === $value)) {
            $errors[$key] = 'Введите дату завершения торгов в формате ДД.ММ.ГГГГ';
        }
    }
}

/**
 * Проверяет, что дата больше текущей минимум на сутки,
 * если нет то записывает ошибуку в массив ошибок
 *
 * @param $arr array значение поля дата
 * @param $key string ключ, имя поля дата
 * @param $errors array() массив ошибок
 */
function actual_date($arr, $key, &$errors)
{
    if (empty($errors[$key])) {
        $tomorrow = strtotime('tomorrow');
        $lot_date = strtotime($arr[$key]);
        if ($lot_date < $tomorrow){
            $errors[$key] = 'Дата окончания должна быть больше текущей минимум на сутки';
        }
    }
}

/**
 * Проверяет, что загрузили действительно картинку в формате png, jpeg или jpg,
 * если нет то записывает ошибуку в массив ошибок
 *
 * @param $key string ключ, имя поля с загруженным файлом
 * @param $errors array() массив ошибок
 */
function validate_img($key, &$errors)
{
    if (isset($_FILES[$key]['name']) and !empty($_FILES[$key]['name'])) {
        $tmp_name = $_FILES[$key]['tmp_name'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);

        if ($file_type !== "image/png" AND $file_type !== "image/jpeg" AND $file_type !== "image/jpg") {
            $errors[$key] = 'Загрузите картинку в формате png, jpeg или jpg.';
        }
      }
}

/**
 * Проверяет, загрузили файл или нет,
 * если нет то записывает ошибуку в массив ошибок
 *
 * @param $key string ключ, имя поля с загруженным файлом
 * @param $errors array() массив ошибок
 */
function available_img($key, &$errors)
{
    if (!(isset($_FILES[$key]['name']) and !empty($_FILES[$key]['name']))) {
        $errors[$key] = 'Это поле надо заполнить: загрузите картинку.';
    }
}

/**
 * Создает дирректорию, если такой нет
 *
 * @param $file_dir string дирректоря
 *
 */
function create_directory($file_dir)
{
    if (!file_exists($file_dir)) {
        $file_dir = ROOT_DIR . $file_dir;
        mkdir($file_dir, 0777, true);
    }
}

/**
 * Меняет имя файла на набор уникальных символов
 *
 * @param $key string ключ, имя поля с выбранным файлом
 * @param $file_dir string дирректоряи
 *
 * @return array возвращает имя файла и его путь в виде элемента массива
 */
function change_filename($key, $file_dir)
{
    $tmp_name = $_FILES[$key]['tmp_name'];
    $path = $_FILES[$key]['name'];
    $filename = uniqid() . '.' . pathinfo($path, PATHINFO_EXTENSION);
    $arr[$key] = $filename;
    move_uploaded_file($tmp_name, ROOT_DIR . $file_dir . '/' . $filename);
    $arr[$key] = $file_dir . '/' . $filename;
    return $arr[$key];
}

/**
 * Переводит на страницу с ошибкой
 *
 * @param $link mysqli Ресурс соединения
 *
 */
function print_mysql_err($link)
{
    $page_content = include_template('error.php', ['error' => mysqli_error($link)]);
    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'title' => 'Ошибка',
//        'is_auth' => $is_auth,
//        'user_name' => $user_name,
        'categories' => get_categories()
    ]);
    print($layout_content);
    die();
}

/**
 * Добавляет новый лот в базу
 *
 * @param $arr array массив данных о новом лоте
 * @param $link mysqli Ресурс соединения
 *
 * @return bool TRUE в случае успешного завершения или FALSE в случае ошибки
 */
function create_lot($arr, $link)
{
    $sql = "INSERT INTO lot (lot_name, description, img_url, start_price, end_datetime, step_bet, id_author, id_category)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = db_get_prepare_stmt($link, $sql, [
        $arr['lot_name'], $arr['description'], $arr['img_url'], intval($arr['start_price']), $arr['lot_date'], intval($arr['step_bet']), $arr['id_author'], intval($arr['id_category'])
    ]);
    $res = mysqli_stmt_execute($stmt);
    return $res;
}

/**
 * Получает данные о пользователе по email
 *
 * @param $value string введеный email
 * @param $link mysqli Ресурс соединения
 *
 * @return array() массив данных о пользователе
 */
function get_user_by_email($value, $link)
{
    $value = mysqli_real_escape_string($link, $value);
    $sql = "SELECT * FROM users WHERE email = '{$value}'";
    $res = mysqli_query($link, $sql);
    $res = mysqli_fetch_array($res, MYSQLI_ASSOC);
    return $res;
}

/**
 * Проверяет, что правильно ввели новый email, если нет
 * то записывает ошибуку в массив ошибок
 *
 * @param $arr array массив данных
 * @param $key string ключ поля где хранится email
 * @param $errors array() массив ошибок
 * @param $link mysqli Ресурс соединения
 */
function validate_email($arr, $key, &$errors, $link)
{
    if (empty($errors[$key])) {
        if (!filter_var($arr[$key], FILTER_VALIDATE_EMAIL)) {
            $errors[$key] = 'Email должен быть корректным';
        }
        if (get_user_by_email($arr[$key], $link)) {
            $errors[$key] = 'Пользователь с этим email уже зарегистрирован';
        }
    }
}

/**
 * Добавляет нового пользователя в базу
 *
 * @param $arr array массив данных о новом пользователе
 * @param $link mysqli Ресурс соединения
 *
 * @return bool TRUE в случае успешного завершения или FALSE в случае ошибки
 */
function create_user($arr, $link)
{
    $sql = 'INSERT INTO users (email, name, password, contacts) VALUES (?, ?, ?, ?)';
    $stmt = db_get_prepare_stmt($link, $sql, [
        $arr['email'], $arr['name'], $arr['password'], $arr['contacts']
    ]);
    $res = mysqli_stmt_execute($stmt);
    return $res;
}

/**
 * Обнавляет данные аватара пользователя в базе
 *
 * @param $avatar array значение пути и имени файла аватара
 * @param $id_user int идентификатор пользователя
 * @param $link mysqli Ресурс соединения
 */
function update_user_avatar($avatar, $id_user, $link)
{
    $sql = "UPDATE users SET avatar = '{$avatar}' WHERE id_user = {$id_user}";
    mysqli_query($link, $sql);
}

/**
 * Проверяет, есть ли данные о пользователе, если нет
 * то записывает ошибуку в массив ошибок
 *
 * @param $key string ключ для записи ошибки
 * @param $value array значение пользователя
 * @param $errors array() массив ошибок
 */
function validate_user($key, $value, &$errors)
{
    if (empty($errors[$key]) and !$value) {
        $errors[$key] = 'Такой пользователь не найден';
    }
}

/**
 * Проверяет, хеши паролей, если не совпадают
 * то записывает ошибуку в массив ошибок
 *
 * @param $form_pas string хеш пароля, который ввел пользователь
 * @param $user_pas string хеш пароля из базы
 * @param $errors array() массив ошибок
 */
function available_password($form_pas, $user_pas, &$errors)
{
    if (!password_verify($form_pas, $user_pas)) {
        $errors['password'] = 'Неверный пароль';
    }
}

/**
 * Проверяет, если введенное значение меньше или равно, чем текущая цена лота + шаг ставки,
 * то записывает ошибуку в массив ошибок
 *
 * @param $form_cost int введеная значение
 * @param $start_price int начальная цена
 * @param $step_bet int ставка
 * @param $max_bet int максимальная ставка
 * @param $errors array() массив ошибок
 */
function validate_sum_bet($form_cost, $start_price, $step_bet, $max_bet, &$errors)
{
    if(empty($errors['cost'])){
        $current_price = empty($max_bet) ? $start_price : $max_bet;
        if($form_cost < $current_price + $step_bet) {
            $errors['cost'] = 'Значение должно быть больше, чем текущая цена + шаг ставки';
        }
    }
}

/**
 * Добавляет ставку в таблицу ставок
 *
 * @param $arr array() информация о ставке
 * @param $link mysqli Ресурс соединения
 *
 * @return bool TRUE в случае успешного завершения или FALSE в случае ошибки
 */
function create_bet_lot($arr, $link)
{
    $sql = 'INSERT INTO bet (sum_bet, id_user, id_lot) VALUES (?, ?, ?)';
    $stmt = db_get_prepare_stmt($link, $sql, [
        $arr['cost'], $arr['id_user'], $arr['id_lot']
    ]);
    $res = mysqli_stmt_execute($stmt);
    return $res;
}

/**
 * Получает список ставок по лоту
 *
 * @param $value int идентификатор лота
 * @param $link mysqli Ресурс соединения
 *
 * @return array() список ставок
 */
function get_bet_by_lot($value, $link)
{
    $value = intval($value);
    $sql = "SELECT b.*, u.name
            FROM bet b INNER JOIN users u ON b.id_user = u.id_user 
            WHERE id_lot = {$value}
            ORDER BY b.creation_date DESC";
    $res = mysqli_query($link, $sql);
    $res = mysqli_fetch_all($res, MYSQLI_ASSOC);

    return $res;
}

/**
 * Проверят есть ли пользователь в массиве.
 *
 * @param $arr array() массив данных
 * @param $id_user string идентификатор пользователя
 *
 * @return bool Если пользователь найден true, иначе false
 */
function user_is_bet($arr, $id_user)
{
    foreach ($arr as $value)
    {
        if($id_user === $value['id_user']){
            return true;
        }
    }
    return false;
}

/**
 * Преобразует дату и время в "человеческом" формате
 *
 * @param $arr array() Массив данных
 * @param $key string Ключ для поля с датой
 *
 */
function human_timing(&$arr, $key)
{
    foreach ($arr as &$value){
        $time_bet = strtotime($value[$key]);
        $time = time() - $time_bet;
        $time = ($time < 60) ? 60 : $time;
        if ($time < 3600){
            $number_of_units = floor($time / 60);
            $value[$key] = $number_of_units . ' ' . 'минут назад';
        }
        else if ($time < 86400) {
            $number_of_units = floor($time / 3600);
            $value[$key] = $number_of_units . ' ' . 'час назад';
        }
        else{
            $value[$key] = date("d.m.y", $time_bet) . ' в ' . date("H:i", $time_bet);
        }
    }
}

/**
 * Выбирает максимальную ставку по лоту
 *
 * @param $link mysqli Ресурс соединения
 * @param $value int идентификатор лота
 *
 * @return array() Максимальную ставку и id лота
 */
function get_max_bet($link, $value)
{
    $value = mysqli_real_escape_string($link, $value);
    $sql = "SELECT b.id_lot, max(b.sum_bet) as max_bet  FROM bet b WHERE id_lot = {$value} GROUP BY b.id_lot";
    $res = mysqli_query($link, $sql);
    $res = mysqli_fetch_array($res, MYSQLI_ASSOC);
    return $res;
 }

/**
 * Проверят длинну вводимых символов, если больше, то записывает ошибку.
 *
 * @param $str string введеная строка
 * @param $errors array() массив ошибок
 * @param $key string ключ поля, по которому проверяем
 * @param $len int кличество символов
 */
 function validate_str_len($str, &$errors, $key, $len)
{
    if (strlen($str) > $len) {
        $errors[$key] = 'Длинна строки не более ' . $len . ' символов';
    }
}

/**
 * Переводит на страницу с ошибкой, если у пользователя нет прав для доступа
 * к запрашиваемой информации.
 *
 * @param $categories array() список категорий
 */
function print_session_err($categories)
{
    $page_content = include_template('403.php', [
        'categories' => $categories
    ]);

    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'title' => 'Ошибка',
        'is_auth' => 0,
        'user_name' => '',
        'categories' => get_categories()
    ]);
    print($layout_content);
    die();
}

/**
 * Поиск по названию  описанию из таблици лотов.
 *
 * @param $link mysqli Ресурс соединения
 * @param $search string данные из поля поиска
 * @param $page_items int количество лотов на странице
 * @param $offset int указатель с какого момента считывать данные из базы
 *
 * @return array() найденные лоты по запросу
 */
function lots_search($link, $search, $page_items, $offset)
{
    $sql = "SELECT l.id_lot, l.lot_name, l.start_price, l.img_url, l.step_bet, c.category_name, l.end_datetime FROM lot l
            JOIN categories c ON c.id_category = l.id_category
            WHERE MATCH(l.lot_name, l.description) AGAINST(?) AND l.end_datetime > NOW()
            ORDER BY l.creation_date DESC
            LIMIT {$page_items} OFFSET {$offset}";
    $stmt = db_get_prepare_stmt($link, $sql, [$search]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $res = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $res;
}

/**
 * Получает количество лотов по запросу.
 *
 * @param $link mysqli Ресурс соединения
 * @param $search string данные из поля поиска
 *
 * @return int количество лотов
 */
function get_count_lots($link, $search)
{
    $sql = "SELECT COUNT(*) as cnt FROM lot l WHERE MATCH(l.lot_name, l.description) AGAINST(?) AND l.end_datetime > NOW()";
    $stmt = db_get_prepare_stmt($link, $sql, [$search]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $items_count = mysqli_fetch_assoc($result)['cnt'];

    return $items_count;
}

/**
 * Получает список лотов по категории
 *
 * @param $id int идентификатор категории
 * @param $page_items int количество лотов на странице
 * @param $offset int указатель с какого момента считывать данные из базы
 *
 * @return array() массив лотов или ошибка
 */
function get_lot_by_category($id, $page_items, $offset)
{
    $sql = "SELECT l.id_lot, l.lot_name, l.start_price, l.img_url, l.step_bet, c.category_name, l.id_category, l.end_datetime
        FROM lot l LEFT JOIN categories c ON l.id_category = c.id_category
        WHERE l.end_datetime > NOW() AND l.id_category = '%s' ORDER BY l.creation_date DESC 
        LIMIT {$page_items} OFFSET {$offset}";
    $sql = sprintf($sql, $id);

    if ($result = mysqli_query(get_link(), $sql)) {

        $lot = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $lot;
    }
}

/**
 * Получает количество актуальных лотов для категории.
 *
 * @param $link mysqli Ресурс соединения
 * @param $id int идентификатор категории
 *
 * @return int количество лотов
 */
function count_lots_by_category($link, $id)
{
    $sql = "SELECT COUNT(*) as cnt FROM lot l WHERE l.end_datetime > NOW() AND l.id_category = '%s'";
    $sql = sprintf($sql, $id);
    $result = mysqli_query($link, $sql);
    $items_count = mysqli_fetch_assoc($result)['cnt'];

    return $items_count;
}