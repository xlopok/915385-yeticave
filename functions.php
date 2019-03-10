<?php


function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return 'Файл недоступен для чтения';
      
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}


// Функция для прайстега
function price_tag ($number) {
    $ceil_number = ceil($number);
    return number_format( $ceil_number, 0,"." ," ") . " ₽";    
}


// Функция для оставшегося времени лотов до удаления со страницы 

function time_for_lots() {
    date_default_timezone_set("Europe/Moscow");
    
    $tomorrow_midnight = strtotime("tomorrow 24:00"); //полуночь след дня
    
    $current_time = time(); // текущее время 
    
    $secs_to_midnight = $tomorrow_midnight - $current_time ; // останется времени до полуночи след дня
    
    $hours = floor($secs_to_midnight / 3600);
    $minutes = floor(($secs_to_midnight % 3600) / 60);
    
    $formatted_time = $hours . ":" . $minutes;
    return $formatted_time;
}

// Время, которое останется до исчезновения лота
function time_interval ($time_end) {
    $time_now = strtotime('now');
    $time_end = strtotime($time_end);
    $interval = $time_end - $time_now;
    $hours = floor($interval/3600);
    $minutes = ceil(($interval - $hours*3600)/60);
    $time_lots = $hours . ":" . $minutes;
    return $time_lots;
  }

/**
* Проверяет, что переданная дата соответствует формату ДД.ММ.ГГГГ
* @param string $date строка с датой
* @return bool
*/
function check_date_format($date) {
   $result = false;
   $regexp = '/(\d{2})\.(\d{2})\.(\d{4})/m';
   if (preg_match($regexp, $date, $parts) && count($parts) == 4) {
       $result = checkdate($parts[2], $parts[1], $parts[3]);
   }
   return $result;
}


// Функция подключения страницы с ошибкой

function show_error(&$content, $error) {
    $page_content = include_template('404.php', ['error' => $error]);
}

// Функция для БД - чтение категорий 

function get_catagories($link) {
    $categories_rows = [];

    $sql_categories = "SELECT * FROM categories";

    $result_categories = mysqli_query($link, $sql_categories);

    $categories_rows = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);
    return $categories_rows;
}

// Функция для БД - чтение лотов

function get_lots($link) {
    $lots_rows = [];

    $sql_lots = "SELECT l.id ,l.name as lot_name, starting_price, img, c.name as category_name
    FROM lots l
    JOIN  categories c
    ON category_id = c.id
    -- откинул цену из таблицы bets
    WHERE winner_user_id IS NULL
    ORDER BY l.dt_add DESC";

    $result_lots = mysqli_query($link, $sql_lots);

    $lots_rows = mysqli_fetch_all($result_lots, MYSQLI_ASSOC);
    return $lots_rows;
}

function get_lot($link, $lot_id) {
    $show_lot = [];

    $sql_lot = "SELECT l.id, l.name AS lot_name, l.description, l.img, l.starting_price, l.bet_step, l.dt_end, c.name AS category 
    FROM lots l 
    JOIN categories c
    ON l.category_id = c.id
    WHERE l.id ='$lot_id';";

    $result_lot = mysqli_query($link, $sql_lot);

    $show_lot = mysqli_fetch_assoc($result_lot);

    return $show_lot;
}

function add_lot ($link, $lot) {
    $sql = 'INSERT INTO
    lots (
       dt_add,
     name, 
       description,
       img,
       starting_price, 
       dt_end,
       bet_step,
       author_id, 
       category_id) 
   VALUES (
       NOW(),?, ?, ?, ?, ?, ?, ?, ?)';

   $stmt = db_get_prepare_stmt($link, $sql, 
   [
       $lot['lot-name'],
       $lot['message'], 
       $lot['lot-photo'], 
       $lot['lot-rate'],
       $lot['lot-date'], 
       $lot['lot-step'],
       $_SESSION['user']['id'],
       $lot['category']
   ]);
   $res = mysqli_stmt_execute($stmt);
   if ($res) {
    $lot_id = mysqli_insert_id($link);

    header("Location: lot.php?lot_id=" . $lot_id);
}

    else {
        $page_content = include_template('404.php', 
        ['error' => 'Такого лота нет'] );
    }
}


// ФУНКЦИЯ НА СТРАНИЦЕ ФОРМЫ - РЕГИСТРАЦИИ ЮЗЕРА, ДОБАВЛЯЕТ ДАННЫЕ ПОЛЬЗОВАТЕЛЯ

function add_user ($link, $reg_form) {
    $password = password_hash($reg_form['password'], PASSWORD_DEFAULT);

    $sql = 'INSERT INTO users (
        registration_date,
        email,
        user_name,
        password, 
        avatar, 
        contacts) VALUES (NOW(), ?, ?, ?, ?, ?)';
    $stmt = db_get_prepare_stmt($link, $sql, [
        $reg_form['email'],
        $reg_form['name'],
        $password, 
        $reg_form['avatar'],
        $reg_form['message'] ]);
        $res = mysqli_stmt_execute($stmt);
        return $res;     
}

function unique_email_give_id($link, $reg_form, $errors) {
    $email = mysqli_real_escape_string($link, $reg_form['email']);
    $sql = "SELECT id FROM users WHERE email = '$email'";
    $res = mysqli_query($link, $sql);
  
    if (mysqli_num_rows($res) > 0) { 
        $errors['email'] = 'Пользователь с этим email уже зарегистрирован';    
    }

    return $errors;
}


function unique_email_give_all($link, $login_form, $errors) {
    $email = mysqli_real_escape_string($link, $login_form['email']);
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $res = mysqli_query($link, $sql);
  
    return $res;
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = null;

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);
    }

    return $stmt;
}

